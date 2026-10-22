<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Doctor extends Command
{
    protected $signature = 'konut:doctor';

    protected $description = 'Cek konfigurasi deployment: widget Tanya Berita AI, Turnstile, aset Vite, cache, dan database.';

    public function handle(): int
    {
        $errors = 0;

        $this->line('== Konut Update: Doctor ==');
        $this->line('Environment   : '.app()->environment());
        $this->line('Commit        : '.trim((string) $this->gitCommit()));
        $this->line('Config cache  : '.(app()->configurationIsCached() ? 'AKTIF (ubah .env -> php artisan config:clear)' : 'nonaktif'));
        $this->line('');

        $errors += $this->checkGemini();
        $errors += $this->checkTurnstile();
        $errors += $this->checkAssets();
        $errors += $this->checkStorage();
        $errors += $this->checkDatabase();

        $this->line('');
        $this->line($errors > 0
            ? "=> {$errors} masalah ditemukan (lihat tanda [ERR] di atas)."
            : '=> Semua pemeriksaan lolos.');

        return self::SUCCESS;
    }

    protected function checkGemini(): int
    {
        $enabled = (bool) config('services.gemini.enabled');
        $key = (string) config('services.gemini.key');

        if (! $enabled) {
            $this->error('[ERR] GEMINI_ENABLED = false -> widget "Tanya Berita" disembunyikan.');

            return 1;
        }

        if (trim($key) === '') {
            $this->error('[ERR] GEMINI_API_KEY kosong -> widget "Tanya Berita" disembunyikan. Isi di .env server / dashboard Render, lalu php artisan config:clear.');

            return 1;
        }

        $this->info('[OK]  Tanya Berita AI aktif (key '.$this->mask($key).', model '.config('services.gemini.model').').');

        return 0;
    }

    protected function checkTurnstile(): int
    {
        if (! config('services.turnstile.enabled')) {
            $this->warn('[WARN] TURNSTILE_ENABLED = false -> captcha dilewati (jangan di production).');

            return 0;
        }

        $site = (string) config('services.turnstile.site_key');
        $secret = (string) config('services.turnstile.secret_key');

        if (trim($site) === '') {
            $this->error('[ERR] TURNSTILE_SITE_KEY kosong -> widget captcha tidak dirender.');

            return 1;
        }

        if (trim($secret) === '') {
            $this->error('[ERR] TURNSTILE_SECRET_KEY kosong -> login/registrasi GAGAL (verifikasi fail-closed).');

            return 1;
        }

        $this->info('[OK]  Turnstile aktif (site '.$this->mask($site).').');

        return 0;
    }

    protected function checkAssets(): int
    {
        $manifestPath = public_path('build/manifest.json');

        if (! File::exists($manifestPath)) {
            $this->error('[ERR] public/build/manifest.json tidak ada -> jalankan `npm ci && npm run build` di server.');

            return 1;
        }

        $manifest = json_decode((string) File::get($manifestPath), true);
        $css = $this->findInBuild('ku-hub-fab');
        $js = $this->findInBuild('ku_hub_teaser_seen');

        if (! $css) {
            $this->error('[ERR] Aset CSS belum memuat .ku-hub-fab -> widget AI tidak akan tampil. Jalankan `npm ci && npm run build`.');

            return 1;
        }

        if (! $js) {
            $this->error('[ERR] Aset JS belum memuat komponen floatingHub -> tombol tidak akan merespons. Jalankan `npm ci && npm run build`.');

            return 1;
        }

        $this->info('[OK]  Aset Vite terbangun & memuat widget AI (manifest '.count(is_array($manifest) ? $manifest : []).' entri).');

        return 0;
    }

    protected function checkStorage(): int
    {
        if (! is_link(public_path('storage')) && ! File::exists(public_path('storage'))) {
            $this->error('[ERR] public/storage tidak ada -> jalankan `php artisan storage:link` (thumbnail & upload tidak tampil).');

            return 1;
        }

        $this->info('[OK]  storage:link tersedia.');

        return 0;
    }

    protected function checkDatabase(): int
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $this->error('[ERR] Database tidak bisa diakses: '.$e->getMessage());

            return 1;
        }

        $this->info('[OK]  Database terhubung ('.config('database.default').').');

        return 0;
    }

    protected function findInBuild(string $needle): bool
    {
        $assets = public_path('build/assets');

        if (! File::isDirectory($assets)) {
            return false;
        }

        foreach (File::files($assets) as $file) {
            if (str_contains((string) File::get($file->getPathname()), $needle)) {
                return true;
            }
        }

        return false;
    }

    protected function gitCommit(): string
    {
        $head = trim((string) @file_get_contents(base_path('.git/HEAD')));

        if ($head === '' || ! str_starts_with($head, 'ref:')) {
            return $head;
        }

        $ref = trim(substr($head, 4));
        $sha = @file_get_contents(base_path('.git/'.$ref));

        if ($sha === false) {
            $packed = @file_get_contents(base_path('.git/packed-refs'));

            if ($packed !== false && preg_match('/^([0-9a-f]{40})\s'.preg_quote($ref, '/').'$/m', $packed, $m)) {
                return substr($m[1], 0, 7);
            }

            return $ref;
        }

        return substr(trim($sha), 0, 7);
    }

    protected function mask(string $value): string
    {
        $value = trim($value);

        if (strlen($value) <= 10) {
            return $value;
        }

        return substr($value, 0, 6).'…'.substr($value, -4);
    }
}
