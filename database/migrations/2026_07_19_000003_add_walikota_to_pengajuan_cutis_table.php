<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->string('nama_walikota')->nullable()->after('status_kadin');
            $table->string('nip_walikota')->nullable()->after('nama_walikota');
            $table->date('tanggal_walikota')->nullable()->after('nip_walikota');
            $table->string('status_walikota', 50)->default('pending')->after('tanggal_walikota');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropColumn(['nama_walikota', 'nip_walikota', 'tanggal_walikota', 'status_walikota']);
        });
    }
};
