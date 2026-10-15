<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($throttleKey)
                ]),
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard.render'));
        }

        RateLimiter::hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        if (config('app.allow_registration') === false) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.render'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }
}
