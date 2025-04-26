<?php

namespace App\Concerns;

use App\PemeliharaanKendaraan;
use Illuminate\Database\Eloquent\Builder;

trait HasTahunAktif
{
    public static function bootHasTahunAktif()
    {
        static::creating(function ($model) {
            $model->tahun = $model->tahun ?: PemeliharaanKendaraan::tahunAktif();
        });
    }

    public function scopeTahunAktif(Builder $query)
    {
        $table = $this->getTable();
        $tahun = PemeliharaanKendaraan::tahunAktif();
        $query->where("{$table}.tahun", $tahun);
    }
}
