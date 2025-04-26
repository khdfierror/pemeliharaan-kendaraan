<?php

namespace App;

use Illuminate\Support\Facades\Session;

class PemeliharaanKendaraan
{
    public static function tahunAktif()
    {
        return Session::get('tahun-aktif', date('Y'));
    }
}
