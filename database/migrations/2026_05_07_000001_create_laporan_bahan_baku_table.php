<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('item_id')->constrained('item')->cascadeOnDelete();
            $table->decimal('volume', 18, 2);
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('total', 18, 2);
            $table->decimal('total_pembayaran', 18, 2);
            $table->foreignId('vendor_id')->constrained('vendor')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['vendor_id', 'tanggal'], 'laporan_bahan_baku_vendor_tanggal_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_bahan_baku');
    }
};
