<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), interest-cohort=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if ($request->isSecure() && ! app()->hasDebugModeEnabled()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($request->is('admin*') && $request->user()) {
            $response->setCache([
                'no_store' => true,
                'no_cache' => true,
                'must_revalidate' => true,
                'private' => true,
            ]);
        }

        $response->headers->set('Content-Security-Policy', $this->buildCsp($request));

        return $response;
    }

    private function buildCsp(Request $request): string
    {
        $devSources = '';
        if (file_exists(public_path('hot'))) {
            $devSources = ' http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*';
        }

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://challenges.cloudflare.com".$devSources,
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com".$devSources,
            "font-src 'self' data: https://fonts.gstatic.com".$devSources,
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https://challenges.cloudflare.com https://api.open-meteo.com".$devSources,
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com https://www.tiktok.com https://challenges.cloudflare.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "media-src 'self' blob:",
        ]);
    }
}
