<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function perawatan(): HasMany
    {
        return $this->hasMany(Perawatan::class);
    }
}
