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
        Schema::create('distribusi_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_distribusi');
            $table->unsignedBigInteger('id_penerima_manfaat');
            $table->timestamps();

            $table->foreign('id_distribusi')->references('id')->on('distribusi_menu')->onDelete('cascade');
            $table->foreign('id_penerima_manfaat')->references('id')->on('penerima_manfaat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_detail');
    }
};
