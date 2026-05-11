<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('detail_menu')) {
            Schema::create('detail_menu', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_menu')->constrained('menu')->onDelete('cascade');
                $table->string('nama_detail_menu', 255);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_menu');
    }
};
