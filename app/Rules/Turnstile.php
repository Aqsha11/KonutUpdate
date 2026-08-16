<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(TurnstileService::class)->verify((string) $value, request()->ip())) {
            $fail('Verifikasi captcha gagal. Silakan coba lagi.');
        }
    }
}
