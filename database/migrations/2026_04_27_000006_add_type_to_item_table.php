<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('item', 'type')) {
            Schema::table('item', function (Blueprint $table) {
                $table->enum('type', ['single', 'menu'])->default('single')->after('nama');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('item', 'type')) {
            Schema::table('item', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};

