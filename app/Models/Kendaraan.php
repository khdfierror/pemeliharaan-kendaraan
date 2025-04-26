<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasUlids;

    protected $table = 'kendaraan';

    protected $fillable = [
        'nomor_plat',
        'jumlah_roda',
        'tahun',
        'merek',
        'nama',
    ];
}
