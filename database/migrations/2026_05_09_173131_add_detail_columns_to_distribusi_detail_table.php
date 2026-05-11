<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('distribusi_detail', function (Blueprint $table) {
            $table->integer('jml_kecil')->default(0)->after('id_penerima_manfaat');
            $table->integer('jml_besar')->default(0)->after('jml_kecil');
            $table->integer('jml_orcil')->default(0)->after('jml_besar');
            $table->integer('jml_orbes_sekolah')->default(0)->after('jml_orcil');
            $table->integer('jml_bumil')->default(0)->after('jml_orbes_sekolah');
            $table->integer('jml_busui')->default(0)->after('jml_bumil');
            $table->integer('jml_balita')->default(0)->after('jml_busui');
            $table->integer('jml_orbes_b3')->default(0)->after('jml_balita');
            $table->integer('jml_lainnya')->default(0)->after('jml_orbes_b3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusi_detail', function (Blueprint $table) {
            $table->dropColumn([
                'jml_kecil', 'jml_besar', 'jml_orcil', 'jml_orbes_sekolah',
                'jml_bumil', 'jml_busui', 'jml_balita', 'jml_orbes_b3', 'jml_lainnya'
            ]);
        });
    }
};
