<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
        } elseif (! empty($this->author_name) || $this->author) {
            $author = [
                'id' => $this->author->id ?? null,
                'name' => $this->author_name ?? ($this->author->name ?? null),
                'slug' => $this->author->slug ?? null,
                'avatar' => isset($this->author->avatar_url) ? $this->author->avatar_url : (isset($this->author->avatar) && $this->author->avatar ? asset('storage/avatars/'.$this->author->avatar) : null),
                'profile_url' => isset($this->author->slug) && $this->author->slug ? url('/penulis/'.$this->author->slug) : null,
            ];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'thumbnail' => $thumbnail,
            'image_alt' => $this->image_alt,
            'type' => $this->type,
            'video_path' => $this->video_path,
            'video_url' => method_exists($this->resource, 'getVideoUrlAttribute') ? $this->video_url : $this->video_path,
            'video_embed_url' => method_exists($this->resource, 'getVideoEmbedUrlAttribute') ? $this->video_embed_url : null,
            'video_poster' => method_exists($this->resource, 'getVideoPosterAttribute') ? $this->video_poster : $thumbnail,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'content_updated_at' => optional($this->content_updated_at)->toIso8601String(),
            'url' => url($this->slug),
            'author' => $author,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'kecamatan' => $this->whenLoaded('kecamatan') && $this->kecamatan ? [
                'id' => $this->kecamatan->id,
                'name' => $this->kecamatan->name,
                'slug' => $this->kecamatan->slug,
            ] : null,
            'tags' => $this->whenLoaded('tags') ? $this->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]) : [],
            'views_count' => (int) ($this->views_count ?? 0),
            'likes_count' => isset($this->likes_count) ? (int) $this->likes_count : 0,
            'comments_count' => isset($this->comments_count) ? (int) $this->comments_count : 0,
            'is_breaking' => (bool) $this->is_breaking
                && (! $this->breaking_expires_at || $this->breaking_expires_at->isFuture()),
            'is_headline' => (bool) $this->is_headline
                && (! $this->headline_expires_at || $this->headline_expires_at->isFuture()),
            'is_featured' => $this->isFeaturedActive(),
            'related' => $this->relationLoaded('related')
                ? PostSummaryResource::collection($this->related)->resolve()
                : [],
        ];
    }
}
