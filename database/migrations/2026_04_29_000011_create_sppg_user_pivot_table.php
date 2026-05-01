<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppg_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppg_id')->constrained('sppg')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['sppg_id', 'user_id'], 'sppg_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppg_user');
    }
};
