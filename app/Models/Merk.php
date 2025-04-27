<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Merk extends Model
{
    use HasUlids;

    protected $table = 'merk';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class);
    }
}
