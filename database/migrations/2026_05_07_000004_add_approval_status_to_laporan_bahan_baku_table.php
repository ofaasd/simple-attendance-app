<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_bahan_baku', function (Blueprint $table) {
            $table->unsignedTinyInteger('approval_status')
                ->default(0)
                ->after('estimasi_tanggal_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_bahan_baku', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};
