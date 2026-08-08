<?php

namespace App\Http\Controllers;

use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\SaldoCuti;
use App\Models\SaranCuti;
use App\Models\User;
use App\Notifications\StatusCutiNotifikasi;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanCutiController extends Controller
{
    public function index(Request $request)
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
        } elseif ($user->isSekda()) {
            $query->where('status', 'diproses_sekda');
        } elseif ($user->isWalikota()) {
            $query->where('status', 'diproses_walikota');
        }

        $pengajuanCutis = $query->latest()->paginate(10);

        return view('cuti.index', compact('pengajuanCutis'));
    }

    public function create()
    {
        $user = Auth::user();
        $jenisCutis = JenisCuti::orderBy('kode')->get();
        $atasanLangsungs = \App\Models\User::whereIn('role', ['atasan_langsung', 'sekretaris', 'kepala_dinas', 'sekda', 'walikota'])
            ->orderBy('nama')->get();

        $pegawais = $user->isAdmin() ? Pegawai::with('saldoCutis')->orderBy('nama')->get() : collect();

        $isKepalaDinasApplicant = $user->isKepalaDinas();

        return view('cuti.create', compact('jenisCutis', 'pegawais', 'atasanLangsungs', 'isKepalaDinasApplicant'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $pegawaiNip = $user->isAdmin() ? $request->input('nip') : $user->nip;
        $isKepalaDinasApplicant = \App\Models\User::where('nip', $pegawaiNip)->value('role') === 'kepala_dinas';

        $rules = [
            'kode_jenis_cuti' => ['required', 'exists:jenis_cutis,kode'],
            'alasan_cuti' => ['required', 'string'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alamat_selama_cuti' => ['nullable', 'string', 'max:255'],
            'telpon_selama_cuti' => ['nullable', 'string', 'max:30'],
            'dokumen_pendukung' => ['nullable', 'file', 'max:2048', 'mimes:pdf,jpg,jpeg,png'],
            'tanda_tangan_data' => ['nullable', 'string'],
            'atasan_langsung_user_id' => $isKepalaDinasApplicant
                ? ['nullable', 'exists:users,user_id']
                : ['required', 'exists:users,user_id'],
        ];

        if ($user->isAdmin()) {
            $rules['nip'] = ['required', 'exists:pegawais,nip'];
        }

        $data = $request->validate($rules);

        $pegawaiNip = $user->isAdmin() ? $data['nip'] : $user->nip;

        if (! $pegawaiNip) {
            return back()->withErrors('Akun Anda belum terhubung dengan data pegawai. Hubungi admin.');
        }

        $mulai = \Carbon\Carbon::parse($data['tanggal_mulai']);
        $selesai = \Carbon\Carbon::parse($data['tanggal_selesai']);
        $lamaHari = $mulai->diffInDays($selesai) + 1;

        $saldoN = SaldoCuti::where('nip', $pegawaiNip)->value('saldo_n');
        if ($saldoN === null) {
            return back()->withErrors('Data saldo cuti tidak ditemukan. Hubungi admin.');
        }
        if ($lamaHari > $saldoN) {
            return back()->withErrors("Sisa cuti tidak mencukupi. Sisa: {$saldoN} hari, diajukan: {$lamaHari} hari.");
        }

        // Simpan dokumen pendukung
        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('dokumen', 'public');
        }

        // Simpan tanda tangan
        $ttdPath = null;
        if ($request->filled('tanda_tangan_data')) {
            $base64 = $request->input('tanda_tangan_data');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            $filename = 'ttd_' . $pegawaiNip . '_' . time() . '.png';
            Storage::disk('public')->put('tanda_tangan/' . $filename, $imageData);
            $ttdPath = 'tanda_tangan/' . $filename;
        }

        $cuti = PengajuanCuti::create([
            'nip' => $pegawaiNip,
            'kode_jenis_cuti' => $data['kode_jenis_cuti'],
            'alasan_cuti' => $data['alasan_cuti'],
            'lama_cuti_hari' => $lamaHari,
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'alamat_selama_cuti' => $data['alamat_selama_cuti'] ?? null,
            'telpon_selama_cuti' => $data['telpon_selama_cuti'] ?? null,
            'dokumen_pendukung' => $dokumenPath,
            'tanda_tangan_pegawai' => $ttdPath,
            'atasan_langsung_user_id' => $isKepalaDinasApplicant ? null : $data['atasan_langsung_user_id'],
            'tanggal_pengajuan' => now(),
            'status' => $isKepalaDinasApplicant ? 'diproses_sekda' : 'diajukan',
            'status_atasan_langsung' => 'pending',
            'status_kasubag' => 'pending',
            'status_sekretaris' => 'pending',
            'status_sekda' => 'pending',
            'status_kepala_dinas' => 'pending',
            'status_walikota' => 'pending',
        ]);

        SaldoCuti::where('nip', $pegawaiNip)->decrement('saldo_n', $lamaHari);

        // Kirim notifikasi ke pegawai
        $pegawai = Pegawai::find($pegawaiNip);
        if ($pegawai && $pegawai->email) {
            $userModel = User::where('nip', $pegawaiNip)->first();
            if ($userModel) {
                $userModel->notify(new StatusCutiNotifikasi($cuti, 'Pengajuan cuti Anda telah berhasil dikirim.'));
            }
        }

        // Kirim WA otomatis ke Atasan Langsung / Sekretaris Daerah
        if ($isKepalaDinasApplicant) {
            $approver = $this->getNextApprover($cuti);
            if ($approver) {
                app(WhatsAppService::class)->kirimKeApprover(
                    $cuti,
                    $approver,
                    $this->pesanPermohonanPersetujuan($cuti, $approver)
                );
            }
        } else {
            $atasan = User::find($data['atasan_langsung_user_id']);
            if ($atasan) {
                app(WhatsAppService::class)->kirimKeApprover(
                    $cuti,
                    $atasan,
                    $this->pesanPermohonanPersetujuan($cuti, $atasan)
                );
            }
        }

        $pesan = $isKepalaDinasApplicant
            ? 'Pengajuan cuti berhasil dikirim. Menunggu tanda tangan Sekretaris Daerah dan Wali Kota.'
            : 'Pengajuan cuti berhasil dikirim. Silakan kirim persetujuan ke atasan via WhatsApp atau Email.';

        return redirect()->route('cuti.show', $cuti)->with('success', $pesan);
    }

    public function show(PengajuanCuti $cuti)
    {
        $cuti->load(['pegawai.saldoCutis', 'jenisCuti', 'atasanLangsungUser.pegawai']);

        $nextApprover = $this->getNextApprover($cuti);

        return view('cuti.show', compact('cuti', 'nextApprover'));
    }

    public function downloadDokumen(PengajuanCuti $cuti)
    {
        if (! $cuti->dokumen_pendukung) {
            abort(404);
        }

        $path = Storage::disk('public')->path($cuti->dokumen_pendukung);
        $filename = basename($cuti->dokumen_pendukung);

        return response()->download($path, $filename);
    }

    /**
     * Simpan saran/masukan pengaju.
     */
    public function storeSaran(Request $request, PengajuanCuti $cuti)
    {
        $isOwner = auth()->user()->isAdmin() || (auth()->user()->isPegawai() && auth()->user()->nip === $cuti->nip);
        abort_if(! $isOwner, 403);

        $data = $request->validate([
            'kesulitan_menu' => ['nullable', 'boolean'],
            'pengajuan_gagal' => ['nullable', 'boolean'],
            'file_gagal' => ['nullable', 'boolean'],
            'halaman_lambat' => ['nullable', 'boolean'],
            'saran' => ['nullable', 'string', 'max:1000'],
        ]);

        SaranCuti::updateOrCreate(
            ['nip' => $cuti->nip],
            [
                'kesulitan_menu' => $request->boolean('kesulitan_menu'),
                'pengajuan_gagal' => $request->boolean('pengajuan_gagal'),
                'file_gagal' => $request->boolean('file_gagal'),
                'halaman_lambat' => $request->boolean('halaman_lambat'),
                'saran' => $data['saran'] ?? null,
            ]
        );

        return back()->with('success', 'Terima kasih atas saran dan masukannya.');
    }

    /**
     * Approval Atasan Langsung
     */
    public function approveAtasanLangsungForm(PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diajukan', 403);
        abort_if($cuti->atasan_langsung_user_id && auth()->id() !== $cuti->atasan_langsung_user_id, 403);

        return view('cuti.approve_atasan_langsung', compact('cuti'));
    }

    public function approveAtasanLangsung(Request $request, PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diajukan', 403);
        abort_if($cuti->atasan_langsung_user_id && auth()->id() !== $cuti->atasan_langsung_user_id, 403);

        $data = $request->validate([
            'status_atasan_langsung' => ['required', 'in:disetujui,tidak_disetujui'],
            'catatan_atasan_langsung' => ['nullable', 'string'],
            'nama_atasan_langsung' => ['required', 'string', 'max:255'],
            'nip_atasan_langsung' => ['nullable', 'string', 'max:50'],
        ]);

        $data['tanggal_atasan_langsung'] = now();
        $data['status'] = $data['status_atasan_langsung'] === 'tidak_disetujui' ? 'ditolak' : 'diproses_kasubag';

        $cuti->update($data);

        if ($data['status_atasan_langsung'] === 'tidak_disetujui') {
            SaldoCuti::where('nip', $cuti->nip)->increment('saldo_n', $cuti->lama_cuti_hari);
        }

        $this->kirimNotifikasi($cuti, 'Atasan Langsung');
        $this->kirimNotifikasiWa($cuti, 'Atasan Langsung');

        return redirect()->route('cuti.index')->with('success', 'Pertimbangan Atasan Langsung tersimpan.');
    }

    /**
     * Approval Kasubag Umum
     */
    public function approveKasubagForm(PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_kasubag', 403);

        return view('cuti.approve_kasubag', compact('cuti'));
    }

    public function approveKasubag(Request $request, PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_kasubag', 403);

        $data = $request->validate([
            'status_kasubag' => ['required', 'in:disetujui,tidak_disetujui'],
            'catatan_kasubag' => ['nullable', 'string'],
            'nama_kasubag' => ['required', 'string', 'max:255'],
            'nip_kasubag' => ['nullable', 'string', 'max:50'],
        ]);

        $data['tanggal_kasubag'] = now();
        $data['status'] = $data['status_kasubag'] === 'tidak_disetujui' ? 'ditolak' : 'diproses_sekretaris';

        $cuti->update($data);

        if ($data['status_kasubag'] === 'tidak_disetujui') {
            SaldoCuti::where('nip', $cuti->nip)->increment('saldo_n', $cuti->lama_cuti_hari);
        }

        $this->kirimNotifikasi($cuti, 'Kasubag Umum');
        $this->kirimNotifikasiWa($cuti, 'Kasubag Umum');

        return redirect()->route('cuti.index')->with('success', 'Persetujuan Kasubag Umum tersimpan.');
    }

    /**
     * Approval Sekretaris
     */
    public function approveSekretarisForm(PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_sekretaris', 403);

        return view('cuti.approve_sekretaris', compact('cuti'));
    }

    public function approveSekretaris(Request $request, PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_sekretaris', 403);

        $data = $request->validate([
            'status_sekretaris' => ['required', 'in:disetujui,tidak_disetujui'],
            'catatan_sekretaris' => ['nullable', 'string'],
            'nama_sekretaris' => ['required', 'string', 'max:255'],
            'nip_sekretaris' => ['nullable', 'string', 'max:50'],
        ]);

        $data['tanggal_sekretaris'] = now();
        $data['status'] = $data['status_sekretaris'] === 'tidak_disetujui' ? 'ditolak' : 'diproses_kepala_dinas';

        $cuti->update($data);

        if ($data['status_sekretaris'] === 'tidak_disetujui') {
            SaldoCuti::where('nip', $cuti->nip)->increment('saldo_n', $cuti->lama_cuti_hari);
        }

        $this->kirimNotifikasi($cuti, 'Sekretaris');
        $this->kirimNotifikasiWa($cuti, 'Sekretaris');

        return redirect()->route('cuti.index')->with('success', 'Persetujuan Sekretaris tersimpan.');
    }

    /**
     * Approval Kepala Dinas - final untuk reguler, lanjut ke Walikota untuk cuti khusus
     */
    public function approveKepalaDinasForm(PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_kepala_dinas', 403);

        return view('cuti.approve_kepala_dinas', compact('cuti'));
    }

    public function approveKepalaDinas(Request $request, PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_kepala_dinas', 403);

        $rules = [
            'status_kepala_dinas' => ['required', 'in:disetujui,tidak_disetujui'],
            'catatan_kepala_dinas' => ['nullable', 'string'],
            'nama_kepala_dinas' => ['required', 'string', 'max:255'],
            'nip_kepala_dinas' => ['nullable', 'string', 'max:50'],
            'nomor_surat' => ['nullable', 'string', 'max:100'],
            'tanda_tangan_data' => ['nullable', 'string'],
        ];

        $data = $request->validate($rules);

        $data['tanggal_kepala_dinas'] = now();
        $data['tanggal_surat'] = now();

        // Simpan tanda tangan
        if ($request->filled('tanda_tangan_data')) {
            $base64 = $request->input('tanda_tangan_data');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            $filename = 'ttd_kd_' . $cuti->pengajuan_cuti_id . '_' . time() . '.png';
            Storage::disk('public')->put('tanda_tangan/' . $filename, $imageData);
            $data['tanda_tangan_kepala_dinas'] = 'tanda_tangan/' . $filename;
        }

        if ($data['status_kepala_dinas'] === 'tidak_disetujui') {
            $data['status'] = 'ditolak';
        } elseif ($cuti->needsWalikota()) {
            $data['status'] = 'diproses_walikota';
        } else {
            $data['status'] = 'disetujui';
        }

        $cuti->update($data);

        if ($data['status_kepala_dinas'] === 'tidak_disetujui') {
            SaldoCuti::where('nip', $cuti->nip)->increment('saldo_n', $cuti->lama_cuti_hari);
        }

        $this->kirimNotifikasi($cuti, 'Kepala Dinas');
        $this->kirimNotifikasiWa($cuti, 'Kepala Dinas');

        $pesan = $cuti->status === 'diproses_walikota'
            ? 'Tanda tangan Kepala Dinas tersimpan. Menunggu persetujuan Wali Kota.'
            : 'Keputusan Kepala Dinas tersimpan.';

        return redirect()->route('cuti.index')->with('success', $pesan);
    }

    /**
     * Tanda tangan Sekretaris Daerah (khusus pengaju Kepala Dinas)
     */
    public function approveSekdaForm(PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_sekda', 403);
        abort_if(! $cuti->isKepalaDinasApplicant(), 403);

        return view('cuti.approve_sekda', compact('cuti'));
    }

    public function approveSekda(Request $request, PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_sekda', 403);
        abort_if(! $cuti->isKepalaDinasApplicant(), 403);

        $data = $request->validate([
            'nama_sekda' => ['required', 'string', 'max:255'],
            'nip_sekda' => ['nullable', 'string', 'max:50'],
            'tanda_tangan_data' => ['required', 'string'],
        ]);

        $base64 = $request->input('tanda_tangan_data');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        $filename = 'ttd_sekda_' . $cuti->pengajuan_cuti_id . '_' . time() . '.png';
        Storage::disk('public')->put('tanda_tangan/' . $filename, $imageData);

        $cuti->update([
            'nama_sekda' => $data['nama_sekda'],
            'nip_sekda' => $data['nip_sekda'],
            'tanggal_sekda' => now(),
            'status_sekda' => 'disetujui',
            'tanda_tangan_sekda' => 'tanda_tangan/' . $filename,
            'status' => 'diproses_walikota',
        ]);

        $this->kirimNotifikasi($cuti, 'Sekretaris Daerah');
        $this->kirimNotifikasiWa($cuti, 'Sekretaris Daerah');

        return redirect()->route('cuti.index')->with('success', 'Tanda tangan Sekretaris Daerah tersimpan. Menunggu tanda tangan Wali Kota.');
    }

    /**
     * Approval Wali Kota (khusus cuti besar/haji/umroh & tanda tangan pengaju Kepala Dinas)
     */
    public function approveWalikotaForm(PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_walikota', 403);

        $isKepalaDinas = $cuti->isKepalaDinasApplicant();

        return view('cuti.approve_walikota', compact('cuti', 'isKepalaDinas'));
    }

    public function approveWalikota(Request $request, PengajuanCuti $cuti)
    {
        abort_if($cuti->status !== 'diproses_walikota', 403);

        $isKepalaDinas = $cuti->isKepalaDinasApplicant();

        if ($isKepalaDinas) {
            $data = $request->validate([
                'nama_walikota' => ['required', 'string', 'max:255'],
                'nip_walikota' => ['nullable', 'string', 'max:50'],
                'tanda_tangan_data' => ['required', 'string'],
            ]);

            $base64 = $request->input('tanda_tangan_data');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            $filename = 'ttd_wk_' . $cuti->pengajuan_cuti_id . '_' . time() . '.png';
            Storage::disk('public')->put('tanda_tangan/' . $filename, $imageData);

            $data['tanggal_walikota'] = now();
            $data['status_walikota'] = 'disetujui';
            $data['tanda_tangan_walikota'] = 'tanda_tangan/' . $filename;
            $data['status'] = 'disetujui';

            $cuti->update($data);
        } else {
            $data = $request->validate([
                'status_walikota' => ['required', 'in:disetujui,tidak_disetujui'],
                'catatan_walikota' => ['nullable', 'string'],
                'nama_walikota' => ['required', 'string', 'max:255'],
                'nip_walikota' => ['nullable', 'string', 'max:50'],
            ]);

            $data['tanggal_walikota'] = now();
            $data['status'] = $data['status_walikota'] === 'tidak_disetujui' ? 'ditolak' : 'disetujui';

            $cuti->update($data);
        }

        if ($cuti->status === 'ditolak') {
            SaldoCuti::where('nip', $cuti->nip)->increment('saldo_n', $cuti->lama_cuti_hari);
        }

        $this->kirimNotifikasi($cuti, 'Wali Kota');
        $this->kirimNotifikasiWa($cuti, 'Wali Kota');

        $pesan = $isKepalaDinas
            ? 'Tanda tangan Wali Kota tersimpan. Cuti disetujui.'
            : 'Keputusan Wali Kota tersimpan.';

        return redirect()->route('cuti.index')->with('success', $pesan);
    }

    /**
     * Kirim notifikasi ke pegawai
     */
    private function kirimNotifikasi(PengajuanCuti $cuti, string $pengelola): void
    {
        $pegawai = $cuti->pegawai;
        if (! $pegawai) return;

        $user = User::where('nip', $pegawai->nip)->first();
        if (! $user) return;

        $statusLabel = match ($cuti->status) {
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => 'Diproses ke tahap berikutnya',
        };

        $user->notify(new StatusCutiNotifikasi(
            $cuti,
            "Pengajuan cuti Anda telah {$statusLabel} oleh {$pengelola}."
        ));
    }

    /**
     * Kirim WA otomatis: ke approver berikutnya atau ke pegawai saat final.
     */
    private function kirimNotifikasiWa(PengajuanCuti $cuti, string $pengelola): void
    {
        $wa = app(WhatsAppService::class);

        if (in_array($cuti->status, ['disetujui', 'ditolak'])) {
            $pegawai = $cuti->pegawai;
            if ($pegawai) {
                $wa->sendToPegawai($pegawai, $this->pesanFinalPegawai($cuti, $pengelola));
            }
            return;
        }

        $approver = $this->getNextApprover($cuti);
        if ($approver) {
            $wa->kirimKeApprover($cuti, $approver, $this->pesanPermohonanPersetujuan($cuti, $approver));
        }
    }

    /**
     * Pesan WA permohonan persetujuan untuk approver.
     */
    private function pesanPermohonanPersetujuan(PengajuanCuti $cuti, User $approver): string
    {
        return "Assalamu'alaikum Wr. Wb.\n\n"
            . "Yth. Bapak/Ibu {$approver->nama}\n\n"
            . "Terdapat pengajuan cuti yang menunggu persetujuan Anda:\n"
            . "- Pemohon: {$cuti->pegawai->nama}\n"
            . "- NIP: {$cuti->pegawai->nip}\n"
            . "- Jenis Cuti: {$cuti->jenisCuti->nama}\n"
            . "- Tanggal: " . $cuti->tanggal_mulai->format('d M Y') . " s.d. " . $cuti->tanggal_selesai->format('d M Y') . "\n"
            . "- Lama: {$cuti->lama_cuti_hari} hari\n"
            . "- Alasan: {$cuti->alasan_cuti}\n\n"
            . "Mohon diproses melalui link berikut:\n"
            . route('cuti.show', $cuti) . "\n\n"
            . "Terima kasih.";
    }

    /**
     * Pesan WA final (disetujui/ditolak) untuk pegawai.
     */
    private function pesanFinalPegawai(PengajuanCuti $cuti, string $pengelola): string
    {
        $statusLabel = $cuti->status === 'disetujui' ? 'DISETUJUI' : 'DITOLAK';

        return "Assalamu'alaikum Wr. Wb.\n\n"
            . "Yth. Bapak/Ibu {$cuti->pegawai->nama}\n\n"
            . "Pengajuan cuti Anda dengan detail berikut:\n"
            . "- Jenis Cuti: {$cuti->jenisCuti->nama}\n"
            . "- Tanggal: " . $cuti->tanggal_mulai->format('d M Y') . " s.d. " . $cuti->tanggal_selesai->format('d M Y') . "\n"
            . "- Lama: {$cuti->lama_cuti_hari} hari\n\n"
            . "telah **{$statusLabel}** oleh {$pengelola}.\n\n"
            . "Link detail: " . route('cuti.show', $cuti);
    }

    /**
     * Cari atasan/pejabat berikutnya yang perlu approve
     */
    public function getNextApprover(PengajuanCuti $cuti): ?User
    {
        if ($cuti->status === 'diajukan') {
            return $cuti->atasanLangsungUser;
        }

        $role = match ($cuti->status) {
            'diproses_kasubag' => 'kasubag',
            'diproses_sekretaris' => 'sekretaris',
            'diproses_sekda' => 'sekda',
            'diproses_kepala_dinas' => 'kepala_dinas',
            'diproses_walikota' => 'walikota',
            default => null,
        };

        if (! $role) return null;

        return User::where('role', $role)->with('pegawai')->first();
    }

    /**
     * Kirim notifikasi Email ke atasan
     */
    public function kirimEmail(PengajuanCuti $cuti)
    {
        $nextApprover = $this->getNextApprover($cuti);

        if (! $nextApprover) {
            return back()->withErrors('Atasan tidak ditemukan.');
        }

        $email = $nextApprover->pegawai->email ?? $nextApprover->email ?? null;
        if (! $email) {
            return back()->withErrors('Email atasan tidak tersedia. Hubungi admin.');
        }

        $subjek = "Pengajuan Cuti - " . $cuti->pegawai->nama . " (" . $cuti->jenisCuti->nama . ")";
        $body = "Assalamu'alaikum Wr. Wb.\n\n"
            . "Yth. Bapak/Ibu " . $nextApprover->nama . "\n\n"
            . "Saya " . $cuti->pegawai->nama . " mengajukan cuti dengan detail sebagai berikut:\n"
            . "- Jenis Cuti: " . $cuti->jenisCuti->nama . "\n"
            . "- Tanggal: " . $cuti->tanggal_mulai->format('d M Y') . " s.d. " . $cuti->tanggal_selesai->format('d M Y') . "\n"
            . "- Lama: " . $cuti->lama_cuti_hari . " hari\n"
            . "- Alasan: " . $cuti->alasan_cuti . "\n\n"
            . "Bersama ini saya mohon persetujuan Bapak/Ibu.\n"
            . "Terima kasih.\n\n"
            . "Link detail: " . route('cuti.show', $cuti);

        try {
            \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($email, $subjek) {
                $message->to($email)
                    ->subject($subjek)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return back()->with('success', 'Email berhasil dikirim ke ' . $nextApprover->nama . '.');
        } catch (\Exception $e) {
            return back()->withErrors('Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
