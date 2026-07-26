<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'kecamatan_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'thumbnail',
        'type',
        'video_path',
        'status',
        'is_breaking',
        'is_headline',
        'published_at',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_breaking' => 'boolean',
            'is_headline' => 'boolean',
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'status' => 'string',
        ];
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeHeadline(Builder $query): Builder
    {
        return $query->where('is_headline', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_headline', true);
    }

    public function scopeExcludeHeadline(Builder $query): Builder
    {
        return $query->where('is_headline', '!=', true);
    }

    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }

    public function scopePopular(Builder $query, int $minViews = 100): Builder
    {
        return $query->where('views_count', '>=', $minViews);
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    // ── Relationships ───────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_categories')
            ->withTimestamps();
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag')
            ->withTimestamps();
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function likesCount(): int
    {
        return $this->likes_count ?? $this->likes()->count();
    }

    public function commentsCount(): int
    {
        return $this->comments_count ?? $this->comments()->approved()->count();
    }

    public function isLikedBy(?string $ip): bool
    {
        if (! $ip) {
            return false;
        }

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('ip_address', $ip);
        }

        return $this->likes()->where('ip_address', $ip)->exists();
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function getVideoUrlAttribute(): ?string
    {
        if (! $this->video_path) {
            return null;
        }

        if (str_starts_with($this->video_path, 'http')) {
            return $this->video_path;
        }

        return Storage::disk('public')->url($this->video_path);
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        $url = $this->video_url;

        if (! $url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        if (preg_match('/(?:vimeo\.com\/)([0-9]+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return $url;
    }

    public function getVideoPosterAttribute(): ?string
    {
        if ($this->thumbnail) {
            return Storage::disk('public')->url($this->thumbnail);
        }

        $url = $this->video_url;

        if (! $url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://img.youtube.com/vi/'.$m[1].'/maxresdefault.jpg';
        }

        return null;
    }
}
