<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionTimeout
{
    protected int $idleMinutes = 5;

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $lastActivity = $request->session()->get('admin_last_activity');

        if ($lastActivity && $lastActivity->lte(now()->subMinutes($this->idleMinutes))) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Sesi berakhir karena tidak ada aktivitas.'], 401);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Sesi Anda berakhir otomatis karena tidak ada aktivitas selama '.$this->idleMinutes.' menit.',
            ]);
        }

        $request->session()->put('admin_last_activity', now());

        return $next($request);
    }
}
