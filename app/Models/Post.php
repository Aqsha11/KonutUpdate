<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    public const FEATURED_EXPIRE_DAYS = 14;

    protected $fillable = [
        'user_id',
        'author_name',
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
        'rejection_reason',
        'is_breaking',
        'is_featured',
        'is_headline',
        'headline_expires_at',
        'breaking_expires_at',
        'published_at',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_headline' => 'boolean',
            'headline_expires_at' => 'datetime',
            'breaking_expires_at' => 'datetime',
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

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeHeadline(Builder $query): Builder
    {
        return $query->where('is_headline', true)
            ->where(fn (Builder $q) => $q->whereNull('headline_expires_at')->orWhere('headline_expires_at', '>', now()));
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)
            ->where('published_at', '>=', now()->subDays(self::FEATURED_EXPIRE_DAYS));
    }

    public function scopeExcludeHeadline(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('is_headline', '!=', true)
                ->orWhere(fn (Builder $q2) => $q2->whereNotNull('headline_expires_at')->where('headline_expires_at', '<=', now()));
        });
    }

    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true)
            ->where(fn (Builder $q) => $q->whereNull('breaking_expires_at')->orWhere('breaking_expires_at', '>', now()));
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

    public function isBreakingActive(): bool
    {
        return $this->is_breaking
            && (is_null($this->breaking_expires_at) || $this->breaking_expires_at->isFuture());
    }

    public function getAuthorNameAttribute(): string
    {
        return ($this->attributes['author_name'] ?? null) ?: ($this->author?->name ?: 'Redaksi');
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

        if (preg_match('/tiktok\.com\/(?:@[\w.\-]+\/video\/|v\/|embed\/)(\d+)/', $url, $m)) {
            return 'https://www.tiktok.com/embed/v2/'.$m[1].'?lang=en-US';
        }

        return $url;
    }

    public function getIsTikTokAttribute(): bool
    {
        $url = $this->video_url;

        return $url !== null && str_contains($url, 'tiktok.com');
    }

    /**
     * Batasi jumlah headline aktif. Headline terlama (kedaluwarsa paling dekat)
     * otomatis dicabut flag-nya saat kuota penuh dan ada headline baru.
     */
    public static function enforceHeadlineLimit(int $keepId, int $max = 6): void
    {
        $activeIds = static::query()
            ->where('is_headline', true)
            ->where(fn (Builder $q) => $q->whereNull('headline_expires_at')->orWhere('headline_expires_at', '>', now()))
            ->orderByRaw('COALESCE(headline_expires_at, created_at) DESC')
            ->pluck('id');

        if ($activeIds->count() <= $max) {
            return;
        }

        $kept = $activeIds->take($max)->values();
        if (! $kept->contains($keepId)) {
            // Pastikan post yang baru ditandai tetap headline: buang yang terlama dari daftar simpan
            $dropped = $kept->pop();
            $kept->push($keepId);
        } else {
            $dropped = null;
        }

        $revoked = $activeIds->diff($kept)->values();
        if ($dropped !== null) {
            $revoked = $revoked->push($dropped)->unique()->values();
        }

        if ($revoked->isNotEmpty()) {
            static::whereIn('id', $revoked)->update([
                'is_headline' => false,
                'headline_expires_at' => null,
            ]);
        }
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
