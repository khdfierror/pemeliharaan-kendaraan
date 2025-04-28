<?php

namespace App\Exports;

use App\Models\Kendaraan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JumlahKendaraanExport implements FromArray, WithHeadings
{
    protected $jumlahRoda;

    public function __construct($jumlahRoda)
    {
        $this->jumlahRoda = $jumlahRoda;
    }

    public function array(): array
    {
        $data = [];

        $kendaraans = Kendaraan::where('jumlah_roda', $this->jumlahRoda)->get();

        foreach ($kendaraans as $index => $kendaraan) {
            $data[] = [
                'No.' => $index + 1,
                'Nomor Plat' => $kendaraan->nomor_plat,
                'Merk' => $kendaraan->merk->nama,
                'Nama' => $kendaraan->nama,
                'Tahun' => $kendaraan->tahun,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nomor Plat',
            'Merk',
            'Nama',
            'Tahun',
        ];
    }
}
