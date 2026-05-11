<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->after('total_bayar')
                ->index('purchase_order_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropIndex('purchase_order_status_idx');
            $table->dropColumn('status');
        });
    }
};

