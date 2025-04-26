<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tahun extends Model
{
    use HasUlids;

    protected $table = "tahun";

    protected $fillable = [
        'tahun',
        'is_aktif',
        'is_default',
    ];
}
