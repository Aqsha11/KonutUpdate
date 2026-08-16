<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_rate_limited_after_ten_attempts(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('login'), [
                'email' => 'budi@example.com',
                'password' => 'salah-password',
                'captcha' => '1',
            ]);
        }

        $this->post(route('login'), [
            'email' => 'budi@example.com',
            'password' => 'salah-password',
            'captcha' => '1',
        ])->assertTooManyRequests();
    }

    public function test_register_is_rate_limited_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('register'), [
                'name' => 'Budi Pembaca',
                'email' => "budi{$i}@example.com",
                'password' => 'Rahasia123',
                'password_confirmation' => 'Rahasia123',
            ]);
        }

        $this->post(route('register'), [
            'name' => 'Budi Pembaca',
            'email' => 'budi-akhir@example.com',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])->assertTooManyRequests();
    }

    public function test_login_page_itself_is_not_rate_limited(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $this->get(route('login'))->assertOk();
        }
    }
}
