<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->string('custom_item_name')->nullable()->after('item_id');
            $table->string('custom_item_satuan')->nullable()->after('custom_item_name');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->dropColumn(['custom_item_satuan', 'custom_item_name']);
        });
    }
};
