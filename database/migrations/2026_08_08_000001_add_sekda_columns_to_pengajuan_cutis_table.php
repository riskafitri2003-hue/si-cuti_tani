<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            // Sekretaris Daerah
            $table->string('nama_sekda')->nullable()->after('status_kepala_dinas');
            $table->string('nip_sekda')->nullable()->after('nama_sekda');
            $table->date('tanggal_sekda')->nullable()->after('nip_sekda');
            $table->string('status_sekda', 50)->default('pending')->after('tanggal_sekda');
            $table->string('tanda_tangan_sekda')->nullable()->after('status_sekda');
            $table->string('tanda_tangan_walikota')->nullable()->after('status_walikota');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropColumn([
                'nama_sekda', 'nip_sekda', 'tanggal_sekda', 'status_sekda', 'tanda_tangan_sekda',
                'tanda_tangan_walikota',
            ]);
        });
    }
};
