<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_cutis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();

            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cutis');

            // Bagian III - Alasan Cuti
            $table->text('alasan_cuti')->nullable();

            // Bagian IV - Lamanya Cuti
            $table->integer('lama_cuti_hari');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // Bagian VI - Alamat selama menjalankan cuti
            $table->string('alamat_selama_cuti')->nullable();
            $table->string('telpon_selama_cuti')->nullable();

            $table->date('tanggal_pengajuan')->nullable();

            // Bagian VII - Pertimbangan Atasan Langsung
            $table->text('catatan_atasan')->nullable();
            $table->integer('atasan_disetujui_hari')->nullable();
            $table->integer('atasan_perubahan_hari')->nullable();
            $table->integer('atasan_ditangguhkan_hari')->nullable();
            $table->integer('atasan_tidak_disetujui_hari')->nullable();
            $table->string('nama_atasan')->nullable();
            $table->string('nip_atasan')->nullable();
            $table->date('tanggal_pertimbangan_atasan')->nullable();
            $table->enum('status_atasan', ['pending', 'disetujui', 'perubahan', 'ditangguhkan', 'tidak_disetujui'])
                ->default('pending');

            // Bagian VIII - Keputusan Pejabat yang berwenang
            $table->integer('pejabat_disetujui_hari')->nullable();
            $table->integer('pejabat_perubahan_hari')->nullable();
            $table->integer('pejabat_ditangguhkan_hari')->nullable();
            $table->integer('pejabat_tidak_disetujui_hari')->nullable();
            $table->string('nama_pejabat')->nullable();
            $table->string('nip_pejabat')->nullable();
            $table->date('tanggal_keputusan_pejabat')->nullable();
            $table->enum('status_pejabat', ['pending', 'disetujui', 'perubahan', 'ditangguhkan', 'tidak_disetujui'])
                ->default('pending');

            // Status keseluruhan alur
            $table->enum('status', ['diajukan', 'diproses_atasan', 'diproses_pejabat', 'disetujui', 'ditolak'])
                ->default('diajukan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cutis');
    }
};
