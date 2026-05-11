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
        Schema::create('distribusi_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu');
            $table->dateTime('tanggal_pengiriman');
            $table->dateTime('tanggal_diterima')->nullable();
            $table->string('foto_menu')->nullable();
            $table->string('foto_suhu')->nullable();
            $table->integer('jumlah')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_menu')->references('id')->on('menu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_menu');
    }
};
