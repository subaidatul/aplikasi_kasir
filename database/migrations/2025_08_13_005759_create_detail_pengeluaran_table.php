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
        Schema::create('detail_pengeluaran', function (Blueprint $table) {
            $table->id('id_detail_pengeluaran');
            $table->foreignId('id_pengeluaran')->constrained('pengeluaran', 'id_pengeluaran')->onDelete('cascade');
            $table->string('nama_keperluan', 255);
            $table->integer('jumlah');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengeluaran');
    }
};