<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->decimal('qty_diterima', 18, 2)->nullable()->after('qty');
            $table->decimal('harga_realisasi', 18, 2)->nullable()->after('harga');
            $table->decimal('subtotal_realisasi', 18, 2)->nullable()->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->dropColumn(['qty_diterima', 'harga_realisasi', 'subtotal_realisasi']);
        });
    }
};
