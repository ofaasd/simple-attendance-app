<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu')) {
            Schema::create('menu', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sppg_id')->constrained('sppg')->onDelete('cascade');
                $table->string('nama');
                $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
                $table->foreignId('uom_id')->nullable()->constrained('uom')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes('deleted_at', precision: 0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
