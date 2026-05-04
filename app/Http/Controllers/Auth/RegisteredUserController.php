<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Services\SmsService;
use App\Services\MailService;

class RegisteredUserController extends Controller
{
    protected $sms;
    protected $mailService;

    public function __construct(SmsService $sms, MailService $mailService)
    {
        $this->sms = $sms;
        $this->mailService = $mailService;
    }
    /**
     * Display the registration view.
     */
    public function create(): RedirectResponse
    {
        return redirect('/?auth=register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        if ($request->has('otp')) {
            // Verify OTP Step
            $request->validate([
                'login' => 'required|string',
                'otp' => 'required|string',
            ]);

            $login = $request->login;
            $cached = \Illuminate\Support\Facades\Cache::get('reg_otp_' . $login);

            if (!$cached || $cached['otp'] !== $request->otp) {
                throw ValidationException::withMessages([
                    'otp' => 'Invalid or expired OTP.',
                ]);
            }

            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            $user = new User();
            $user->{$field} = $login;
            $user->password = $cached['password'];
            $user->name = explode('@', $login)[0];
            $user->email_verified_at = $field === 'email' ? now() : null;
            $user->phone_verified_at = $field === 'phone' ? now() : null;
            $user->ai_credit = (int) \App\Models\AiSetting::getValue('free_ai_credit_limit', 100);
            $user->save();

            event(new Registered($user));
            Auth::login($user);
            \Illuminate\Support\Facades\Cache::forget('reg_otp_' . $login);

            // Send branded welcome email
            if ($user->email) {
                $this->mailService->sendWelcome($user);
            }

            // Redirect back to the page the user was on
            $intendedUrl = $request->input('intended_url');

            if ($intendedUrl && str_starts_with($intendedUrl, '/')) {
                return redirect()->to($intendedUrl);
            }

            return to_route('dashboard');
        }

        // Initiate Registration Step
        $request->validate([
            'login' => 'required|string|max:255',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (User::query()->where($field, '=', $login)->exists()) {
            throw ValidationException::withMessages([
                'login' => 'This account already exists.',
            ]);
        }

        $otp = (string) rand(100000, 999999);
        \Illuminate\Support\Facades\Cache::put('reg_otp_' . $login, [
            'otp' => $otp,
            'password' => Hash::make($request->password),
        ], now()->addMinutes(10));

        // Send OTP via Email if applicable
        if ($field === 'email') {
            try {
                $tempUser = new User(['name' => explode('@', $login)[0], 'email' => $login]);
                $this->mailService->sendRegisterOtp($tempUser, $otp);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send OTP to {$login}: " . $e->getMessage());
            }
        } elseif ($field === 'phone') {
            $this->sms->sendOtp($login, $otp);
        }

        return response()->json(['requires_otp' => true]);
    }

    /**
     * Resend the OTP code.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $login = $request->login;
        $cached = \Illuminate\Support\Facades\Cache::get('reg_otp_' . $login);

        if (!$cached) {
            return response()->json([
                'message' => 'Session expired. Please register again.',
            ], 422);
        }

        $otp = (string) rand(100000, 999999);
        $cached['otp'] = $otp;
        \Illuminate\Support\Facades\Cache::put('reg_otp_' . $login, $cached, now()->addMinutes(10));

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if ($field === 'email') {
            try {
                $tempUser = new User(['name' => explode('@', $login)[0], 'email' => $login]);
                $this->mailService->sendRegisterOtp($tempUser, $otp);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to resend OTP to {$login}: " . $e->getMessage());
            }
        } elseif ($field === 'phone') {
            $this->sms->sendOtp($login, $otp);
        }

        return response()->json(['message' => 'OTP resent successfully.']);
    }
}
