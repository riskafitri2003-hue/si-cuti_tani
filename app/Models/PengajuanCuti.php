<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanCuti extends Model
{
    protected $primaryKey = 'pengajuan_cuti_id';

    protected $fillable = [
        'nip', 'kode_jenis_cuti',
        'alasan_cuti', 'lama_cuti_hari', 'tanggal_mulai', 'tanggal_selesai',
        'alamat_selama_cuti', 'telpon_selama_cuti', 'tanggal_pengajuan',
        'dokumen_pendukung', 'tanda_tangan_pegawai', 'atasan_langsung_user_id',
        'status',

        // Atasan Langsung
        'atasan_langsung_disetujui_hari', 'atasan_langsung_perubahan_hari',
        'atasan_langsung_ditangguhkan_hari', 'atasan_langsung_tidak_disetujui_hari',
        'nama_atasan_langsung', 'nip_atasan_langsung', 'tanggal_atasan_langsung', 'catatan_atasan_langsung', 'status_atasan_langsung',

        // Kasubag Umum
        'kasubag_disetujui_hari', 'kasubag_perubahan_hari',
        'kasubag_ditangguhkan_hari', 'kasubag_tidak_disetujui_hari',
        'nama_kasubag', 'nip_kasubag', 'tanggal_kasubag', 'catatan_kasubag', 'status_kasubag',

        // Sekretaris
        'sekretaris_disetujui_hari', 'sekretaris_perubahan_hari',
        'sekretaris_ditangguhkan_hari', 'sekretaris_tidak_disetujui_hari',
        'nama_sekretaris', 'nip_sekretaris', 'tanggal_sekretaris', 'catatan_sekretaris', 'status_sekretaris',

        // Kepala Dinas
        'kepala_dinas_disetujui_hari', 'kepala_dinas_perubahan_hari',
        'kepala_dinas_ditangguhkan_hari', 'kepala_dinas_tidak_disetujui_hari',
        'nama_kepala_dinas', 'nip_kepala_dinas', 'tanggal_kepala_dinas', 'catatan_kepala_dinas', 'status_kepala_dinas',
        'tanda_tangan_kepala_dinas', 'nomor_surat', 'tanggal_surat',

        // Walikota
        'nama_walikota', 'nip_walikota', 'tanggal_walikota', 'status_walikota', 'tanda_tangan_walikota',

        // Sekretaris Daerah
        'nama_sekda', 'nip_sekda', 'tanggal_sekda', 'status_sekda', 'tanda_tangan_sekda',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_surat' => 'date',
        'tanggal_pengajuan' => 'date',
        'tanggal_atasan_langsung' => 'date',
        'tanggal_kasubag' => 'date',
        'tanggal_sekretaris' => 'date',
        'tanggal_kepala_dinas' => 'date',
        'tanggal_walikota' => 'date',
        'tanggal_sekda' => 'date',
    ];

    public function isCutiKhusus(): bool
    {
        return in_array($this->jenisCuti->kode ?? 0, [2, 7]);
    }

    public function needsWalikota(): bool
    {
        return $this->isCutiKhusus();
    }

    public function isKepalaDinasApplicant(): bool
    {
        return optional($this->pegawai?->user)->role === 'kepala_dinas';
    }

    public function isAlurKepalaDinas(): bool
    {
        return $this->isKepalaDinasApplicant() && in_array($this->status, ['diproses_sekda', 'diproses_walikota', 'disetujui']);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'kode_jenis_cuti', 'kode');
    }

    public function atasanLangsungUser()
    {
        return $this->belongsTo(User::class, 'atasan_langsung_user_id', 'user_id');
    }

    public function saran()
    {
        return $this->hasOne(SaranCuti::class, 'nip', 'nip');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'diajukan' => 'secondary',
            'diproses_atasan_langsung' => 'info',
            'diproses_kasubag' => 'info',
            'diproses_sekretaris' => 'info',
            'diproses_sekda' => 'info',
            'diproses_kepala_dinas' => 'primary',
            'diproses_walikota' => 'purple',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }
}
