<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    private HtmlSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = new HtmlSanitizer;
    }

    public function test_removes_disallowed_tags(): void
    {
        $this->assertSame('alert(1)hello', $this->sanitizer->sanitize('<script>alert(1)</script>hello'));
        $this->assertSame('hello <p>world</p>', $this->sanitizer->sanitize('<iframe src="x"></iframe>hello <p>world</p>'));
    }

    public function test_removes_event_handler_attributes(): void
    {
        $this->assertSame('<img src="/x.png" alt="x">', $this->sanitizer->sanitize('<img src="/x.png" onerror="alert(1)" alt="x">'));
        $this->assertSame('<p>text</p>', $this->sanitizer->sanitize('<p onclick=alert(1)>text</p>'));
    }

    public function test_neutralizes_javascript_href(): void
    {
        $this->assertSame('<a href="#">x</a>', $this->sanitizer->sanitize('<a href="javascript:alert(1)">x</a>'));
        $this->assertSame('<a href="#">x</a>', $this->sanitizer->sanitize('<a href="java&#x73;cript:alert(1)">x</a>'));
        $this->assertSame('<a href="#">x</a>', $this->sanitizer->sanitize("<a href=\"java\nscript:alert(1)\">x</a>"));
    }

    public function test_blocks_dangerous_protocols_in_href_and_src(): void
    {
        $this->assertSame('<a href="#">x</a>', $this->sanitizer->sanitize('<a href="data:text/html,evil">x</a>'));
        $this->assertSame('<img src="">', $this->sanitizer->sanitize('<img src="data:image/svg+xml,x">'));
        $this->assertSame('<a href="https://example.com">x</a>', $this->sanitizer->sanitize('<a href="https://example.com">x</a>'));
        $this->assertSame('<a href="mailto:redaksi@example.com">x</a>', $this->sanitizer->sanitize('<a href="mailto:redaksi@example.com">x</a>'));
    }

    public function test_allows_relative_urls(): void
    {
        $this->assertSame('<a href="/berita/x">x</a>', $this->sanitizer->sanitize('<a href="/berita/x">x</a>'));
        $this->assertSame('<img src="/uploads/x.webp" alt="x">', $this->sanitizer->sanitize('<img src="/uploads/x.webp" alt="x">'));
    }

    public function test_forces_rel_noopener_on_blank_target(): void
    {
        $this->assertSame(
            '<a href="https://example.com" target="_blank" rel="noopener noreferrer">x</a>',
            $this->sanitizer->sanitize('<a href="https://example.com" target="_blank">x</a>')
        );
    }
}
