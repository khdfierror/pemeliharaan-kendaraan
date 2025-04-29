<?php

namespace App\Exports;

use App\Models\DetailPerawatan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PerawatanBulanIniExport implements FromArray, WithHeadings
{
    protected $jumlahRoda;
    protected $jenisPerawatanId;

    public function __construct($jumlahRoda = null, $jenisPerawatanId = null)
    {
        $this->jumlahRoda = $jumlahRoda;
        $this->jenisPerawatanId = $jenisPerawatanId;
    }

    public function array(): array
    {
        $query = DetailPerawatan::whereHas('perawatan.kendaraan');

        if ($this->jumlahRoda) {
            $query->whereHas('perawatan.kendaraan', function ($q) {
                $q->where('jumlah_roda', $this->jumlahRoda);
            });
        }

        if ($this->jenisPerawatanId) {
            $query->where('jenis_perawatan_id', $this->jenisPerawatanId);
        }

        $query->whereMonth('habis_masa_pakai', now()->month)
            ->whereYear('habis_masa_pakai', now()->year);

        $details = $query->with('perawatan.kendaraan', 'jenisPerawatan')->get();

        $data = [];

        foreach ($details as $index => $item) {
            $data[] = [
                $index + 1,
                $item->perawatan->kendaraan->nama ?? '-',
                $item->jenisPerawatan->nama ?? '-',
                $item->habis_masa_pakai?->format('d-m-Y') ?? '-',
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['Data Kendaraan Bermotor Yang Perlu Perawatan Bulan Ini'],
            ['No.', 'Nama Kendaraan', 'Jenis Perawatan', 'Habis Masa Pakai'],
        ];
    }
}
