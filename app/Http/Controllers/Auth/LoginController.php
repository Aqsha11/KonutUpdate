<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Turnstile;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'cf-turnstile-response' => ['required', 'string', new Turnstile],
        ], [
            'cf-turnstile-response.required' => 'Verifikasi bukan robot wajib diselesaikan.',
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $seconds = app(RateLimiter::class)->availableIn($this->throttleKey($request));

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik.",
            ])->onlyInput('email');
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user && $user->role?->slug !== 'super_admin' && ! $user->hasVerifiedEmail()) {
            return back()->withErrors([
                'email' => 'Email Anda belum diverifikasi. Silakan verifikasi terlebih dahulu melalui link yang dikirim ke email Anda.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            app(RateLimiter::class)->clear($this->throttleKey($request));

            $user = Auth::user();
            $role = $user->role?->slug;

            if ($role === 'kontributor') {
                if ($redirect = $this->safeRedirect($request->input('redirect'))) {
                    return redirect($redirect);
                }

                return redirect()->intended(route('kontributor.dashboard'));
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        app(RateLimiter::class)->hit($this->throttleKey($request), 60);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function hasTooManyLoginAttempts(Request $request): bool
    {
        return app(RateLimiter::class)->tooManyAttempts($this->throttleKey($request), 5);
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }

    private function safeRedirect(?string $redirect): ?string
    {
        if (! $redirect) {
            return null;
        }

        $host = (string) parse_url($redirect, PHP_URL_HOST);

        return $host === request()->getHost() ? $redirect : null;
    }
}
