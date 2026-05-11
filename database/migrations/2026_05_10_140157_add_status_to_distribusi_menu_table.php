<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('distribusi_menu', function (Blueprint $table) {
            $table->enum('status', ['on progress', 'on delivery', 'done'])->default('on progress')->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusi_menu', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

