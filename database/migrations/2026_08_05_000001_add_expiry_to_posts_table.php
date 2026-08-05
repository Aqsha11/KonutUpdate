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
            $table->timestamp('headline_expires_at')->nullable()->after('is_headline');
            $table->timestamp('breaking_expires_at')->nullable()->after('is_headline');
        });

        DB::table('posts')
            ->where('is_headline', true)
            ->whereNull('headline_expires_at')
            ->update(['headline_expires_at' => now()->addDays(7)]);

        DB::table('posts')
            ->where('is_breaking', true)
            ->whereNull('breaking_expires_at')
            ->update(['breaking_expires_at' => now()->addDays(7)]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['headline_expires_at', 'breaking_expires_at']);
        });
    }
};
