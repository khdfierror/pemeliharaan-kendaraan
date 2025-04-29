<?php

namespace App\Models;

use App\Concerns\HasTahunAktif;
use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perawatan extends Model
{
    use HasUlids, HasTahunAktif;

    protected $table = 'perawatan';

    protected $fillable = [
        'tahun',
        'kendaraan_id',
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

    protected static function boot()
    {
        parent::boot();

        static::updated(function (Perawatan $perawatan) {
            if ($perawatan->wasChanged('tanggal_nota')) {
                // Pastikan ambil data fresh
                $details = $perawatan->detailPerawatan()->get();

                foreach ($details as $detail) {
                    if ($detail->masa_pakai) {
                        $detail->habis_masa_pakai = \Carbon\Carbon::parse($perawatan->tanggal_nota)->addMonths((int) $detail->masa_pakai);
                        $detail->saveQuietly();
                    }
                }
            }
        });
    }
}
