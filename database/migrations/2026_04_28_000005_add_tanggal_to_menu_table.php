<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('menu', 'tanggal')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->date('tanggal')->nullable()->after('id');
            });

            DB::statement("UPDATE menu SET tanggal = DATE(created_at) WHERE tanggal IS NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('menu', 'tanggal')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->dropColumn('tanggal');
            });
        }
    }
};