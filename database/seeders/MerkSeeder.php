<?php

namespace Database\Seeders;

use App\Models\Merk;
use Illuminate\Database\Seeder;

use function Laravel\Prompts\confirm;

class MerkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $confirmed = confirm('Truncate Merk ?');

        Merk::truncate($confirmed);

        $this->merk();
    }

    public function merk()
    {
        $merk = [
            [
                'kode' => 'HND',
                'nama' => 'Honda',
            ],
            [
                'kode' => 'SUZ',
                'nama' => 'Suzuki',
            ],
            [
                'kode' => 'TYT',
                'nama' => 'Toyota',
            ],
            [
                'kode' => 'DHS',
                'nama' => 'Daihatsu',
            ],
            [
                'kode' => 'MTS',
                'nama' => 'Mitsubishi',
            ],
            [
                'kode' => 'YMH',
                'nama' => 'Yamaha',
            ],
        ];

        foreach ($merk as $index => $item) {
            $index = Merk::firstOrCreate($item);
            $index->save();
        }
    }
}
