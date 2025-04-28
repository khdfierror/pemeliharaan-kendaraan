<?php

namespace App\Models;

use App\Concerns\HasTahunAktif;
use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPerawatan extends Model
{
    use HasUlids, HasTahunAktif;

    protected $table = 'detail_perawatan';

    protected $fillable = [
        'tahun',
        'perawatan_id',
        'jenis_perawatan_id',
        'uraian',
        'volume',
        'harga_satuan',
        'total',
        'masa_pakai',
        'habis_masa_pakai',
        'km_awal',
        'km_akhir',
        'catatan',
    ];

    protected $casts = [
        'habis_masa_pakai' => 'date',
    ];

    public function jenisPerawatan(): BelongsTo
    {
        return $this->belongsTo(JenisPerawatan::class);
    }

    public function perawatan(): BelongsTo
    {
        return $this->belongsTo(Perawatan::class);
    }
}
