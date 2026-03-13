<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        // Assign default 'user' role
        $user->assignRole('user');

        // Create default subscription preferences
        UserSubscription::create([
            'user_id' => $user->id,
            'email_price_alerts' => true,
            'email_daily_report' => true,
            'email_weekly_report' => true,
            'email_market_analysis' => true,
            'push_price_alerts' => true,
            'push_daily_report' => false,
            'push_major_events' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với Giá Vàng Hôm Nay.');
    }
}
