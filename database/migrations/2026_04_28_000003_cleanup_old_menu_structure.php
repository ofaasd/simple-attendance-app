<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('item', 'type')) {
            Schema::table('item', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasTable('item_bom')) {
            Schema::drop('item_bom');
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('item', 'type')) {
            Schema::table('item', function (Blueprint $table) {
                $table->enum('type', ['single', 'menu'])->default('single')->after('nama');
            });
        }

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
};
