<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanCutiExport implements FromQuery, WithMapping, WithHeadings, WithTitle, ShouldAutoSize
{
    protected int $no = 0;

    protected array $statusLabels = [
        'pending'         => 'Menunggu',
        'disetujui'       => 'Disetujui',
        'tidak_disetujui' => 'Ditolak',
        'perubahan'       => 'Perubahan',
        'ditangguhkan'    => 'Ditangguhkan',
    ];

    public function __construct(protected Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function title(): string
    {
        return 'Laporan Cuti';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIP',
            'Unit Kerja',
            'Jenis Cuti',
            'Lama (hari)',
            'Tgl Mulai',
            'Tgl Selesai',
            'Alasan',
            'Atasan Langsung',
            'Kasubag',
            'Sekretaris',
            'Kepala Dinas',
            'Sekretaris Daerah',
            'Wali Kota',
            'Status',
            'Tgl Pengajuan',
            'Sisa N-2',
            'Sisa N-1',
            'Sisa N',
            'Total Sisa Cuti',
        ];
    }

    public function map($p): array
    {
        $this->no++;

        $saldo = $p->pegawai->saldoCutis->first();
        $n2 = $saldo ? (int) $saldo->saldo_n2 : null;
        $n1 = $saldo ? (int) $saldo->saldo_n1 : null;
        $n = $saldo ? (int) $saldo->saldo_n : null;

        return [
            $this->no,
            $p->pegawai->nama,
            $p->pegawai->nip,
            $p->pegawai->unit_kerja,
            $p->jenisCuti->nama,
            $p->lama_cuti_hari,
            optional($p->tanggal_mulai)->format('d/m/Y'),
            optional($p->tanggal_selesai)->format('d/m/Y'),
            $p->alasan_cuti,
            $this->label($p->status_atasan_langsung),
            $this->label($p->status_kasubag),
            $this->label($p->status_sekretaris),
            $this->label($p->status_kepala_dinas),
            $p->isKepalaDinasApplicant() ? $this->label($p->status_sekda) : '-',
            ($p->isCutiKhusus() || $p->isKepalaDinasApplicant()) ? $this->label($p->status_walikota) : '-',
            ucfirst($p->status),
            optional($p->tanggal_pengajuan)->format('d/m/Y'),
            $n2 ?? '-',
            $n1 ?? '-',
            $n ?? '-',
            $saldo ? ($n2 + $n1 + $n) : '-',
        ];
    }

    protected function label(?string $status): string
    {
        return $this->statusLabels[$status] ?? 'Menunggu';
    }
}
