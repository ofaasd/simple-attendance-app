<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_order')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('item')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendor')->onDelete('cascade');
            $table->decimal('qty', 18, 2);
            $table->decimal('harga', 18, 2);
            $table->decimal('subtotal', 18, 2);
            $table->timestamps();

            $table->index(['purchase_order_id', 'item_id'], 'purchase_order_detail_po_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_detail');
    }
};

