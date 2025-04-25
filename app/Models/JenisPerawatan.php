<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class JenisPerawatan extends Model
{
    use HasUlids;

    protected $table = 'jenis_perawatan';

    protected $fillable = [
        'kode',
        'nama',
    ];
}
