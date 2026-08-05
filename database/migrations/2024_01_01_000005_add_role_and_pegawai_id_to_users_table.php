<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // admin: kelola data pegawai & master
            // pegawai: mengajukan cuti
            // atasan: memberi pertimbangan (bagian VII)
            // pejabat: keputusan akhir (bagian VIII)
            $table->enum('role', ['admin', 'pegawai', 'atasan', 'pejabat'])->default('pegawai')->after('email');
            $table->foreignId('pegawai_id')->nullable()->after('role')
                ->constrained('pegawais')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pegawai_id');
            $table->dropColumn('role');
        });
    }
};
