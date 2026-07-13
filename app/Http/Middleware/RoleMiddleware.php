<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->role) {
            abort(403, 'Unauthorized action.');
        }

        foreach ($roles as $role) {
            if ($user->role->slug === $role) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
