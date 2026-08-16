<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')
            ->whereNotIn('status', ['draft', 'pending', 'published', 'rejected'])
            ->update(['status' => 'draft']);

        Schema::table('posts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published'])->default('draft')->change();
        });
    }
};
