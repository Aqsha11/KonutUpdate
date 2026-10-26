<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $avatar = null;
        if (method_exists($this->resource, 'getAvatarUrlAttribute')) {
            $avatar = $this->avatar_url ?? null;
        } elseif (! empty($this->avatar)) {
            $avatar = asset('storage/avatars/'.$this->avatar);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'avatar' => $avatar,
            'profile_url' => $this->slug ? url('/penulis/'.$this->slug) : null,
        ];
    }
}
