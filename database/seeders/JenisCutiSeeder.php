<?php

namespace Database\Seeders;

use App\Models\JenisCuti;
use Illuminate\Database\Seeder;

class JenisCutiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 1, 'nama' => 'Cuti Tahunan'],
            ['kode' => 2, 'nama' => 'Cuti Besar'],
            ['kode' => 3, 'nama' => 'Cuti Sakit'],
            ['kode' => 4, 'nama' => 'Cuti Melahirkan'],
            ['kode' => 5, 'nama' => 'Cuti Karena Alasan Penting'],
            ['kode' => 6, 'nama' => 'Cuti Di Luar Tanggungan Negara'],
            ['kode' => 7, 'nama' => 'Cuti Haji/Umroh'],
        ];

        foreach ($data as $row) {
            JenisCuti::updateOrCreate(['kode' => $row['kode']], $row);
        }
    }
}
