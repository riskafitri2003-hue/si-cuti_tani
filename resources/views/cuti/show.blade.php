@extends('layouts.app')
@section('title', 'Formulir Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        @if(auth()->user()->canBeAtasanLangsung() && $cuti->status === 'diajukan' && $cuti->atasan_langsung_user_id == auth()->id())
            <a href="{{ route('cuti.atasan-langsung.form', $cuti) }}" class="btn btn-sm rounded-pill px-3" style="background:#0d6efd;color:#fff;">
                <i class="bi bi-pencil me-1"></i>Pertimbangan Atasan Langsung
            </a>
        @endif
        @if(auth()->user()->isKasubag() && $cuti->status === 'diproses_kasubag')
            <a href="{{ route('cuti.kasubag.form', $cuti) }}" class="btn btn-sm rounded-pill px-3" style="background:#0dcaf0;color:#fff;">
                <i class="bi bi-check-lg me-1"></i>Setujui Kasubag
            </a>
        @endif
        @if(auth()->user()->isSekretaris() && $cuti->status === 'diproses_sekretaris')
            <a href="{{ route('cuti.sekretaris.form', $cuti) }}" class="btn btn-sm rounded-pill px-3" style="background:#fd7e14;color:#fff;">
                <i class="bi bi-check-lg me-1"></i>Setujui Sekretaris
            </a>
        @endif
        @if(auth()->user()->isKepalaDinas() && $cuti->status === 'diproses_kepala_dinas')
            <a href="{{ route('cuti.kepala-dinas.form', $cuti) }}" class="btn btn-sm rounded-pill px-3" style="background:#198754;color:#fff;">
                <i class="bi bi-check2-square me-1"></i>Putuskan Kepala Dinas
            </a>
        @endif
        @if(auth()->user()->isSekda() && $cuti->status === 'diproses_sekda')
            <a href="{{ route('cuti.sekda.form', $cuti) }}" class="btn btn-sm rounded-pill px-3" style="background:#3949ab;color:#fff;">
                <i class="bi bi-pen me-1"></i>Tanda Tangan Sekretaris Daerah
            </a>
        @endif
        @if(auth()->user()->isWalikota() && $cuti->status === 'diproses_walikota')
            <a href="{{ route('cuti.walikota.form', $cuti) }}" class="btn btn-sm rounded-pill px-3" style="background:#6f42c1;color:#fff;">
                <i class="bi bi-pen me-1"></i>Tanda Tangan Wali Kota
            </a>
        @endif
        @if($cuti->pegawai->wa)
            <a href="https://wa.me/{{ $cuti->pegawai->wa }}?text={{ urlencode('Notifikasi Cuti: Pengajuan cuti Anda (' . $cuti->jenisCuti->nama . ', ' . $cuti->lama_cuti_hari . ' hari) sedang dalam proses. Status: ' . $cuti->status) }}" target="_blank" class="btn btn-sm rounded-pill px-3" style="background:#25d366;color:#fff;">
                <i class="bi bi-whatsapp me-1"></i>Kirim WA
            </a>
        @endif
        @if(auth()->user()->isAdmin())
            <form action="{{ route('cuti.destroy', $cuti) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Hapus pengajuan cuti ini? Saldo cuti pegawai akan dikembalikan.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </form>
        @endif
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
    </div>
</div>

{{-- KIRIM PERSETUJUAN KE ATASAN --}}
@php
    $isOwner = auth()->user()->isPegawai() && auth()->user()->nip === $cuti->nip;
    $isAdmin = auth()->user()->isAdmin();
    $statusProses = !in_array($cuti->status, ['disetujui', 'ditolak']);
    $canSend = $statusProses && $nextApprover;
    $nextWa = $nextApprover->pegawai->wa ?? $nextApprover->wa ?? null;
    $nextEmail = $nextApprover->pegawai->email ?? $nextApprover->email ?? null;
@endphp

@if($canSend)
@php
    $pesanWA = "Assalamu'alaikum Wr. Wb." . "\n\n"
        . "Yth. Bapak/Ibu " . $nextApprover->nama . "\n\n"
        . "Saya " . $cuti->pegawai->nama . " mengajukan cuti dengan detail sebagai berikut:" . "\n"
        . "- Jenis Cuti: " . $cuti->jenisCuti->nama . "\n"
        . "- Tanggal: " . $cuti->tanggal_mulai->format('d M Y') . " s.d. " . $cuti->tanggal_selesai->format('d M Y') . "\n"
        . "- Lama: " . $cuti->lama_cuti_hari . " hari\n"
        . "- Alasan: " . $cuti->alasan_cuti . "\n\n"
        . "Bersama ini saya mohon persetujuan Bapak/Ibu.\n"
        . "Terima kasih.\n\n"
        . "Link detail: " . route('cuti.show', $cuti);

    $subjekEmail = "Pengajuan Cuti - " . $cuti->pegawai->nama . " (" . $cuti->jenisCuti->nama . ")";
    $bodyEmail = "Assalamu'alaikum Wr. Wb.%0D%0A%0D%0A"
        . "Yth. Bapak/Ibu " . urlencode($nextApprover->nama) . "%0D%0A%0D%0A"
        . "Saya " . urlencode($cuti->pegawai->nama) . " mengajukan cuti dengan detail sebagai berikut:%0D%0A"
        . "- Jenis Cuti: " . urlencode($cuti->jenisCuti->nama) . "%0D%0A"
        . "- Tanggal: " . $cuti->tanggal_mulai->format('d M Y') . " s.d. " . $cuti->tanggal_selesai->format('d M Y') . "%0D%0A"
        . "- Lama: " . $cuti->lama_cuti_hari . " hari%0D%0A"
        . "- Alasan: " . urlencode($cuti->alasan_cuti) . "%0D%0A%0D%0A"
        . "Bersama ini saya mohon persetujuan Bapak/Ibu.%0D%0A"
        . "Terima kasih.%0D%0A%0D%0A"
        . "Link detail: " . urlencode(route('cuti.show', $cuti));
@endphp
<div class="card mb-3 no-print" style="border:2px solid #25d366;border-radius:12px;">
    <div class="card-body">
        <h6 class="mb-3" style="color:#25d366;"><i class="bi bi-send me-1"></i>Kirim Persetujuan ke Atasan</h6>
        <div class="row align-items-center">
            <div class="col-md-7">
                <p class="mb-1 small">
                    Saat ini pengajuan menunggu persetujuan:
                </p>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill" style="background:#1a237e;font-size:0.85rem;">
                        <i class="bi bi-person-badge me-1"></i>{{ ucfirst(str_replace('_', ' ', $cuti->status === 'diajukan' ? 'atasan_langsung' : str_replace('diproses_', '', $cuti->status))) }}
                    </span>
                    <strong>{{ $nextApprover->nama }}</strong>
                </div>
                @if($nextWa)
                    <div class="small text-muted mb-1"><i class="bi bi-whatsapp me-1 text-success"></i>WA: {{ $nextWa }}</div>
                @endif
                @if($nextEmail)
                    <div class="small text-muted"><i class="bi bi-envelope me-1 text-primary"></i>Email: {{ $nextEmail }}</div>
                @endif
            </div>
            <div class="col-md-5 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    @if($nextWa)
                        <a href="https://wa.me/{{ $nextWa }}?text={{ urlencode($pesanWA) }}" target="_blank" class="btn rounded-pill px-4" style="background:#25d366;color:#fff;">
                            <i class="bi bi-whatsapp me-1"></i>Kirim via WhatsApp
                        </a>
                    @endif
                    @if($nextEmail)
                        <form action="{{ route('cuti.kirim-email', $cuti) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-envelope me-1"></i>Kirim via Email
                            </button>
                        </form>
                    @endif
                </div>
                @if(!$nextWa && !$nextEmail)
                    <div class="text-muted small"><i class="bi bi-exclamation-triangle me-1"></i>Atasan belum memiliki kontak WA/Email. Hubungi admin.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- FORM SARAN/MASUKAN PENGAJU --}}
