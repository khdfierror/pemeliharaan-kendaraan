<?php

namespace App\Models;

use App\Concerns\HasTahunAktif;
use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kendaraan extends Model
{
    use HasUlids, HasTahunAktif;

    protected $table = 'kendaraan';

    protected $fillable = [
        'merk_id',
        'tahun',
        'nomor_plat',
        'jumlah_roda',
        'tahun_produksi',
        'nama',
        'keterangan',
    ];

    public function perawatan(): HasMany
    {
        return $this->hasMany(Perawatan::class);
    }

    public function merk(): BelongsTo
    {
        return $this->belongsTo(Merk::class);
    }
}
