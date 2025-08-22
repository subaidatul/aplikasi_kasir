<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    protected $guarded = ['id_pengeluaran'];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    // Ganti nama metode relasi dari 'detailPengeluaran' menjadi 'details'
    public function details()
    {
        return $this->hasMany(DetailPengeluaran::class, 'id_pengeluaran', 'id_pengeluaran');
    }
}