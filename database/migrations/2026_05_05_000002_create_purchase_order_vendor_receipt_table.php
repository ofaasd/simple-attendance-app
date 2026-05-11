<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_vendor_receipt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_order')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendor')->onDelete('cascade');
            $table->string('nota_path');
            $table->timestamps();

            $table->unique(['purchase_order_id', 'vendor_id'], 'po_vendor_receipt_po_vendor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_vendor_receipt');
    }
};

