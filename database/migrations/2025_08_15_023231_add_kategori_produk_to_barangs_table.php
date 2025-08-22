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
        Schema::table('barang', function (Blueprint $table) {
            // Mengubah tipe data kolom 'kode_barang' menjadi string (VARCHAR)
            $table->string('kode_barang', 255)->change();

            // Menambahkan kolom 'kategori_produk' setelah 'nama_barang'
            $table->string('kategori_produk')->after('nama_barang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Mengembalikan tipe data kolom 'kode_barang' ke semula (jika tidak tahu, biarkan kosong atau sesuaikan)
            // Contoh: $table->bigInteger('kode_barang')->change();

            // Menghapus kolom 'kategori_produk'
            $table->dropColumn('kategori_produk');
        });
    }
};