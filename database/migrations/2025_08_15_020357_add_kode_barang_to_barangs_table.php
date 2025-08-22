<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('kode_barang')->unique()->after('nama_barang');
        });
    }

    /**
     * Batalkan migrasi dengan menghapus kolom.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('kode_barang');
        });
    }
};