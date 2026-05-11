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
        Schema::create('cash_out', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_cashout_id');
            $table->unsignedBigInteger('sppg_id');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('jenis_cashout_id')->references('id')->on('jenis_cashout')->onDelete('restrict');
            $table->foreign('sppg_id')->references('id')->on('sppg')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_out');
    }
};

