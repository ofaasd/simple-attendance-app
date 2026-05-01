<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu') || !Schema::hasColumn('menu', 'kategori_id')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE menu ALTER COLUMN kategori_id DROP NOT NULL');
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `menu` MODIFY `kategori_id` BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu') || !Schema::hasColumn('menu', 'kategori_id')) {
            return;
        }

        if (DB::table('menu')->whereNull('kategori_id')->exists()) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE menu ALTER COLUMN kategori_id SET NOT NULL');
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `menu` MODIFY `kategori_id` BIGINT UNSIGNED NOT NULL');
        }
    }
};
