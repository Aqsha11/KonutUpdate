<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeFeedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resource;

        $headlineMain = isset($data['headlineMain']) && $data['headlineMain'] ? new PostSummaryResource($data['headlineMain']) : null;
        $headlineSide = PostSummaryResource::collection($data['headlineSide'] ?? collect());

        return [
            'stories' => $data['stories'] ?? [],
            'headline' => [
                'main' => $headlineMain,
                'side' => $headlineSide,
            ],
            'latest' => [
                'data' => PostSummaryResource::collection($data['latest'] ?? collect()),
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => ($data['latest'] ?? collect())->count(),
                    'total' => ($data['latest'] ?? collect())->count(),
                ],
            ],
            'trending' => PostSummaryResource::collection($data['trending'] ?? collect()),
            'featured' => PostSummaryResource::collection($data['featured'] ?? collect()),
            'videos' => VideoItemResource::collection($data['videos'] ?? collect()),
            'opini' => PostSummaryResource::collection($data['opini'] ?? collect()),
            'categories' => CategoryResource::collection($data['categories'] ?? collect()),
            'tags' => $data['tags'] ?? [],
            'kecamatan' => $data['kecamatan'] ?? [],
        ];
    }
}
