<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor', 'kode_vendor')) {
                $table->string('kode_vendor', 50)->nullable()->after('id');
            }
            if (!Schema::hasColumn('vendor', 'pic_nama')) {
                $table->string('pic_nama')->nullable()->after('email');
            }
            if (!Schema::hasColumn('vendor', 'pic_jabatan')) {
                $table->string('pic_jabatan')->nullable()->after('pic_nama');
            }
            if (!Schema::hasColumn('vendor', 'pic_no_telp')) {
                $table->string('pic_no_telp', 50)->nullable()->after('pic_jabatan');
            }
            if (!Schema::hasColumn('vendor', 'termin_pembayaran')) {
                $table->string('termin_pembayaran', 100)->nullable()->after('pic_no_telp');
            }
            if (!Schema::hasColumn('vendor', 'metode_pengiriman')) {
                $table->string('metode_pengiriman', 100)->nullable()->after('termin_pembayaran');
            }
            if (!Schema::hasColumn('vendor', 'catatan')) {
                $table->text('catatan')->nullable()->after('metode_pengiriman');
            }
        });

        if (Schema::hasColumn('vendor', 'kode_vendor')) {
            Schema::table('vendor', function (Blueprint $table) {
                $table->unique('kode_vendor', 'vendor_kode_vendor_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('vendor', function (Blueprint $table) {
            if (Schema::hasColumn('vendor', 'kode_vendor')) {
                $table->dropUnique('vendor_kode_vendor_unique');
            }
            if (Schema::hasColumn('vendor', 'catatan')) {
                $table->dropColumn('catatan');
            }
            if (Schema::hasColumn('vendor', 'metode_pengiriman')) {
                $table->dropColumn('metode_pengiriman');
            }
            if (Schema::hasColumn('vendor', 'termin_pembayaran')) {
                $table->dropColumn('termin_pembayaran');
            }
            if (Schema::hasColumn('vendor', 'pic_no_telp')) {
                $table->dropColumn('pic_no_telp');
            }
            if (Schema::hasColumn('vendor', 'pic_jabatan')) {
                $table->dropColumn('pic_jabatan');
            }
            if (Schema::hasColumn('vendor', 'pic_nama')) {
                $table->dropColumn('pic_nama');
            }
            if (Schema::hasColumn('vendor', 'kode_vendor')) {
                $table->dropColumn('kode_vendor');
            }
        });
    }
};

