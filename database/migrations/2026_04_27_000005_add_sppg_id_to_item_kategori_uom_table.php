<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->foreignId('sppg_id')->nullable()->after('id')->constrained('sppg')->nullOnDelete();
        });

        Schema::table('uom', function (Blueprint $table) {
            $table->foreignId('sppg_id')->nullable()->after('id')->constrained('sppg')->nullOnDelete();
        });

        Schema::table('item', function (Blueprint $table) {
            $table->foreignId('sppg_id')->nullable()->after('id')->constrained('sppg')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('item', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sppg_id');
        });

        Schema::table('uom', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sppg_id');
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sppg_id');
        });
    }
};

