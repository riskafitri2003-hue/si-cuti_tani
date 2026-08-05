<?php

namespace App\Http\Controllers;

use App\Models\PengajuanCuti;
use App\Models\SaldoCuti;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = PengajuanCuti::with(['pegawai', 'jenisCuti']);

        if ($user->isPegawai()) {
            $query->where('nip', $user->nip);
        } elseif ($user->isAtasanLangsung()) {
            $query->where('status', 'diajukan');
        } elseif ($user->isKasubag()) {
            $query->where('status', 'diproses_kasubag');
        } elseif ($user->isSekretaris()) {
            $query->where('status', 'diproses_sekretaris');
        } elseif ($user->isKepalaDinas()) {
            $query->where('status', 'diproses_kepala_dinas');
        } elseif ($user->isWalikota()) {
            $query->where('status', 'diproses_walikota');
        }

        $pengajuanCutis = $query->latest()->take(10)->get();

        $stats = [
            'total' => PengajuanCuti::count(),
            'diajukan' => PengajuanCuti::where('status', 'diajukan')->count(),
            'diproses_atasan_langsung' => PengajuanCuti::where('status', 'diproses_atasan_langsung')->count(),
            'diproses_kasubag' => PengajuanCuti::where('status', 'diproses_kasubag')->count(),
            'diproses_sekretaris' => PengajuanCuti::where('status', 'diproses_sekretaris')->count(),
            'diproses_kepala_dinas' => PengajuanCuti::where('status', 'diproses_kepala_dinas')->count(),
            'diproses_walikota' => PengajuanCuti::where('status', 'diproses_walikota')->count(),
            'disetujui' => PengajuanCuti::where('status', 'disetujui')->count(),
            'ditolak' => PengajuanCuti::where('status', 'ditolak')->count(),
        ];

        // Data saldo cuti untuk pegawai
        $saldoCutis = collect();
        if ($user->isPegawai() && $user->nip) {
            $saldoCutis = SaldoCuti::where('nip', $user->nip)->get();
        }

        return view('dashboard', compact('pengajuanCutis', 'stats', 'saldoCutis'));
    }
}
