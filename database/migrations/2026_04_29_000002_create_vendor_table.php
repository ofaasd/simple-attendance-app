<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor', function (Blueprint $table) {
            $table->id();
            $table->string('kode_vendor', 50)->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_telp', 50);
            $table->string('email')->nullable();
            $table->string('pic_nama')->nullable();
            $table->string('pic_jabatan')->nullable();
            $table->string('pic_no_telp', 50)->nullable();
            $table->string('termin_pembayaran', 100)->nullable();
            $table->string('metode_pengiriman', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};

