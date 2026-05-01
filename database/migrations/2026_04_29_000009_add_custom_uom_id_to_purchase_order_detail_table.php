<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->foreignId('custom_uom_id')
                ->nullable()
                ->after('custom_item_name')
                ->constrained('uom')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->dropForeign(['custom_uom_id']);
            $table->dropColumn('custom_uom_id');
        });
    }
};
