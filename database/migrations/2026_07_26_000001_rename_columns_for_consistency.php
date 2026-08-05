<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Already applied in previous partial run
        // 1. saldo_cutis: rename pegawai_id -> nip → DONE
        // 2. saldo_cutis: rename jenis_cuti -> kode_jenis_cuti → DONE

        // 3. users: rename name -> nama
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nama');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nama', 'name');
        });
    }
};
