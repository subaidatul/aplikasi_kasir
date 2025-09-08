<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_unit',
        'nama_barang',
        'kode_barang',
        'satuan',
        'harga_beli',
        'harga_jual',
        'deskripsi',
        'status',
        'kategori_produk',
        'gambar'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    public function detailPendapatan()
    {
        return $this->hasMany(DetailPendapatan::class, 'id_barang', 'id_barang');
    }

    /**
     * Relasi ke model Stok.
     * Menggunakan nama berbeda agar tidak bentrok dengan kolom 'stok'.
     */
    public function riwayatStok()
    {
        return $this->hasMany(Stok::class, 'id_barang', 'id_barang');
    }
}