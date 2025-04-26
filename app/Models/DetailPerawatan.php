<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPerawatan extends Model
{
    use HasUlids;

    protected $table = 'detail_perawatan';

    protected $fillable = [
        'perawatan_id',
        'jenis_perawatan_id',
        'jumlah',
        'harga_satuan',
        'total',
        'masa_pakai',
        'km_awal',
        'km_akhir',
        'catatan',
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
