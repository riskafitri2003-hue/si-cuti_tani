<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\SaldoCuti;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(JenisCutiSeeder::class);

        // Contoh pegawai sesuai formulir pada gambar
        $pegawai = Pegawai::create([
            'nama' => 'drh. Yulia Suci Rahmadani',
            'nip' => '198605102011012001',
            'jabatan' => 'Medik Veteriner Ahli Muda',
            'unit_kerja' => 'Dinas Pertanian dan Pangan',
            'masa_kerja' => '14 tahun 3 bulan',
            'alamat' => 'Painan Pesisir Selatan',
            'no_telpon' => '081213093232',
        ]);

        SaldoCuti::insert([
            ['nip' => $pegawai->nip, 'jenis_cuti' => 1, 'saldo_n2' => 0, 'saldo_n1' => 6, 'saldo_n' => 12, 'keterangan_n2' => '-', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Akun-akun default
        User::create([
            'nama' => 'Administrator',
            'nip' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'nama' => 'drh. Yulia Suci Rahmadani',
            'nip' => '198605102011012001',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'nip' => $pegawai->nip,
        ]);

        User::create([
            'nama' => 'User Baru',
            'nip' => '123456789',
            'password' => Hash::make('220703'),
            'role' => 'admin',
        ]);
    }
}
