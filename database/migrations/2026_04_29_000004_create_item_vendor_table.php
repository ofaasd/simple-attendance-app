<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppg_id')->constrained('sppg')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('item')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendor')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('harga', 18, 2);
            $table->unsignedInteger('rank')->default(1);
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);

            $table->index(['item_id', 'vendor_id', 'tanggal'], 'item_vendor_item_vendor_tanggal_idx');
            $table->index(['item_id', 'tanggal'], 'item_vendor_item_tanggal_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_vendor');
    }
};
