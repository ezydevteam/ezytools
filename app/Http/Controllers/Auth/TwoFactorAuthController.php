<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends Controller
{
    /**
     * Generate 2FA secret and return setup info.
     */
    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_confirmed_at) {
            return back()->with('error', 'Two-factor authentication is already enabled.');
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user->two_factor_secret = $secret;
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrCodeUrl);

        return back()->with('two_factor_setup', [
            'secret' => $secret,
            'qr_code' => $svg,
        ]);
    }

    /**
     * Confirm 2FA setup by verifying the first code.
     */
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $user = $request->user();
        
        if (!$user->two_factor_secret) {
            return back()->with('error', 'Two-factor authentication is not currently being configured.');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            $user->two_factor_confirmed_at = now();
            // Generate 8 recovery codes
            $recoveryCodes = collect(range(1, 8))->map(function () {
                return Str::random(10).'-'.Str::random(10);
            })->toArray();
            
            $user->two_factor_recovery_codes = json_encode($recoveryCodes);
            $user->save();

            return back()->with([
                'success' => 'Two-factor authentication has been enabled successfully.',
                'recovery_codes' => $recoveryCodes,
            ]);
        }

        throw ValidationException::withMessages([
            'code' => 'The provided two-factor authentication code was invalid.',
        ]);
    }

    /**
     * Disable 2FA for the user.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'This password does not match our records.',
            ]);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show the 2FA login challenge view.
     */
    public function showChallenge(Request $request)
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * Verify the 2FA code during login.
     */
    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        if (!$request->code && !$request->recovery_code) {
            throw ValidationException::withMessages(['code' => 'Please provide an authentication code.']);
        }

        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('login.id'));

        if (!$user) {
            return redirect()->route('login');
        }

        $valid = false;

        if ($request->code) {
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);
        } elseif ($request->recovery_code) {
            $codes = json_decode($user->two_factor_recovery_codes, true) ?? [];
            $index = array_search($request->recovery_code, $codes);
            
            if ($index !== false) {
                unset($codes[$index]);
                $user->two_factor_recovery_codes = json_encode(array_values($codes));
                $user->save();
                $valid = true;
            }
        }

        if ($valid) {
            Auth::login($user, $request->session()->get('login.remember', false));
            
            $intendedUrl = $request->session()->get('login.intended_url');
            $request->session()->forget(['login.id', 'login.remember', 'login.intended_url']);
            $request->session()->regenerate();

            if ($intendedUrl && str_starts_with($intendedUrl, '/')) {
                return redirect()->to($intendedUrl);
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        throw ValidationException::withMessages([
            'code' => 'The provided two-factor authentication code was invalid.',
        ]);
    }
}
