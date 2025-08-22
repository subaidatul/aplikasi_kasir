<?php

// app/Models/Unit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    // Tambahkan baris ini
    protected $table = 'unit';
    protected $primaryKey = 'id_unit';
    public $timestamps = false; // Jika tabel 'unit' tidak memiliki kolom timestamps

    protected $fillable = [
        'nama_unit',
        'keterangan',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_unit', 'id_unit');
    }
}