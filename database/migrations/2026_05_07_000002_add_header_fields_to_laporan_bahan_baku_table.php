<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_bahan_baku', function (Blueprint $table) {
            $table->string('nama_laporan')->nullable()->after('id');
            $table->date('tanggal_laporan')->nullable()->after('nama_laporan');
            $table->date('estimasi_tanggal_bayar')->nullable()->after('tanggal_laporan');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['nama_laporan', 'tanggal_laporan', 'estimasi_tanggal_bayar']);
        });
    }
};

