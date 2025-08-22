<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'detail_pengeluaran';
    protected $primaryKey = 'id_detail_pengeluaran';
    
    // Ganti guarded dengan fillable untuk keamanan yang lebih baik
    protected $fillable = [
        'id_pengeluaran',
        'nama_keperluan', 
        'jumlah',
        'total',
    ];

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran', 'id_pengeluaran');
    }
}