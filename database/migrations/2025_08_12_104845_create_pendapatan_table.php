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
        Schema::create('pendapatan', function (Blueprint $table) {
            $table->id('id_pendapatan');
            $table->string('no_pendapatan');
            $table->date('tanggal');
            $table->text('deskripsi')->nullable();
            $table->decimal('total', 10, 2);
            $table->foreignId('id_unit')->constrained('unit', 'id_unit')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendapatan');
    }
};