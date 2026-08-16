<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TurnstileService
{
    public function verify(string $token, ?string $remoteIp = null): bool
    {
        $secret = config('services.turnstile.secret_key');

        if (! $secret || $token === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array_filter([
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]));
        } catch (Throwable $e) {
            Log::error('Turnstile siteverify gagal terhubung: '.$e->getMessage());

            return false;
        }

        if (! $response->successful()) {
            Log::error('Turnstile siteverify non-2xx', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        $data = $response->json();

        if (! data_get($data, 'success')) {
            Log::error('Turnstile verifikasi gagal', [
                'error-codes' => data_get($data, 'error-codes'),
            ]);

            return false;
        }

        return true;
    }
}
