<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $thumbnail = null;
        if (! empty($this->thumbnail)) {
            $thumbnail = str_starts_with($this->thumbnail, 'http')
                ? $this->thumbnail
                : asset('storage/'.$this->thumbnail);
        }

        $author = null;
        if ($this->relationLoaded('author') && $this->author) {
            $author = new UserSummaryResource($this->author);
        } elseif (! empty($this->author_name)) {
            $author = [
                'name' => $this->author_name,
                'slug' => $this->author?->slug,
                'profile_url' => $this->author?->slug ? url('/penulis/'.$this->author->slug) : null,
            ];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'thumbnail' => $thumbnail,
            'image_alt' => $this->image_alt,
            'type' => $this->type,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'url' => url($this->slug),
            'author' => $author,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'kecamatan' => $this->whenLoaded('kecamatan') && $this->kecamatan ? [
                'id' => $this->kecamatan->id,
                'name' => $this->kecamatan->name,
                'slug' => $this->kecamatan->slug,
            ] : null,
            'views_count' => (int) ($this->views_count ?? 0),
            'likes_count' => isset($this->likes_count) ? (int) $this->likes_count : ($this->likes()->count() ?? 0),
            'comments_count' => isset($this->comments_count) ? (int) $this->comments_count : ($this->comments()->count() ?? 0),
            'is_breaking' => (bool) $this->is_breaking
                && (! $this->breaking_expires_at || $this->breaking_expires_at->isFuture()),
            'is_headline' => (bool) $this->is_headline
                && (! $this->headline_expires_at || $this->headline_expires_at->isFuture()),
            'is_featured' => $this->isFeaturedActive(),
        ];
    }
}
