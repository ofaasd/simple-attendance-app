<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('item_bom')) {
            Schema::create('item_bom', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('item')->onDelete('cascade');
                $table->foreignId('component_item_id')->constrained('item')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['item_id', 'component_item_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('item_bom');
    }
};

