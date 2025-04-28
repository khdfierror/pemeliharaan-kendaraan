<?php

namespace App\Models;

use App\Concerns\HasTahunAktif;
use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
        'masa_pakai' => 'integer',
    ];

    public function jenisPerawatan(): BelongsTo
    {
        return $this->belongsTo(JenisPerawatan::class);
    }

    public function perawatan(): BelongsTo
    {
        return $this->belongsTo(Perawatan::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            if ($detail->perawatan && $detail->masa_pakai) {
                $tanggalNota = $detail->perawatan->tanggal_nota ?? now();
                $detail->habis_masa_pakai = Carbon::parse($tanggalNota)->addMonths((int) $detail->masa_pakai);
            }
        });

        static::updating(function ($detail) {
            if ($detail->perawatan && $detail->masa_pakai) {
                $tanggalNota = $detail->perawatan->tanggal_nota ?? now();
                $detail->habis_masa_pakai = Carbon::parse($tanggalNota)->addMonths((int) $detail->masa_pakai);
            }
        });
    }
}
