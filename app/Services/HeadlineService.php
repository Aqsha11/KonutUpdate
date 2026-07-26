<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;

class HeadlineService
{
    public function __construct(
        protected PostRepository $postRepository,
    ) {}

    public function getHeadlinePosts(int $limit = 5): Collection
    {
        return $this->postRepository->getHeadlinePosts($limit);
    }

    public function getHeadlineIds(): array
    {
        return $this->postRepository->getHeadlineIds();
    }
}
