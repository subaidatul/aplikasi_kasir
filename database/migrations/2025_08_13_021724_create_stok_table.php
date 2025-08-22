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
        Schema::create('stok', function (Blueprint $table) {
            $table->id('id_stok');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->onDelete('cascade');
            $table->foreignId('id_unit')->constrained('unit', 'id_unit')->onDelete('cascade'); // Menambahkan kolom 'id_unit'
            $table->string('no_transaksi'); // No. Pendapatan atau No. Pengeluaran
            $table->date('tanggal');
            $table->string('keterangan'); // Contoh: 'Pembelian' atau 'Penjualan'
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);
            $table->integer('sisa_stok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};