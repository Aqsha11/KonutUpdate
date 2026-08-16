<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'cf-turnstile-response' => ['required', 'string', new Turnstile],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'cf-turnstile-response.required' => 'Verifikasi bukan robot wajib diselesaikan.',
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Link reset password telah dikirim ke email Anda. Silakan periksa inbox (termasuk folder Spam).');
    }
}
