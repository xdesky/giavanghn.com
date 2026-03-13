<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from social provider
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['social' => 'Không thể đăng nhập bằng ' . $provider . '. Vui lòng thử lại.']);
        }

        // Check if social account exists
        $socialAccount = SocialAccount::where('provider', $provider)
                                      ->where('provider_id', $socialUser->getId())
                                      ->first();

        if ($socialAccount) {
            // Login existing user
            Auth::login($socialAccount->user);
            $socialAccount->user->update(['last_login_at' => now()]);

            return redirect('/dashboard');
        }

        // Check if user with email already exists
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Link social account to existing user
            $this->createSocialAccount($user, $provider, $socialUser);
            Auth::login($user);
            $user->update(['last_login_at' => now()]);

            return redirect('/dashboard')->with('success', 'Tài khoản ' . $provider . ' đã được liên kết thành công!');
        }

        // Create new user
        $user = User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'password' => Hash::make(Str::random(32)), // Random password for social users
            'email_verified_at' => now(), // Auto-verify social users
            'is_active' => true,
        ]);

        // Assign default role
        $user->assignRole('user');

        // Create subscription preferences
        UserSubscription::create([
            'user_id' => $user->id,
            'email_price_alerts' => true,
            'email_daily_report' => true,
            'push_price_alerts' => true,
        ]);

        // Create social account
        $this->createSocialAccount($user, $provider, $socialUser);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với Giá Vàng Hôm Nay.');
    }

    /**
     * Create social account record
     */
    private function createSocialAccount(User $user, string $provider, $socialUser): SocialAccount
    {
        return SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken ?? null,
            'avatar' => $socialUser->getAvatar(),
        ]);
    }

    /**
     * Validate social provider
     */
    private function validateProvider(string $provider): void
    {
        $allowedProviders = ['google', 'facebook', 'apple'];

        if (!in_array($provider, $allowedProviders)) {
            abort(404, 'Provider not supported');
        }
    }
}
