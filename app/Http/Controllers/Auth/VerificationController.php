<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Email Anda berhasil diverifikasi. Akun Anda sudah aktif dan masuk otomatis.');
    }

    public function resend(Request $request)
    {
        if ($request->user()) {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->route('home');
            }

            $request->user()->sendEmailVerificationNotification();

            session(['verification_sent_at' => now()->getTimestamp()]);

            return back()->with('status', 'Link verifikasi baru telah dikirim ke email Anda.');
        }

        $request->validate([
            'email' => 'required|email',
            'cf-turnstile-response' => ['required', 'string', new Turnstile],
        ], [
            'cf-turnstile-response.required' => 'Verifikasi bukan robot wajib diselesaikan.',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (! $user || $user->hasVerifiedEmail()) {
            return back()->with('status', 'Jika email terdaftar dan belum terverifikasi, link verifikasi baru akan dikirim.');
        }

        $user->sendEmailVerificationNotification();

        session(['verification_sent_at' => now()->getTimestamp()]);

        return back()->with('status', 'Link verifikasi baru telah dikirim ke email Anda.');
    }
}
