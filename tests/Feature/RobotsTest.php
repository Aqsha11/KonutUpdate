<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RobotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_declares_both_sitemaps(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();

        $body = $response->getContent();

        $this->assertStringContainsString('User-agent: *', $body);
        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Disallow: /search', $body);
        $this->assertStringContainsString('Sitemap: '.url('sitemap.xml'), $body);
        $this->assertStringContainsString('Sitemap: '.url('sitemap-news.xml'), $body);
    }
}
