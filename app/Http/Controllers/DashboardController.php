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
        } elseif (! $user->isAdmin()) {
            $stageMap = [
                'atasan_langsung' => 'diajukan',
                'kasubag' => 'diproses_kasubag',
                'sekretaris' => 'diproses_sekretaris',
                'kepala_dinas' => 'diproses_kepala_dinas',
                'sekda' => 'diproses_sekda',
                'walikota' => 'diproses_walikota',
            ];

            $query->where(function ($q) use ($user, $stageMap) {
                foreach ($stageMap as $role => $status) {
                    if ($user->hasRole($role)) {
                        $q->orWhere('status', $status);
                    }
                }

                // Cuti diajukan yang menunggu tanda tangannya sebagai atasan langsung
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('status', 'diajukan')
                        ->where('atasan_langsung_user_id', $user->user_id);
                });
            });
        }

        $pengajuanCutis = $query->latest()->take(10)->get();

        $stats = [
            'total' => PengajuanCuti::count(),
            'diajukan' => PengajuanCuti::where('status', 'diajukan')->count(),
            'diproses_atasan_langsung' => PengajuanCuti::where('status', 'diproses_atasan_langsung')->count(),
            'diproses_kasubag' => PengajuanCuti::where('status', 'diproses_kasubag')->count(),
            'diproses_sekretaris' => PengajuanCuti::where('status', 'diproses_sekretaris')->count(),
            'diproses_sekda' => PengajuanCuti::where('status', 'diproses_sekda')->count(),
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
