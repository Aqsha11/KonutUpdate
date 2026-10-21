<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
     * Halaman statis yang punya URL ramah (tanpa prefix /p/).
     * Key = slug halaman, value = nama route canonical.
     */
    public const FRIENDLY_ROUTES = [
        'tentang-kami' => 'pages.about',
        'pedoman-media-siber' => 'pages.pedoman',
        'privacy-policy' => 'pages.privacy',
        'kontak' => 'pages.kontak',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * URL kanonik halaman: URL ramah bila ada, selain itu /p/{slug}.
     */
    public function canonicalUrl(): string
    {
        if (isset(self::FRIENDLY_ROUTES[$this->slug])) {
            return route(self::FRIENDLY_ROUTES[$this->slug]);
        }

        return route('pages.show', $this->slug);
    }
}
