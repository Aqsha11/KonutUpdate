<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\StrongPassword;
use App\Rules\Turnstile;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(string $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', new StrongPassword],
            'cf-turnstile-response' => ['required', 'string', new Turnstile],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'cf-turnstile-response.required' => 'Verifikasi bukan robot wajib diselesaikan.',
        ]);

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
        }

        return back()->withErrors(['email' => 'Token reset tidak valid atau telah kedaluwarsa. Silakan ulangi proses lupa password.']);
    }
}
