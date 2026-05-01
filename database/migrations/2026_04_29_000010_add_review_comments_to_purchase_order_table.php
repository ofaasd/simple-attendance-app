<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->text('akuntan_comment')->nullable()->after('status');
            $table->text('verval_comment')->nullable()->after('akuntan_comment');
            $table->text('head_comment')->nullable()->after('verval_comment');
            $table->text('last_rejection_comment')->nullable()->after('head_comment');
            $table->string('last_rejected_by_role', 50)->nullable()->after('last_rejection_comment');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropColumn([
                'akuntan_comment',
                'verval_comment',
                'head_comment',
                'last_rejection_comment',
                'last_rejected_by_role',
            ]);
        });
    }
};
