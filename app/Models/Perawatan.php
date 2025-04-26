<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $casts = [
        'tanggal_nota' => 'date',
    ];

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function detailPerawatan(): HasMany
    {
        return $this->hasMany(DetailPerawatan::class);
    }
}
