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
        //
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->integer('is_remote');
            $table->integer('user_id');
            $table->date('day');
            $table->integer('start')->default(null);
            $table->string('lat_start')->default(null);
            $table->string('long_start')->default(null);
            $table->string('ip_start')->default(null);
            $table->string('browser_start')->default(null);
            $table->string('isp_start')->default(null);
            $table->string('image_start')->default(null);
            $table->integer('start_late')->default(null);
            $table->integer('end')->default(null);
            $table->string('lat_end')->default(null);
            $table->string('long_end')->default(null);
            $table->string('ip_end')->default(null);
            $table->string('browser_end')->default(null);
            $table->string('isp_end')->default(null);
            $table->string('image_end')->default(null);
            $table->integer('start_marked_by_admin')->default(null); //bernilai 1 jika diabsenkan oleh admin
            $table->integer('end_marked_by_system')->default(null); //bernilai 1 jika absen pulang diabsenkan oleh admin
            $table->integer('overtime')->default(null); //Bernilai 1 jika pegawai absen di hari libur, lihat master hari libur
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('presences');
    }
};
