<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIdUnitFromPengeluaranTableAgain extends Migration
{
    public function up()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            // Hapus batasan foreign key terlebih dahulu
            $table->dropForeign('pengeluaran_id_unit_foreign');

            // Kemudian, hapus kolom id_unit
            $table->dropColumn('id_unit');
        });
    }

    public function down()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            // Tambahkan kembali kolom id_unit jika diperlukan untuk rollback
            $table->unsignedBigInteger('id_unit')->nullable();

            // Tambahkan kembali foreign key constraint
            $table->foreign('id_unit')->references('id_unit')->on('unit');
        });
    }
}
