<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class ExpireFeaturedPosts extends Command
{
    protected $signature = 'posts:expire-featured';

    protected $description = 'Reset is_headline/is_breaking untuk berita yang sudah kadaluarsa (7 hari)';

    public function handle(): int
    {
        $headline = Post::where('is_headline', true)
            ->whereNotNull('headline_expires_at')
            ->where('headline_expires_at', '<=', now())
            ->update([
                'is_headline' => false,
                'headline_expires_at' => null,
            ]);

        $breaking = Post::where('is_breaking', true)
            ->whereNotNull('breaking_expires_at')
            ->where('breaking_expires_at', '<=', now())
            ->update([
                'is_breaking' => false,
                'breaking_expires_at' => null,
            ]);

        $this->info("Headline kadaluarsa di-reset: {$headline} berita");
        $this->info("Breaking kadaluarsa di-reset: {$breaking} berita");

        return self::SUCCESS;
    }
}
