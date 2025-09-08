<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';
    protected $primaryKey = 'id_stok';
    
    // Perbaikan: Ganti $guarded dengan $fillable
    protected $fillable = [
        'id_barang',
        'id_unit',
        'tanggal',
        'keterangan',
        'stok_masuk',
        'stok_keluar',
        'sisa_stok'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }
}