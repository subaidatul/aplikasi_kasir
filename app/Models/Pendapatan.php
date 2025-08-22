<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendapatan extends Model
{
    use HasFactory;

    protected $table = 'pendapatan';
    protected $primaryKey = 'id_pendapatan';
    protected $guarded = ['id_pendapatan'];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    public function detailPendapatan()
    {
        return $this->hasMany(DetailPendapatan::class, 'id_pendapatan', 'id_pendapatan');
    }
}