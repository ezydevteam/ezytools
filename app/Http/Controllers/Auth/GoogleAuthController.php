<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect(Request $request)
    {
        // Validate redirect URL to prevent open redirect attacks
        $request->validate([
            'redirect' => ['nullable', 'string', 'max:500'],
        ]);

        // Store the page the user was on for redirect after callback (only relative paths)
        if ($request->has('redirect')) {
            $redirect = $request->input('redirect');
            if (is_string($redirect) && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//')) {
                session()->put('google_auth_redirect', $redirect);
            }
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/?auth=login')->withErrors(['login' => 'Google Authentication Failed.']);
        }

        $user = User::query()->where('email', '=', $googleUser->getEmail())->first();

        if ($user) {
            // Update google_id and avatar if missing
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                ]);
            }
            Auth::login($user, true);
        } else {
            // Create a new user
            $user = User::create([
                'name' => $googleUser->getName() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'avatar' => $googleUser->getAvatar(),
                'ai_credit' => (int) \App\Models\AiSetting::getValue('free_ai_credit_limit', 100),
            ]);
            Auth::login($user, true);
        }

        $redirectUrl = session()->pull('google_auth_redirect');

        if ($redirectUrl && str_starts_with(parse_url($redirectUrl, PHP_URL_PATH) ?? '', '/')) {
            return redirect()->to($redirectUrl);
        }

        return redirect()->route('dashboard');
    }
}
