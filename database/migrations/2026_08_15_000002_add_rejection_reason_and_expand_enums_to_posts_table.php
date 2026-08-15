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
            $table->text('rejection_reason')->nullable()->after('status');
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_status_check');
            DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_status_check CHECK (status IN ('draft','published','pending','rejected'))");

            DB::statement('ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_type_check');
            DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_type_check CHECK (type IN ('article','video','opini'))");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_status_check');
            DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_status_check CHECK (status IN ('draft','published'))");

            DB::statement('ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_type_check');
            DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_type_check CHECK (type IN ('article','video'))");
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
