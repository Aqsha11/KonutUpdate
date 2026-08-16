<?php

namespace App\Jobs;

use App\Models\PageView;
use App\Models\Post;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class RecordViewJob
{
    use Queueable;

    public function __construct(
        private int $postId,
        private string $ipAddress,
        private ?string $userAgent
    ) {}

    public function handle(): void
    {
        $dedupKey = 'viewed:'.$this->postId.':'.md5($this->ipAddress);

        if (Cache::has($dedupKey)) {
            return;
        }

        Cache::put($dedupKey, true, now()->addMinutes(10));

        $post = Post::find($this->postId);
        if (! $post) {
            return;
        }

        $post->increment('views_count');

        PageView::create([
            'post_id' => $this->postId,
            'ip_address' => $this->anonymizeIp($this->ipAddress),
            'user_agent' => $this->userAgent,
        ]);
    }

    private function anonymizeIp(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[3] = '0';

            return implode('.', $parts);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return substr($ip, 0, strrpos($ip, ':')).':0';
        }

        return $ip;
    }
}
