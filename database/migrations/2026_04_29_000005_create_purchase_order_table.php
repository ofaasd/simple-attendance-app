<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppg_id')->constrained('sppg')->onDelete('cascade');
            $table->string('kode_po', 50)->unique();
            $table->date('tanggal_po');
            $table->date('tanggal_menu_dari');
            $table->date('tanggal_menu_sampai');
            $table->decimal('total_bayar', 18, 2)->default(0);
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);

            $table->index(['sppg_id', 'tanggal_po'], 'purchase_order_sppg_tanggal_po_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
