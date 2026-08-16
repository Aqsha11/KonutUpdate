<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function success()
    {
        return view('auth.verify-success');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['bail', 'required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\p{M}\s\'\-\.\,]+$/u'],
            'email' => ['bail', 'required', 'email:rfc,spoof', 'max:191', Rule::unique('users', 'email')],
            'password' => ['bail', 'required', 'string', 'confirmed', new StrongPassword],
            'cf-turnstile-response' => ['bail', 'required', 'string', new Turnstile],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal :min karakter.',
            'name.max' => 'Nama maksimal :max karakter.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan tanda baca dasar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'cf-turnstile-response.required' => 'Verifikasi bukan robot wajib diselesaikan.',
        ]);

        try {
            $user = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'role_id' => Role::where('slug', 'kontributor')->value('id'),
                ]);

                $user->sendEmailVerificationNotification();

                session(['verification_sent_at' => now()->getTimestamp()]);

                return $user;
            });
        } catch (\Throwable $e) {
            Log::error('Akun batal dibuat, email verifikasi gagal terkirim: '.$e->getMessage());

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Akun tidak berhasil dibuat. Email verifikasi tidak dapat dikirim, silakan coba lagi.']);
        }

        return redirect()->route('register.success');
    }
}
