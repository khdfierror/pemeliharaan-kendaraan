<?php

namespace Database\Seeders;

use App\Models\JenisPerawatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\confirm;

class JenisPerawatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $confirmed = confirm('Truncate Jenis ?');

        JenisPerawatan::truncate($confirmed);

        $this->jenisPerawatan();
    }

    public function jenisPerawatan()
    {
        $jenisPerawatan = [
            [
                'kode' => 'GOM',
                'nama' => 'Ganti Oli Mesin',
            ],
            [
                'kode' => 'GOT',
                'nama' => 'Ganti Oli Transmisi',
            ],
            [
                'kode' => 'GBL',
                'nama' => 'Ganti Ban Luar',
            ],
            [
                'kode' => 'GBD',
                'nama' => 'Ganti Ban Dalam',
            ],
        ];

        foreach ($jenisPerawatan as $index => $item) {
            $index = JenisPerawatan::firstOrCreate($item);
            $index->save();
        }
    }
}
