<?php

namespace App\Http\Controllers;

use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = PengajuanCuti::with(['pegawai', 'jenisCuti']);

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

        $pengajuanCutis = $query->latest('tanggal_pengajuan')->paginate(20)->withQueryString();

        // Statistik
        $baseQuery = PengajuanCuti::query();
        if ($request->filled('dari')) {
            $baseQuery->where('tanggal_mulai', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $baseQuery->where('tanggal_selesai', '<=', $request->sampai);
        }
        if ($request->filled('status') && $request->status !== '') {
            $baseQuery->where('status', $request->status);
        }
        if ($request->filled('nip')) {
            $baseQuery->where('nip', $request->nip);
        }
        if ($request->filled('kode_jenis_cuti')) {
            $baseQuery->where('kode_jenis_cuti', $request->kode_jenis_cuti);
        }

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

        return view('laporan.index', compact(
            'pengajuanCutis', 'total', 'disetujui', 'ditolak', 'proses',
            'pegawais', 'jenisCutis', 'chartJenisLabel', 'chartJenisData'
        ));
    }
}
