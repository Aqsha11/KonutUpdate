<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    public function verify(string $token, ?string $remoteIp = null): bool
    {
        $secret = config('services.turnstile.secret_key');

        if (! $secret || $token === '') {
            return false;
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array_filter([
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $remoteIp,
            ]));

        if (! $response->successful()) {
            return false;
        }

        return (bool) data_get($response->json(), 'success');
    }
}
