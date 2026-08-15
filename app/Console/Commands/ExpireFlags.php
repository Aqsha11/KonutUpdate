<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class ExpireFlags extends Command
{
    protected $signature = 'posts:expire-flags';

    protected $description = 'Reset is_headline (7 hari) dan is_breaking (3 hari) yang sudah kadaluarsa. Konten Pilihan (is_featured) hanya dimatikan manual, tidak terpengaruh command ini.';

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
