<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetAdminCommand extends Command
{
    protected $signature = 'admin:reset {--nip=admin : NIP yang dipakai untuk login admin} {--password=password : Password baru admin}';

    protected $description = 'Buat ulang/reset akun admin untuk pemulihan akses (password otomatis di-hash)';

    public function handle(): int
    {
        $nip = $this->option('nip');
        $password = $this->option('password');

        $user = User::updateOrCreate(
            ['nip' => $nip],
            ['nama' => 'Administrator', 'password' => $password, 'role' => 'admin']
        );

        $this->info('Akun admin berhasil dibuat/di-reset.');
        $this->warn("NIP      : {$user->nip}");
        $this->warn("Password : {$password}");
        $this->warn("Role     : {$user->role}");

        return self::SUCCESS;
    }
}
