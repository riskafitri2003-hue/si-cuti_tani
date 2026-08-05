<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->string('dokumen_pendukung')->nullable()->after('telpon_selama_cuti');
            $table->string('tanda_tangan_pegawai')->nullable()->after('dokumen_pendukung');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropColumn(['dokumen_pendukung', 'tanda_tangan_pegawai']);
        });
    }
};
