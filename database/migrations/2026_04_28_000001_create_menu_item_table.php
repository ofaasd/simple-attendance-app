<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu_item')) {
            Schema::create('menu_item', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
                $table->foreignId('item_id')->constrained('item')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['menu_id', 'item_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item');
    }
};

