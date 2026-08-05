<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom lama
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropColumn([
                'catatan_atasan',
                'atasan_disetujui_hari',
                'atasan_perubahan_hari',
                'atasan_ditangguhkan_hari',
                'atasan_tidak_disetujui_hari',
                'nama_atasan',
                'nip_atasan',
                'tanggal_pertimbangan_atasan',
                'status_atasan',
                'pejabat_disetujui_hari',
                'pejabat_perubahan_hari',
                'pejabat_ditangguhkan_hari',
                'pejabat_tidak_disetujui_hari',
                'nama_pejabat',
                'nip_pejabat',
                'tanggal_keputusan_pejabat',
                'status_pejabat',
                'nomor_surat',
                'tanggal_surat',
            ]);
        });

        // Ubah status dari enum ke string
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->string('status', 50)->default('diajukan')->change();
        });

        // Tambah kolom baru
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            // 1. Kabid
            $table->integer('kabid_disetujui_hari')->nullable();
            $table->integer('kabid_perubahan_hari')->nullable();
            $table->integer('kabid_ditangguhkan_hari')->nullable();
            $table->integer('kabid_tidak_disetujui_hari')->nullable();
            $table->string('nama_kabid')->nullable();
            $table->string('nip_kabid')->nullable();
            $table->date('tanggal_kabid')->nullable();
            $table->text('catatan_kabid')->nullable();
            $table->string('status_kabid', 50)->default('pending');

            // 2. Kasubag Umum
            $table->integer('kasubag_disetujui_hari')->nullable();
            $table->integer('kasubag_perubahan_hari')->nullable();
            $table->integer('kasubag_ditangguhkan_hari')->nullable();
            $table->integer('kasubag_tidak_disetujui_hari')->nullable();
            $table->string('nama_kasubag')->nullable();
            $table->string('nip_kasubag')->nullable();
            $table->date('tanggal_kasubag')->nullable();
            $table->text('catatan_kasubag')->nullable();
            $table->string('status_kasubag', 50)->default('pending');

            // 3. Sekretaris
            $table->integer('sekretaris_disetujui_hari')->nullable();
            $table->integer('sekretaris_perubahan_hari')->nullable();
            $table->integer('sekretaris_ditangguhkan_hari')->nullable();
            $table->integer('sekretaris_tidak_disetujui_hari')->nullable();
            $table->string('nama_sekretaris')->nullable();
            $table->string('nip_sekretaris')->nullable();
            $table->date('tanggal_sekretaris')->nullable();
            $table->text('catatan_sekretaris')->nullable();
            $table->string('status_sekretaris', 50)->default('pending');

            // 4. Kepala Dinas (Kadin)
            $table->integer('kadin_disetujui_hari')->nullable();
            $table->integer('kadin_perubahan_hari')->nullable();
            $table->integer('kadin_ditangguhkan_hari')->nullable();
            $table->integer('kadin_tidak_disetujui_hari')->nullable();
            $table->string('nama_kadin')->nullable();
            $table->string('nip_kadin')->nullable();
            $table->date('tanggal_kadin')->nullable();
            $table->text('catatan_kadin')->nullable();
            $table->string('status_kadin', 50)->default('pending');

            // Nomor surat
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropColumn([
                'kabid_disetujui_hari', 'kabid_perubahan_hari', 'kabid_ditangguhkan_hari', 'kabid_tidak_disetujui_hari',
                'nama_kabid', 'nip_kabid', 'tanggal_kabid', 'catatan_kabid', 'status_kabid',
                'kasubag_disetujui_hari', 'kasubag_perubahan_hari', 'kasubag_ditangguhkan_hari', 'kasubag_tidak_disetujui_hari',
                'nama_kasubag', 'nip_kasubag', 'tanggal_kasubag', 'catatan_kasubag', 'status_kasubag',
                'sekretaris_disetujui_hari', 'sekretaris_perubahan_hari', 'sekretaris_ditangguhkan_hari', 'sekretaris_tidak_disetujui_hari',
                'nama_sekretaris', 'nip_sekretaris', 'tanggal_sekretaris', 'catatan_sekretaris', 'status_sekretaris',
                'kadin_disetujui_hari', 'kadin_perubahan_hari', 'kadin_ditangguhkan_hari', 'kadin_tidak_disetujui_hari',
                'nama_kadin', 'nip_kadin', 'tanggal_kadin', 'catatan_kadin', 'status_kadin',
                'nomor_surat', 'tanggal_surat',
            ]);
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'diproses_atasan', 'diproses_pejabat', 'disetujui', 'ditolak'])
                ->default('diajukan')->change();
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->text('catatan_atasan')->nullable();
            $table->integer('atasan_disetujui_hari')->nullable();
            $table->integer('atasan_perubahan_hari')->nullable();
            $table->integer('atasan_ditangguhkan_hari')->nullable();
            $table->integer('atasan_tidak_disetujui_hari')->nullable();
            $table->string('nama_atasan')->nullable();
            $table->string('nip_atasan')->nullable();
            $table->date('tanggal_pertimbangan_atasan')->nullable();
            $table->enum('status_atasan', ['pending', 'disetujui', 'perubahan', 'ditangguhkan', 'tidak_disetujui'])->default('pending');
            $table->integer('pejabat_disetujui_hari')->nullable();
            $table->integer('pejabat_perubahan_hari')->nullable();
            $table->integer('pejabat_ditangguhkan_hari')->nullable();
            $table->integer('pejabat_tidak_disetujui_hari')->nullable();
            $table->string('nama_pejabat')->nullable();
            $table->string('nip_pejabat')->nullable();
            $table->date('tanggal_keputusan_pejabat')->nullable();
            $table->enum('status_pejabat', ['pending', 'disetujui', 'perubahan', 'ditangguhkan', 'tidak_disetujui'])->default('pending');
        });
    }
};
