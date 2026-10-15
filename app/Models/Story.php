<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    use HasFactory;

    public const EXPIRE_DAYS = 7;

    protected $fillable = [
        'title',
        'category_id',
        'image',
        'url',
        'is_active',
        'expires_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->category?->name ?? $this->title;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? Storage::url($this->image)
            : asset('images/no-image.svg');
    }

    public function getRemainingDaysAttribute(): int
    {
        if (! $this->expires_at) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInDays($this->expires_at, false)));
    }
}
