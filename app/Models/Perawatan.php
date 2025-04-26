<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Perawatan extends Model
{
    use HasUlids;

    protected $table = 'perawatan';

    protected $fillable = [
        'kendaraan_id',
        'tahun',
        'nomor_nota',
        'tanggal_nota',
        'keterangan',
    ];
}
