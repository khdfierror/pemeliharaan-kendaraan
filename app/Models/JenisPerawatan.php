<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPerawatan extends Model
{
    use HasUlids;

    protected $table = 'jenis_perawatan';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function detailPerawatan(): HasMany
    {
        return $this->hasMany(DetailPerawatan::class);
    }
}