@php
    $isOwnerSaran = auth()->user()->isAdmin() || (auth()->user()->isPegawai() && auth()->user()->nip === $cuti->nip);
    $sudahSaran = $cuti->saran ? true : false;
@endphp

@if($isOwnerSaran)
<div class="card mb-3 no-print" style="border:2px solid #ffc107;border-radius:12px;">
    <div class="card-body">
        <h6 class="mb-1" style="color:#b45309;"><i class="bi bi-lightbulb me-1"></i>Form Saran / Masukan</h6>
        <p class="small text-muted mb-3">Beri tahu kami kendala yang Anda alami saat menggunakan aplikasi.</p>

        @if($sudahSaran)
            <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle-fill me-1"></i> Terima kasih atas saran dan masukannya.
            </div>
        @else
            <form method="POST" action="{{ route('cuti.saran.store', $cuti) }}">
                @csrf
                <div class="mb-3">
                    <div class="fw-medium small mb-1">1. Pengguna kesulitan memahami menu aplikasi.</div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="kesulitan_menu" value="1" id="km-ya">
                        <label class="form-check-label" for="km-ya">Ya</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="kesulitan_menu" value="0" id="km-tidak" checked>
                        <label class="form-check-label" for="km-tidak">Tidak</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="fw-medium small mb-1">2. Pengajuan cuti gagal dikirim.</div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pengajuan_gagal" value="1" id="pg-ya">
                        <label class="form-check-label" for="pg-ya">Ya</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pengajuan_gagal" value="0" id="pg-tidak" checked>
                        <label class="form-check-label" for="pg-tidak">Tidak</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="fw-medium small mb-1">3. File pendukung tidak dapat diunggah.</div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="file_gagal" value="1" id="fg-ya">
                        <label class="form-check-label" for="fg-ya">Ya</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="file_gagal" value="0" id="fg-tidak" checked>
                        <label class="form-check-label" for="fg-tidak">Tidak</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="fw-medium small mb-1">4. Halaman aplikasi lambat dibuka.</div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="halaman_lambat" value="1" id="hl-ya">
                        <label class="form-check-label" for="hl-ya">Ya</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="halaman_lambat" value="0" id="hl-tidak" checked>
                        <label class="form-check-label" for="hl-tidak">Tidak</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="s-saran">Saran</label>
                    <textarea name="saran" id="s-saran" rows="3" class="form-control" placeholder="Tuliskan saran Anda di sini (opsional)"></textarea>
                </div>
                <button type="submit" class="btn rounded-pill px-4" style="background:#ffc107;color:#111;">
                    <i class="bi bi-send me-1"></i>Kirim Saran
                </button>
            </form>
        @endif
    </div>
