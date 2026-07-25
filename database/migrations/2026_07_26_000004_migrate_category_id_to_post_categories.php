<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $posts = DB::table('posts')->whereNotNull('category_id')->select('id', 'category_id')->get();

        foreach ($posts as $post) {
            DB::table('post_categories')->insert([
                'post_id' => $post->id,
                'category_id' => $post->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('post_categories')->truncate();
    }
};
