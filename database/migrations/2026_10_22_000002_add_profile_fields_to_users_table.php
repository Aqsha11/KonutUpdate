<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // URL profil penulis publik: /penulis/{slug}
            $table->string('slug')->nullable()->unique();
            // Profil singkat untuk halaman profil penulis.
            $table->text('bio')->nullable();
        });

        // Backfill slug dari nama (unique); nama duplikat diberi akhiran id.
        DB::table('users')->orderBy('id')->chunkById(100, function ($rows) {
            $used = DB::table('users')->whereNotNull('slug')->pluck('slug')->all();

            foreach ($rows as $row) {
                if ($row->slug) {
                    continue;
                }

                $base = Str::slug($row->name) ?: 'penulis';
                $slug = $base;
                $suffix = 1;

                while (in_array($slug, $used, true)) {
                    $suffix++;
                    $slug = $base.'-'.$suffix;
                }

                $used[] = $slug;

                DB::table('users')->where('id', $row->id)->update(['slug' => $slug]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'bio']);
        });
    }
};
