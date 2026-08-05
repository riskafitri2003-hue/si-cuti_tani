<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename kolom pada tabel pengajuan_cutis
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->renameColumn('kabid_user_id', 'atasan_langsung_user_id');
            $table->renameColumn('kabid_disetujui_hari', 'atasan_langsung_disetujui_hari');
            $table->renameColumn('kabid_perubahan_hari', 'atasan_langsung_perubahan_hari');
            $table->renameColumn('kabid_ditangguhkan_hari', 'atasan_langsung_ditangguhkan_hari');
            $table->renameColumn('kabid_tidak_disetujui_hari', 'atasan_langsung_tidak_disetujui_hari');
            $table->renameColumn('nama_kabid', 'nama_atasan_langsung');
            $table->renameColumn('nip_kabid', 'nip_atasan_langsung');
            $table->renameColumn('tanggal_kabid', 'tanggal_atasan_langsung');
            $table->renameColumn('catatan_kabid', 'catatan_atasan_langsung');
            $table->renameColumn('status_kabid', 'status_atasan_langsung');
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->renameColumn('kadin_disetujui_hari', 'kepala_dinas_disetujui_hari');
            $table->renameColumn('kadin_perubahan_hari', 'kepala_dinas_perubahan_hari');
            $table->renameColumn('kadin_ditangguhkan_hari', 'kepala_dinas_ditangguhkan_hari');
            $table->renameColumn('kadin_tidak_disetujui_hari', 'kepala_dinas_tidak_disetujui_hari');
            $table->renameColumn('nama_kadin', 'nama_kepala_dinas');
            $table->renameColumn('nip_kadin', 'nip_kepala_dinas');
            $table->renameColumn('tanggal_kadin', 'tanggal_kepala_dinas');
            $table->renameColumn('catatan_kadin', 'catatan_kepala_dinas');
            $table->renameColumn('status_kadin', 'status_kepala_dinas');
        });

        // Update role values pada tabel users
        DB::table('users')->where('role', 'kabid')->update(['role' => 'atasan_langsung']);
        DB::table('users')->where('role', 'kadin')->update(['role' => 'kepala_dinas']);

        // Update status values pada tabel pengajuan_cutis
        DB::table('pengajuan_cutis')->where('status', 'diproses_kabid')->update(['status' => 'diproses_atasan_langsung']);
        DB::table('pengajuan_cutis')->where('status', 'diproses_kadin')->update(['status' => 'diproses_kepala_dinas']);
    }

    public function down(): void
    {
        // Kembalikan role values
        DB::table('users')->where('role', 'atasan_langsung')->update(['role' => 'kabid']);
        DB::table('users')->where('role', 'kepala_dinas')->update(['role' => 'kadin']);

        // Kembalikan status values
        DB::table('pengajuan_cutis')->where('status', 'diproses_atasan_langsung')->update(['status' => 'diproses_kabid']);
        DB::table('pengajuan_cutis')->where('status', 'diproses_kepala_dinas')->update(['status' => 'diproses_kadin']);

        // Kembalikan kolom pada tabel pengajuan_cutis
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->renameColumn('atasan_langsung_user_id', 'kabid_user_id');
            $table->renameColumn('atasan_langsung_disetujui_hari', 'kabid_disetujui_hari');
            $table->renameColumn('atasan_langsung_perubahan_hari', 'kabid_perubahan_hari');
            $table->renameColumn('atasan_langsung_ditangguhkan_hari', 'kabid_ditangguhkan_hari');
            $table->renameColumn('atasan_langsung_tidak_disetujui_hari', 'kabid_tidak_disetujui_hari');
            $table->renameColumn('nama_atasan_langsung', 'nama_kabid');
            $table->renameColumn('nip_atasan_langsung', 'nip_kabid');
            $table->renameColumn('tanggal_atasan_langsung', 'tanggal_kabid');
            $table->renameColumn('catatan_atasan_langsung', 'catatan_kabid');
            $table->renameColumn('status_atasan_langsung', 'status_kabid');
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->renameColumn('kepala_dinas_disetujui_hari', 'kadin_disetujui_hari');
            $table->renameColumn('kepala_dinas_perubahan_hari', 'kadin_perubahan_hari');
            $table->renameColumn('kepala_dinas_ditangguhkan_hari', 'kadin_ditangguhkan_hari');
            $table->renameColumn('kepala_dinas_tidak_disetujui_hari', 'kadin_tidak_disetujui_hari');
            $table->renameColumn('nama_kepala_dinas', 'nama_kadin');
            $table->renameColumn('nip_kepala_dinas', 'nip_kadin');
            $table->renameColumn('tanggal_kepala_dinas', 'tanggal_kadin');
            $table->renameColumn('catatan_kepala_dinas', 'catatan_kadin');
            $table->renameColumn('status_kepala_dinas', 'status_kadin');
        });
    }
};
