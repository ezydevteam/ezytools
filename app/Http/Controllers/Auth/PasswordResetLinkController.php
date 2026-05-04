<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Services\SmsService;
use App\Services\MailService;

class PasswordResetLinkController extends Controller
{
    protected $sms;
    protected $mailService;

    public function __construct(SmsService $sms, MailService $mailService)
    {
        $this->sms = $sms;
        $this->mailService = $mailService;
    }
    /**
     * Display the password reset link request view.
     */
    public function create(): RedirectResponse
    {
        return redirect('/?auth=forgot');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $login = $request->email; // We'll rename this to login in frontend later
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $request->validate([
            'email' => "required|string|exists:users,{$field}",
        ]);

        $otp = (string) rand(100000, 999999);
        
        Cache::put('reset_otp_' . $login, [
            'otp' => $otp,
        ], now()->addMinutes(10));

        if ($field === 'email') {
            try {
                $user = User::where('email', $login)->first();
                if ($user) {
                    $this->mailService->sendForgotPasswordOtp($user, $otp);
                }
            } catch (\Exception $e) {
                Log::error("Failed to send reset OTP to {$login}: " . $e->getMessage());
                return response()->json(['message' => 'Failed to send OTP. Please try again later.'], 500);
            }
        } elseif ($field === 'phone') {
            $this->sms->sendOtp($login, $otp);
        }

        return response()->json(['requires_otp' => true]);
    }

    /**
     * Handle the password reset after OTP verification.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = $request->email;
        $cached = Cache::get('reset_otp_' . $email);

        if (!$cached || $cached['otp'] !== $request->otp) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid or expired OTP.',
            ]);
        }

        $user = User::query()->where('email', '=', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget('reset_otp_' . $email);

        return response()->json(['message' => 'Password reset successfully.']);
    }

    /**
     * Resend the reset OTP.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $cached = Cache::get('reset_otp_' . $email);

        if (!$cached) {
            return response()->json(['message' => 'Session expired. Please start over.'], 422);
        }

        $otp = (string) rand(100000, 999999);
        $cached['otp'] = $otp;
        Cache::put('reset_otp_' . $email, $cached, now()->addMinutes(10));

        try {
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->mailService->sendForgotPasswordOtp($user, $otp);
            }
        } catch (\Exception $e) {
            Log::error("Failed to resend reset OTP to {$email}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP.'], 500);
        }

        return response()->json(['message' => 'OTP resent successfully.']);
    }
}
