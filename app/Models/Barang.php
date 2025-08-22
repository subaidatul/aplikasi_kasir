<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    // Gunakan $fillable untuk secara eksplisit mengizinkan mass assignment
    protected $fillable = [
        'id_unit',
        'nama_barang',
        'kode_barang',
        'satuan',
        'stok_awal',
        'stok',
        'harga_beli',
        'harga_jual',
        'deskripsi',
        'status',
        'kategori_produk' 
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    /**
     * Relasi ke model DetailPendapatan.
     * Digunakan untuk memeriksa apakah barang sudah memiliki transaksi.
     */
    public function detailPendapatan()
    {
        return $this->hasMany(DetailPendapatan::class, 'id_barang', 'id_barang');
    }

    /**
     * Relasi ke model Stok.
     * Digunakan untuk menghapus catatan stok terkait barang.
     */
    public function stok()
    {
        return $this->hasMany(Stok::class, 'id_barang', 'id_barang');
    }
}