<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPendapatan extends Model
{
    use HasFactory;

    protected $table = 'detail_pendapatan';
    protected $primaryKey = 'id_detail_pendapatan';
    protected $guarded = ['id_detail_pendapatan'];

    public function pendapatan()
    {
        return $this->belongsTo(Pendapatan::class, 'id_pendapatan', 'id_pendapatan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}