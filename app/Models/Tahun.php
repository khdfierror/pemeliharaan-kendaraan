<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

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
