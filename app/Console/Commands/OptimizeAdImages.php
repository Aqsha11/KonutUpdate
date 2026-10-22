<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Services\AdImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeAdImages extends Command
{
    protected $signature = 'ads:optimize {--force : Simpan perubahan; tanpa opsi ini hanya simulasi}';

    protected $description = 'Optimalkan gambar iklan lama menjadi WebP maksimal 800x1200.';

    public function handle(AdImageService $images): int
    {
        $ads = Ad::query()->whereNotNull('image')->orderBy('id')->get();

        if ($ads->isEmpty()) {
            $this->info('Tidak ada gambar iklan untuk dioptimalkan.');

            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $disk = Storage::disk('public');
        $rows = [];
        $processed = 0;
        $updated = 0;
        $totalBefore = 0;
        $totalAfter = 0;

        foreach ($ads as $ad) {
            if (! $disk->exists($ad->image)) {
                $rows[] = [$ad->id, $ad->title, $ad->image, '-', '-', 'file tidak ditemukan'];
                $this->warn("Iklan #{$ad->id}: file {$ad->image} tidak ditemukan.");

                continue;
            }

            try {
                $before = $disk->size($ad->image);
                $dimensions = @getimagesize($disk->path($ad->image));

                if ($dimensions === false) {
                    $rows[] = [$ad->id, $ad->title, $ad->image, $this->formatBytes($before), '-', 'bukan gambar valid'];
                    $this->warn("Iklan #{$ad->id}: file {$ad->image} bukan gambar yang bisa dibaca.");

                    continue;
                }

                $extension = strtolower(pathinfo($ad->image, PATHINFO_EXTENSION));
                $alreadyOptimized = $extension === 'webp'
                    && $dimensions[0] <= AdImageService::MAX_WIDTH
                    && $dimensions[1] <= AdImageService::MAX_HEIGHT
                    && $before <= 300 * 1024;

                if ($alreadyOptimized) {
                    $totalBefore += $before;
                    $totalAfter += $before;
                    $processed++;
                    $rows[] = [
                        $ad->id,
                        $ad->title,
                        $ad->image,
                        $this->formatBytes($before),
                        $this->formatBytes($before),
                        'sudah optimal',
                    ];

                    continue;
                }

                $optimized = $images->optimized($disk->path($ad->image));
                $after = strlen($optimized['contents']);
                $needsResize = $dimensions[0] > AdImageService::MAX_WIDTH
                    || $dimensions[1] > AdImageService::MAX_HEIGHT;
                $shouldWrite = $needsResize || $extension !== 'webp' || $after < $before;

                $totalBefore += $before;
                $totalAfter += $shouldWrite ? $after : $before;
                $processed++;

                if ($force && $shouldWrite) {
                    $oldPath = $ad->image;
                    $newPath = $images->newPath();
                    $disk->put($newPath, $optimized['contents']);
                    $ad->update(['image' => $newPath]);
                    $disk->delete($oldPath);
                    $updated++;
                }

                $rows[] = [
                    $ad->id,
                    $ad->title,
                    $ad->image,
                    $this->formatBytes($before),
                    $this->formatBytes($shouldWrite ? $after : $before),
                    $shouldWrite
                        ? ($force ? 'diperbarui' : 'akan diperbarui')
                        : 'dilewati (tidak lebih kecil)',
                ];
            } catch (\Throwable $e) {
                $rows[] = [$ad->id, $ad->title, $ad->image, '-', '-', 'gagal: '.$e->getMessage()];
                $this->warn("Iklan #{$ad->id} gagal diproses: {$e->getMessage()}");
            }
        }

        $this->table(['ID', 'Judul', 'File', 'Sebelum', 'Sesudah', 'Status'], $rows);
        $this->info(sprintf(
            '%s: %d iklan diperiksa, %d diperbarui, %s -> %s (hemat %s).',
            $force ? 'Selesai' : 'Simulasi (tambahkan --force untuk menyimpan)',
            $processed,
            $updated,
            $this->formatBytes($totalBefore),
            $this->formatBytes($totalAfter),
            $this->formatBytes(max(0, $totalBefore - $totalAfter)),
        ));

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
