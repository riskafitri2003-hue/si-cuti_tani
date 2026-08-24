<?php

namespace App\Http\Controllers;

use App\Exports\LaporanCutiExport;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public const BULAN = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $query = $this->applyFilters(
            PengajuanCuti::with(['pegawai.saldoCutis', 'jenisCuti']),
            $request
        );

        $pengajuanCutis = $query->latest('tanggal_pengajuan')->paginate(20)->withQueryString();

        // Statistik
        $baseQuery = $this->applyFilters(PengajuanCuti::query(), $request);

        $total = (clone $baseQuery)->count();
        $disetujui = (clone $baseQuery)->where('status', 'disetujui')->count();
        $ditolak = (clone $baseQuery)->where('status', 'ditolak')->count();
        $proses = $total - $disetujui - $ditolak;

        // Data grafik per jenis cuti
        $chartJenis = (clone $baseQuery)
            ->selectRaw('kode_jenis_cuti, count(*) as total')
            ->groupBy('kode_jenis_cuti')
            ->pluck('total', 'kode_jenis_cuti');

        $jenisCutis = JenisCuti::orderBy('kode')->get();
        $chartJenisLabel = [];
        $chartJenisData = [];
        foreach ($jenisCutis as $jc) {
            $chartJenisLabel[] = $jc->nama;
            $chartJenisData[] = $chartJenis[$jc->kode] ?? 0;
        }

        $pegawais = Pegawai::orderBy('nama')->get();

        $bulanList = self::BULAN;
        $periodeLabel = $this->periodeLabel($request);

        return view('laporan.index', compact(
            'pengajuanCutis', 'total', 'disetujui', 'ditolak', 'proses',
            'pegawais', 'jenisCutis', 'chartJenisLabel', 'chartJenisData',
            'bulanList', 'periodeLabel'
        ));
    }

    public function export(Request $request)
    {
        $query = $this->applyFilters(
            PengajuanCuti::with(['pegawai.saldoCutis', 'jenisCuti'])->latest('tanggal_pengajuan'),
            $request
        );

        $label = $this->periodeLabel($request);
        $namaFile = 'Laporan-Cuti' . ($label ? '-' . str_replace(' ', '-', $label) : '');

        return Excel::download(new LaporanCutiExport($query), $namaFile . '.xlsx');
    }

    private function applyFilters(Builder $query, Request $request): Builder
    {
        // Filter bulan & tahun (basis tanggal_mulai)
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_mulai', (int) $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', (int) $request->tahun);
        }

        // Filter periode tanggal_mulai
        if ($request->filled('dari')) {
            $query->where('tanggal_mulai', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal_selesai', '<=', $request->sampai);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter pegawai
        if ($request->filled('nip')) {
            $query->where('nip', $request->nip);
        }

        // Filter jenis cuti
        if ($request->filled('kode_jenis_cuti')) {
            $query->where('kode_jenis_cuti', $request->kode_jenis_cuti);
        }

        return $query;
    }

    private function periodeLabel(Request $request): ?string
    {
        $label = null;

        if ($request->filled('bulan') && isset(self::BULAN[(int) $request->bulan])) {
            $label = self::BULAN[(int) $request->bulan];
        }
        if ($request->filled('tahun')) {
            $label = trim(($label ?? '') . ' ' . $request->tahun);
        }

        return $label !== null && $label !== '' ? $label : null;
    }
}
