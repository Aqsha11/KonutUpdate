<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen((string) $value) < 8) {
            $fail('Kata sandi minimal 8 karakter.');

            return;
        }

        if (! preg_match('/[A-Z]/', (string) $value)) {
            $fail('Kata sandi harus mengandung minimal satu huruf kapital.');

            return;
        }

        if (! preg_match('/[a-z]/', (string) $value)) {
            $fail('Kata sandi harus mengandung minimal satu huruf kecil.');

            return;
        }

        if (! preg_match('/\d/', (string) $value)) {
            $fail('Kata sandi harus mengandung minimal satu angka.');
        }
    }
}
