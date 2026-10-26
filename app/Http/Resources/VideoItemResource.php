<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $thumbnail = null;
        if (method_exists($this->resource, 'getVideoPosterAttribute')) {
            $thumbnail = $this->video_poster;
        } elseif (! empty($this->thumbnail)) {
            $thumbnail = str_starts_with($this->thumbnail, 'http')
                ? $this->thumbnail
                : asset('storage/'.$this->thumbnail);
        }

        $url = url($this->slug ?? '#');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'thumbnail' => $thumbnail,
            'video_url' => method_exists($this->resource, 'getVideoUrlAttribute') ? $this->video_url : ($this->video_path ?? null),
            'video_embed_url' => method_exists($this->resource, 'getVideoEmbedUrlAttribute') ? $this->video_embed_url : null,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'url' => $url,
            'views_count' => (int) ($this->views_count ?? 0),
            'likes_count' => isset($this->likes_count) ? (int) $this->likes_count : 0,
            'comments_count' => isset($this->comments_count) ? (int) $this->comments_count : 0,
        ];
    }
}
