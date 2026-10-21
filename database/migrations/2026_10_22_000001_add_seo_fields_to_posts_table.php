<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Akun penulis untuk halaman profil /penulis/{slug} + schema Person.
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            // Alt text deskriptif gambar (fallback: judul artikel).
            $table->string('image_alt')->nullable();
            // Sumber dateModified yang jujur: hanya berubah saat isi artikel
            // benar-benar diubah (bukan saat flag admin di-toggle ulang).
            $table->timestamp('content_updated_at')->nullable();
            // Penanda artikel duplikat -> otomatis noindex + tak masuk sitemap.
            $table->foreignId('duplicate_of_id')->nullable()->constrained('posts')->nullOnDelete();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index('author_id');
            $table->index('duplicate_of_id');
        });

        // Backfill: için "terakhir diubah" terdekat yang tersedia adalah
        // updated_at, dijepit agar tidak lebih tua dari published_at.
        DB::table('posts')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                $updated = $row->updated_at ?: $row->published_at;

                if ($updated && $row->published_at && $updated < $row->published_at) {
                    $updated = $row->published_at;
                }

                if ($updated) {
                    DB::table('posts')->where('id', $row->id)->update([
                        'content_updated_at' => $updated,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['author_id']);
            $table->dropIndex(['duplicate_of_id']);
            $table->dropConstrainedForeignId('author_id');
            $table->dropConstrainedForeignId('duplicate_of_id');
            $table->dropColumn(['image_alt', 'content_updated_at']);
        });
    }
};