</div>
@endif

<div class="card">
    <div class="card-body" style="font-size:14px;">
        <div class="text-end mb-3" style="color:#000;">
            <div style="font-weight:500;">Bukittinggi, {{ strtolower($cuti->tanggal_pengajuan?->format('d F Y')) }}</div>
            <div>Yth. Bapak./Ibu pimpinan Kepala Dinas Pertanian dan Pangan Kota Bukittinggi</div>
            <div>di Bukittinggi</div>
        </div>

        <h5 class="text-center fw-bold mb-1" style="color:#000;">
            FORMULIR PERMINTAAN DAN PEMBERIAN CUTI
        </h5>
        <div class="text-center mb-3" style="color:#000;">
            No. {{ $cuti->nomor_formulir ? $cuti->nomor_formulir . '/' : '____/____/' }}Umum/DPP/{{ date('Y') }}
        </div>

        {{-- I. DATA PEGAWAI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="section-title" colspan="4">I. DATA PEGAWAI</th></tr>
            <tr>
                <td style="width:12%;font-weight:600;">Nama</td><td style="width:38%;">{{ $cuti->pegawai->nama }}</td>
                <td style="width:12%;font-weight:600;">NIP</td><td>{{ $cuti->pegawai->nip }}</td>
            </tr>
            <tr>
                <td style="font-weight:600;">Jabatan</td><td>{{ $cuti->pegawai->jabatan }}</td>
                <td style="font-weight:600;">Masa Kerja</td><td>{{ $cuti->pegawai->masa_kerja }}</td>
            </tr>
            <tr>
                <td style="font-weight:600;">Unit Kerja</td><td colspan="3">{{ $cuti->pegawai->unit_kerja }}</td>
            </tr>
            @if($cuti->pegawai->email || $cuti->pegawai->wa)
            <tr class="no-print">
                <td style="font-weight:600;">Email</td><td>{{ $cuti->pegawai->email ?? '-' }}</td>
                <td style="font-weight:600;">WhatsApp</td><td>
                    @if($cuti->pegawai->wa)
                        <a href="https://wa.me/{{ $cuti->pegawai->wa }}" target="_blank" class="text-decoration-none">{{ $cuti->pegawai->wa }}</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endif
        </table>

        {{-- II. JENIS CUTI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="section-title" colspan="2">II. JENIS CUTI YANG DIAMBIL</th></tr>
            <tr>
                <td>1. Cuti Tahunan @if($cuti->jenisCuti->kode == 1) ✔ @else ☐ @endif</td>
                <td>2. Cuti Besar @if($cuti->jenisCuti->kode == 2) ✔ @else ☐ @endif</td>
            </tr>
            <tr>
                <td>3. Cuti Sakit @if($cuti->jenisCuti->kode == 3) ✔ @else ☐ @endif</td>
                <td>4. Cuti Melahirkan @if($cuti->jenisCuti->kode == 4) ✔ @else ☐ @endif</td>
            </tr>
            <tr>
                <td>5. Cuti Karena Alasan Penting @if($cuti->jenisCuti->kode == 5) ✔ @else ☐ @endif</td>
                <td>6. Cuti di Luar Tanggungan Negara @if($cuti->jenisCuti->kode == 6) ✔ @else ☐ @endif</td>
            </tr>
            <tr>
                <td>7. Cuti Haji/Umroh @if($cuti->jenisCuti->kode == 7) ✔ @else ☐ @endif</td>
                <td></td>
            </tr>
        </table>

        {{-- III. ALASAN CUTI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="section-title">III. ALASAN CUTI</th></tr>
            <tr><td>{{ $cuti->alasan_cuti }}</td></tr>
        </table>

        {{-- IV. LAMANYA CUTI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="section-title" colspan="4">IV. LAMANYA CUTI</th></tr>
            <tr>
                <td style="font-weight:600;">SELAMA</td><td>{{ $cuti->lama_cuti_hari }} hari</td>
                <td style="font-weight:600;">MULAI TANGGAL</td><td>{{ $cuti->tanggal_mulai->format('d M Y') }} S.D. {{ $cuti->tanggal_selesai->format('d M Y') }}</td>
            </tr>
        </table>

        {{-- V. CATATAN CUTI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="section-title" colspan="4">V. CATATAN CUTI</th></tr>
            <tr><th>TAHUN</th><th>SISA</th><th>KETERANGAN</th><th>TERPOTONG PENGAJUAN INI</th></tr>
            @php $s = $cuti->pegawai->saldoCutis->first(); @endphp
            @if($s)
            <tr>
                <td>N-2</td>
                <td>{{ $s->saldo_n2 }}</td>
                <td>{{ $s->keterangan_n2 ?? '-' }}</td>
                <td>{{ $cuti->potongan_saldo_n2 }} hari</td>
            </tr>
            <tr>
                <td>N-1</td>
                <td>{{ $s->saldo_n1 }}</td>
                <td>{{ $s->keterangan_n1 ?? '-' }}</td>
                <td>{{ $cuti->potongan_saldo_n1 }} hari</td>
            </tr>
            <tr>
                <td>N</td>
                <td>{{ $s->saldo_n }}</td>
                <td>{{ $s->keterangan_n ?? '-' }}</td>
                <td>{{ $cuti->potongan_saldo_n }} hari</td>
            </tr>
            @endif
        </table>

        {{-- VI. ALAMAT --}}
        <table class="table table-bordered mb-3">
            <tr><th class="section-title" colspan="3">VI. ALAMAT SELAMA MENJALANKAN CUTI</th></tr>
            <tr>
                <td style="width:15%;font-weight:600;">Alamat</td>
                <td style="width:15%;font-weight:600;">TELP.</td>
                <td>
                    {{ $cuti->alamat_selama_cuti ?? '-' }}<br>
                    {{ $cuti->telpon_selama_cuti ? 'No. Telp: ' . $cuti->telpon_selama_cuti : '' }}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div style="max-width:340px;margin-left:auto;">
                        <div class="text-center mb-1">Hormat saya,</div>
                        <div class="text-center" style="height:70px;">
                            @if($cuti->tanda_tangan_pegawai)
                                <img src="{{ asset('storage/' . $cuti->tanda_tangan_pegawai) }}" alt="Tanda Tangan Pegawai" style="max-width:150px;">
                            @endif
                        </div>
                        <div class="text-center fw-semibold">{{ $cuti->pegawai->nama }}</div>
                        <div class="text-center small">NIP. {{ $cuti->pegawai->nip }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- DOKUMEN PENDUKUNG (lampiran, tidak bernomor bagian) --}}
        @if($cuti->dokumen_pendukung)
        <table class="table table-bordered mb-3" style="margin-top:12px;">
            <tr><th class="bg-black text-white"><i class="bi bi-paperclip me-1"></i>DOKUMEN PENDUKUNG</th></tr>
            <tr>
                <td>
                    <a href="{{ route('cuti.dokumen', $cuti) }}" class="text-decoration-none">
                        <i class="bi bi-file-earmark me-1"></i>{{ basename($cuti->dokumen_pendukung) }}
                    </a>
                </td>
            </tr>
        </table>
        @endif

        {{-- VII. PERTIMBANGAN ATASAN LANGSUNG & VIII. KEPUTUSAN PEJABAT YANG BERWENANG --}}
        @unless($cuti->isKepalaDinasApplicant())
        @php
            $saldoRow = $cuti->pegawai->saldoCutis->first();
            $sSaldo = $saldoRow ? ((int)$saldoRow->saldo_n2 + (int)$saldoRow->saldo_n1 + (int)$saldoRow->saldo_n) : 0;
        @endphp
        <table class="table table-bordered mb-3">
            <tr><th class="section-title" colspan="4">VII. PERTIMBANGAN ATASAN LANGSUNG</th></tr>
            <tr>
                <th class="text-center">DISETUJUI</th>
                <th class="text-center">PERUBAHAN</th>
                <th class="text-center">DITANGGUHKAN</th>
                <th class="text-center">TIDAK DISETUJUI</th>
            </tr>
            <tr>
                <td class="text-center">{{ $cuti->atasan_langsung_disetujui_hari !== null ? $cuti->atasan_langsung_disetujui_hari . ' hari' : '-' }}</td>
                <td class="text-center">{{ $cuti->atasan_langsung_perubahan_hari !== null ? $cuti->atasan_langsung_perubahan_hari . ' hari' : '-' }}</td>
                <td class="text-center">{{ $sSaldo }} hari</td>
                <td class="text-center">{{ $cuti->atasan_langsung_tidak_disetujui_hari !== null ? $cuti->atasan_langsung_tidak_disetujui_hari . ' hari' : '-' }}</td>
            </tr>
        </table>

        <div style="max-width:340px;margin-left:auto;" class="mb-4">
            <div class="text-center mb-1">ATASAN LANGSUNG</div>
            <div class="text-center" style="height:70px;">
                @if($cuti->tanda_tangan_atasan_langsung)
                    <img src="{{ asset('storage/'.$cuti->tanda_tangan_atasan_langsung) }}" alt="Tanda Tangan Atasan Langsung" style="max-width:150px;max-height:60px;">
                @endif
            </div>
            <div class="text-center fw-semibold">{{ $cuti->nama_atasan_langsung ?? 'Nama atasan langsung' }}</div>
            <div class="text-center small">NIP. {{ $cuti->nip_atasan_langsung ?? '..............................' }}</div>
        </div>

        <table class="table table-bordered mb-3">
            <tr><th class="section-title" colspan="4">VIII. KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI</th></tr>
            <tr>
                <th class="text-center">DISETUJUI</th>
                <th class="text-center">PERUBAHAN</th>
                <th class="text-center">DITANGGUHKAN</th>
                <th class="text-center">TIDAK DISETUJUI</th>
            </tr>
            <tr>
                <td class="text-center">{{ $cuti->kepala_dinas_disetujui_hari !== null ? $cuti->kepala_dinas_disetujui_hari . ' hari' : '-' }}</td>
                <td class="text-center">{{ $cuti->kepala_dinas_perubahan_hari !== null ? $cuti->kepala_dinas_perubahan_hari . ' hari' : '-' }}</td>
                <td class="text-center">{{ $sSaldo }} hari</td>
                <td class="text-center">{{ $cuti->kepala_dinas_tidak_disetujui_hari !== null ? $cuti->kepala_dinas_tidak_disetujui_hari . ' hari' : '-' }}</td>
            </tr>
        </table>

        <div style="max-width:340px;margin-left:auto;" class="mb-4">
            <div class="text-center mb-1">KEPALA DINAS PERTANIAN DAN PANGAN</div>
            <div class="text-center" style="height:70px;">
                @if($cuti->tanda_tangan_kepala_dinas)
                    <img src="{{ asset('storage/'.$cuti->tanda_tangan_kepala_dinas) }}" alt="Tanda Tangan Kepala Dinas" style="max-width:150px;max-height:60px;">
                @endif
            </div>
            <div class="text-center fw-semibold">{{ $cuti->nama_kepala_dinas ?? 'Nama kepala dinas' }}</div>
            <div class="text-center small">NIP. {{ $cuti->nip_kepala_dinas ?? '..............................' }}</div>
            @if($cuti->nomor_surat)
                <div class="text-center small mt-1">Nomor: <strong>{{ $cuti->nomor_surat }}</strong></div>
            @endif
        </div>
        @endunless

        {{-- VII-D.1. TANDA TANGAN SEKRETARIS DAERAH (pengaju Kepala Dinas) --}}
        @if($cuti->isKepalaDinasApplicant())
        <div class="section-sekda mb-3 bg-white" style="border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
            <div class="section-header" style="background:#000;color:#fff;">
                <i class="bi bi-pen me-1"></i>VII-D. TANDA TANGAN SEKRETARIS DAERAH
            </div>
            <div class="section-body">
                @if($cuti->nama_sekda)
                <table class="table table-sm table-bordered mb-2">
                    <tr><th>Keputusan</th><th>Paraf</th><th>Tanggal</th></tr>
                    <tr>
                        <td>
                            @if($cuti->status_sekda === 'disetujui')
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Ditandatangani</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($cuti->status_sekda === 'disetujui')
                                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#000;"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cuti->tanggal_sekda?->format('d M Y') ?? '-' }}</td>
                    </tr>
                </table>
                <div class="small">
                    <span class="badge" style="background:#000;color:#fff;">{{ $cuti->status_sekda }}</span>
                    <strong>{{ $cuti->nama_sekda }}</strong>
                    &mdash; NIP. {{ $cuti->nip_sekda ?? '-' }}
                </div>
                @if($cuti->tanda_tangan_sekda)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $cuti->tanda_tangan_sekda) }}" alt="Tanda Tangan Sekretaris Daerah" style="max-width:180px;border:1px solid #ddd;border-radius:6px;padding:4px;background:#fff;">
                </div>
                @endif
                @else
                <div class="text-muted small"><i class="bi bi-clock me-1"></i>Menunggu tanda tangan Sekretaris Daerah</div>
                @endif
            </div>
        </div>
        @endif

        {{-- VII-E. TANDA TANGAN WALIKOTA (cuti besar/haji/umroh & pengaju Kepala Dinas) --}}
        @if($cuti->needsWalikota() || $cuti->isKepalaDinasApplicant())
        <div class="section-walikota mb-3 bg-white" style="border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
            <div class="section-header" style="background:#000;color:#fff;">
                <i class="bi bi-pen me-1"></i>VII-E. TANDA TANGAN WALIKOTA BUKITTINGGI
            </div>
            <div class="section-body">
                @if($cuti->nama_walikota)
                <table class="table table-sm table-bordered mb-2">
                    <tr><th>Keputusan</th><th>Paraf</th><th>Tanggal</th></tr>
                    <tr>
                        <td>
                            @if($cuti->status_walikota === 'disetujui')
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Ditandatangani</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($cuti->status_walikota === 'disetujui')
                                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#000;"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cuti->tanggal_walikota?->format('d M Y') ?? '-' }}</td>
                    </tr>
                </table>
                <div class="small">
                    <span class="badge" style="background:#000;color:#fff;">{{ $cuti->status_walikota }}</span>
                    <strong>{{ $cuti->nama_walikota }}</strong>
                    &mdash; NIP. {{ $cuti->nip_walikota ?? '-' }}
                </div>
                @if($cuti->tanda_tangan_walikota)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $cuti->tanda_tangan_walikota) }}" alt="Tanda Tangan Wali Kota" style="max-width:180px;border:1px solid #ddd;border-radius:6px;padding:4px;background:#fff;">
                </div>
                @endif
                @else
                <div class="text-muted small"><i class="bi bi-clock me-1"></i>Menunggu tanda tangan Wali Kota Bukittinggi</div>
                @endif
            </div>
        </div>
        @endif

        {{-- KU/S PENGESAHAN TAHAP INTERN --}}
        @if($cuti->status === 'disetujui')
        <div class="text-center mt-1 small">
            @if($cuti->status_kasubag === 'disetujui')
                <span class="fw-bold">✔ KU</span> Kasubag Umum
            @endif
            @if($cuti->status_kasubag === 'disetujui' && $cuti->status_sekretaris === 'disetujui')
                &nbsp;&nbsp;|&nbsp;&nbsp;
            @endif
            @if($cuti->status_sekretaris === 'disetujui')
                <span class="fw-bold">✔ S</span> Sekretaris
            @endif
        </div>
        @endif

    </div>
</div>

<style>
    .section-title { background:#D9EAF7 !important; color:#000 !important; }
    .ttd-cell { vertical-align: top; }
    .ttd-img { height: 60px; display: flex; align-items: flex-end; justify-content: center; }
    .ttd-img img { max-width: 110px; max-height: 60px; }
    .badge-atasan-langsung, .badge-kepala-dinas { background:#000; color:#fff; }
    .section-body .text-success, .section-body .text-danger { color:#000; }
    .section-sekda, .section-walikota { border-left-color:#000; }

    @media print {
        @page { size: A4; margin: 5mm; }
        body { background: #fff; }
        .no-print { display: none !important; }
        .container { max-width: 100% !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; font-size: 9px !important; color: #000 !important; }
        .card-body h5 { font-size: 12px !important; margin-bottom: 2px !important; }
        .card-body h6 { font-size: 11px !important; }
        .table { font-size: 9px !important; margin-bottom: 3px !important; }
        .table th, .table td { padding: 2px 4px !important; }
        .mb-1, .mb-2 { margin-bottom: 2px !important; }
        .mb-3 { margin-bottom: 3px !important; }
        .mb-4 { margin-bottom: 3px !important; }
        .mt-1, .mt-2, .mt-3, .mt-4 { margin-top: 2px !important; }
        .section-header { padding: 1px 8px !important; font-size: 10px !important; }
        .section-body { padding: 3px 8px !important; }
        .card-body img { max-width: 70px !important; }
        .ttd-img { height: 45px !important; }
        .ttd-img img { max-width: 70px !important; max-height: 45px !important; }
        .card-body div[style*="width:280px"] { width: 180px !important; }
        .section-body .text-success, .section-body .text-danger, .section-body .text-muted { color: #000 !important; }
        .badge, .badge[style] { background: #000 !important; color: #fff !important; }
        .section-header[style] { background: #000 !important; color: #fff !important; }
    }
</style>
@endsection
