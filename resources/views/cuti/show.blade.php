@extends('layouts.app')
@section('title', 'Formulir Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        @if(auth()->user()->isAtasanLangsung() && $cuti->status === 'diajukan' && $cuti->atasan_langsung_user_id == auth()->id())
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
        @if(auth()->user()->isWalikota() && $cuti->status === 'diproses_walikota')
            <a href="{{ route('cuti.walikota.form', $cuti) }}" class="btn btn-sm rounded-pill px-3" style="background:#6f42c1;color:#fff;">
                <i class="bi bi-check2-square me-1"></i>Putuskan Walikota
            </a>
        @endif
        @if($cuti->pegawai->wa && auth()->user()->isAdmin())
            <a href="https://wa.me/{{ $cuti->pegawai->wa }}?text={{ urlencode('Notifikasi Cuti: Pengajuan cuti Anda (' . $cuti->jenisCuti->nama . ', ' . $cuti->lama_cuti_hari . ' hari) sedang dalam proses. Status: ' . $cuti->status) }}" target="_blank" class="btn btn-sm rounded-pill px-3" style="background:#25d366;color:#fff;">
                <i class="bi bi-whatsapp me-1"></i>Kirim WA
            </a>
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
    $canSend = ($isOwner || $isAdmin) && $statusProses && $nextApprover;
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
        <div class="text-end mb-3">
            <div style="color:#1a237e;font-weight:500;">Bukittinggi, {{ $cuti->tanggal_pengajuan?->format('d F Y') }}</div>
            <small>Kepada Yth. Bpk./Ibu Pimpinan {{ $cuti->pegawai->unit_kerja }}</small>
        </div>

        <h5 class="text-center fw-bold mb-4" style="color:#1a237e;">
            <i class="bi bi-file-earmark-text me-1"></i>FORMULIR PERMINTAAN DAN PEMBERIAN CUTI
        </h5>

        {{-- I. DATA PEGAWAI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="bg-primary text-white" colspan="4"><i class="bi bi-person me-1"></i>I. DATA PEGAWAI</th></tr>
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
            <tr><th class="bg-primary text-white" colspan="2"><i class="bi bi-bookmark me-1"></i>II. JENIS CUTI YANG DIAMBIL</th></tr>
            <tr>
                <td>1. Cuti Tahunan {{ $cuti->jenisCuti->kode == 1 ? '✔' : '' }}</td>
                <td>2. Cuti Besar {{ $cuti->jenisCuti->kode == 2 ? '✔' : '' }}</td>
            </tr>
            <tr>
                <td>3. Cuti Sakit {{ $cuti->jenisCuti->kode == 3 ? '✔' : '' }}</td>
                <td>4. Cuti Melahirkan {{ $cuti->jenisCuti->kode == 4 ? '✔' : '' }}</td>
            </tr>
            <tr>
                <td>5. Cuti Alasan Penting {{ $cuti->jenisCuti->kode == 5 ? '✔' : '' }}</td>
                <td>6. Cuti Di Luar Tanggungan Negara {{ $cuti->jenisCuti->kode == 6 ? '✔' : '' }}</td>
            </tr>
            <tr>
                <td colspan="2">7. Cuti Haji/Umroh {{ $cuti->jenisCuti->kode == 7 ? '✔' : '' }}</td>
            </tr>
        </table>

        {{-- III. ALASAN CUTI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="bg-primary text-white"><i class="bi bi-chat-dots me-1"></i>III. ALASAN CUTI</th></tr>
            <tr><td>{{ $cuti->alasan_cuti }}</td></tr>
        </table>

        {{-- IV. LAMANYA CUTI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="bg-primary text-white" colspan="4"><i class="bi bi-calendar-range me-1"></i>IV. LAMANYA CUTI</th></tr>
            <tr>
                <td style="font-weight:600;">SELAMA</td><td>{{ $cuti->lama_cuti_hari }} hari</td>
                <td style="font-weight:600;">MULAI TANGGAL</td><td>{{ $cuti->tanggal_mulai->format('d M Y') }} S.D. {{ $cuti->tanggal_selesai->format('d M Y') }}</td>
            </tr>
        </table>

        {{-- V. CATATAN CUTI --}}
        <table class="table table-bordered mb-3">
            <tr><th class="bg-primary text-white" colspan="3"><i class="bi bi-journal me-1"></i>V. CATATAN CUTI</th></tr>
            <tr><th>TAHUN</th><th>SISA</th><th>KETERANGAN</th></tr>
            @php $s = $cuti->pegawai->saldoCutis->first(); @endphp
            @if($s)
            <tr>
                <td>N-2</td>
                <td>{{ $s->saldo_n2 }}</td>
                <td>{{ $s->keterangan_n2 ?? '-' }}</td>
            </tr>
            <tr>
                <td>N-1</td>
                <td>{{ $s->saldo_n1 }}</td>
                <td>{{ $s->keterangan_n1 ?? '-' }}</td>
            </tr>
            <tr>
                <td>N</td>
                <td>{{ $s->saldo_n }}</td>
                <td>{{ $s->keterangan_n ?? '-' }}</td>
            </tr>
            @endif
        </table>

        {{-- VI. ALAMAT --}}
        <table class="table table-bordered mb-3">
            <tr><th class="bg-primary text-white" colspan="2"><i class="bi bi-geo-alt me-1"></i>VI. ALAMAT SELAMA MENJALANKAN CUTI</th></tr>
            <tr>
                <td>{{ $cuti->alamat_selama_cuti ?? '-' }}</td>
                <td style="width:30%;">Telpon: {{ $cuti->telpon_selama_cuti ?? '-' }}</td>
            </tr>
        </table>

        {{-- DOKUMEN PENDUKUNG --}}
        @if($cuti->dokumen_pendukung)
        <table class="table table-bordered mb-3">
            <tr><th class="bg-primary text-white"><i class="bi bi-paperclip me-1"></i>VII. DOKUMEN PENDUKUNG</th></tr>
            <tr>
                <td>
                    <a href="{{ route('cuti.dokumen', $cuti) }}" class="text-decoration-none">
                        <i class="bi bi-file-earmark me-1"></i>{{ basename($cuti->dokumen_pendukung) }}
                    </a>
                </td>
            </tr>
        </table>
        @endif

        {{-- TANDA TANGAN PEGAWAI --}}
        @if($cuti->tanda_tangan_pegawai)
        <div class="mb-3">
            <strong style="color:#1a237e;"><i class="bi bi-pen me-1"></i>VIII. TANDA TANGAN PEGAWAI</strong>
            <div class="mt-2">
                <img src="{{ asset('storage/' . $cuti->tanda_tangan_pegawai) }}" alt="Tanda Tangan Pegawai" style="max-width:200px;border:1px solid #ddd;border-radius:6px;padding:4px;background:#fff;">
                <div class="small text-muted mt-1">{{ $cuti->pegawai->nama }}</div>
            </div>
        </div>
        @endif

        {{-- VII-A. ATASAN LANGSUNG --}}
        <div class="section-atasan-langsung mb-3 bg-white" style="border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
            <div class="section-header" style="background:#e3f2fd;color:#0d6efd;">
                <i class="bi bi-person-check me-1"></i>VII-A. PERTIMBANGAN ATASAN LANGSUNG
            </div>
            <div class="section-body">
                @if($cuti->atasanLangsungUser)
                <div class="mb-2 small"><i class="bi bi-person-badge me-1"></i>Atasan Langsung: <strong>{{ $cuti->atasanLangsungUser->nama }}</strong></div>
                @endif
                @if($cuti->nama_atasan_langsung)
                <table class="table table-sm table-bordered mb-2">
                    <tr><th>Keputusan</th><th>Paraf</th><th>Catatan</th></tr>
                    <tr>
                        <td>
                            @if($cuti->status_atasan_langsung === 'disetujui')
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                            @elseif($cuti->status_atasan_langsung === 'tidak_disetujui')
                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Tidak Disetujui</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array($cuti->status_atasan_langsung, ['disetujui']))
                                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#198754;"></i>
                            @elseif($cuti->status_atasan_langsung === 'tidak_disetujui')
                                <i class="bi bi-x-circle-fill" style="font-size:1.3rem;color:#dc3545;"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cuti->catatan_atasan_langsung ?? '-' }}</td>
                    </tr>
                </table>
                <div class="small">
                    <span class="badge badge-atasan-langsung">{{ $cuti->status_atasan_langsung }}</span>
                    <strong>{{ $cuti->nama_atasan_langsung }}</strong>
                    &mdash; NIP. {{ $cuti->nip_atasan_langsung ?? '-' }}
                    <span class="text-muted">({{ $cuti->tanggal_atasan_langsung?->format('d M Y') }})</span>
                </div>
                @else
                <div class="text-muted small"><i class="bi bi-clock me-1"></i>Menunggu pertimbangan Atasan Langsung</div>
                @endif
            </div>
        </div>

        {{-- VII-B. KASUBAG --}}
        <div class="section-kasubag mb-3 bg-white" style="border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
            <div class="section-header" style="background:#e8f8ff;color:#0dcaf0;">
                <i class="bi bi-person-check me-1"></i>VII-B. PERSETUJUAN KASUBAG UMUM
            </div>
            <div class="section-body">
                @if($cuti->nama_kasubag)
                <table class="table table-sm table-bordered mb-2">
                    <tr><th>Keputusan</th><th>Paraf</th><th>Catatan</th></tr>
                    <tr>
                        <td>
                            @if($cuti->status_kasubag === 'disetujui')
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                            @elseif($cuti->status_kasubag === 'tidak_disetujui')
                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Tidak Disetujui</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array($cuti->status_kasubag, ['disetujui']))
                                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#198754;"></i>
                            @elseif($cuti->status_kasubag === 'tidak_disetujui')
                                <i class="bi bi-x-circle-fill" style="font-size:1.3rem;color:#dc3545;"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cuti->catatan_kasubag ?? '-' }}</td>
                    </tr>
                </table>
                <div class="small">
                    <span class="badge badge-kasubag">{{ $cuti->status_kasubag }}</span>
                    <strong>{{ $cuti->nama_kasubag }}</strong>
                    &mdash; NIP. {{ $cuti->nip_kasubag ?? '-' }}
                    <span class="text-muted">({{ $cuti->tanggal_kasubag?->format('d M Y') }})</span>
                </div>
                @else
                <div class="text-muted small"><i class="bi bi-clock me-1"></i>Menunggu persetujuan Kasubag Umum</div>
                @endif
            </div>
        </div>

        {{-- VII-C. SEKRETARIS --}}
        <div class="section-sekretaris mb-3 bg-white" style="border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
            <div class="section-header" style="background:#fff3e0;color:#fd7e14;">
                <i class="bi bi-person-check me-1"></i>VII-C. PERSETUJUAN SEKRETARIS
            </div>
            <div class="section-body">
                @if($cuti->nama_sekretaris)
                <table class="table table-sm table-bordered mb-2">
                    <tr><th>Keputusan</th><th>Paraf</th><th>Catatan</th></tr>
                    <tr>
                        <td>
                            @if($cuti->status_sekretaris === 'disetujui')
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                            @elseif($cuti->status_sekretaris === 'tidak_disetujui')
                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Tidak Disetujui</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array($cuti->status_sekretaris, ['disetujui']))
                                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#198754;"></i>
                            @elseif($cuti->status_sekretaris === 'tidak_disetujui')
                                <i class="bi bi-x-circle-fill" style="font-size:1.3rem;color:#dc3545;"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cuti->catatan_sekretaris ?? '-' }}</td>
                    </tr>
                </table>
                <div class="small">
                    <span class="badge badge-sekretaris">{{ $cuti->status_sekretaris }}</span>
                    <strong>{{ $cuti->nama_sekretaris }}</strong>
                    &mdash; NIP. {{ $cuti->nip_sekretaris ?? '-' }}
                    <span class="text-muted">({{ $cuti->tanggal_sekretaris?->format('d M Y') }})</span>
                </div>
                @else
                <div class="text-muted small"><i class="bi bi-clock me-1"></i>Menunggu persetujuan Sekretaris</div>
                @endif
            </div>
        </div>

        {{-- VII-D. KEPALA DINAS --}}
        <div class="section-kepala-dinas mb-3 bg-white" style="border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
            <div class="section-header" style="background:#e8f5e9;color:#198754;">
                <i class="bi bi-check2-square me-1"></i>VII-D. KEPUTUSAN KEPALA DINAS
                @if($cuti->nomor_surat) <small>&mdash; Nomor {{ $cuti->nomor_surat }}</small> @endif
            </div>
            <div class="section-body">
                @if($cuti->nama_kepala_dinas)
                <table class="table table-sm table-bordered mb-2">
                    <tr><th>Keputusan</th><th>Paraf</th><th>Tanggal</th></tr>
                    <tr>
                        <td>
                            @if($cuti->status_kepala_dinas === 'disetujui')
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                            @elseif($cuti->status_kepala_dinas === 'tidak_disetujui')
                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Tidak Disetujui</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array($cuti->status_kepala_dinas, ['disetujui']))
                                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#198754;"></i>
                            @elseif($cuti->status_kepala_dinas === 'tidak_disetujui')
                                <i class="bi bi-x-circle-fill" style="font-size:1.3rem;color:#dc3545;"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cuti->tanggal_kepala_dinas?->format('d M Y') ?? '-' }}</td>
                    </tr>
                </table>
                <div class="small">
                    <span class="badge badge-kepala-dinas">{{ $cuti->status_kepala_dinas }}</span>
                    <strong>{{ $cuti->nama_kepala_dinas }}</strong>
                    &mdash; NIP. {{ $cuti->nip_kepala_dinas ?? '-' }}
                </div>
                @if($cuti->tanda_tangan_kepala_dinas && $cuti->status_kepala_dinas === 'disetujui')
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $cuti->tanda_tangan_kepala_dinas) }}" alt="Tanda Tangan Kepala Dinas" style="max-width:180px;border:1px solid #ddd;border-radius:6px;padding:4px;background:#fff;">
                </div>
                @endif
                @else
                <div class="text-muted small"><i class="bi bi-clock me-1"></i>Menunggu keputusan Kepala Dinas</div>
                @endif
            </div>
        </div>

        {{-- VII-E. WALIKOTA (khusus cuti besar/haji/umroh) --}}
        @if($cuti->needsWalikota())
        <div class="section-walikota mb-3 bg-white" style="border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
            <div class="section-header" style="background:#f3e5f5;color:#6f42c1;">
                <i class="bi bi-check2-square me-1"></i>VII-E. KEPUTUSAN WALIKOTA BUKITTINGGI
            </div>
            <div class="section-body">
                @if($cuti->nama_walikota)
                <table class="table table-sm table-bordered mb-2">
                    <tr><th>Keputusan</th><th>Paraf</th><th>Tanggal</th></tr>
                    <tr>
                        <td>
                            @if($cuti->status_walikota === 'disetujui')
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                            @elseif($cuti->status_walikota === 'tidak_disetujui')
                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Tidak Disetujui</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array($cuti->status_walikota, ['disetujui']))
                                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#198754;"></i>
                            @elseif($cuti->status_walikota === 'tidak_disetujui')
                                <i class="bi bi-x-circle-fill" style="font-size:1.3rem;color:#dc3545;"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cuti->tanggal_walikota?->format('d M Y') ?? '-' }}</td>
                    </tr>
                </table>
                <div class="small">
                    <span class="badge" style="background:#6f42c1;color:#fff;">{{ $cuti->status_walikota }}</span>
                    <strong>{{ $cuti->nama_walikota }}</strong>
                    &mdash; NIP. {{ $cuti->nip_walikota ?? '-' }}
                </div>
                @else
                <div class="text-muted small"><i class="bi bi-clock me-1"></i>Menunggu keputusan Wali Kota Bukittinggi</div>
                @endif
            </div>
        </div>
        @endif

        {{-- TEMPAT TANDA TANGAN --}}
        @if($cuti->status === 'disetujui')
        <div style="margin-top:40px;margin-bottom:20px;">
            <div class="row">
                <div class="col-6">
                    @if($cuti->needsWalikota() && $cuti->nama_walikota)
                    <div class="text-center">
                        <div class="mb-1">Ditetapkan di : <strong>Bukittinggi</strong></div>
                        <div class="mb-3">Pada tanggal  : <strong>{{ $cuti->tanggal_walikota?->format('d F Y') }}</strong></div>

                        <div class="fw-bold mb-4" style="color:#6f42c1;">WALIKOTA BUKITTINGGI</div>

                        <div style="border-top:2px solid #333;display:inline-block;width:280px;margin-bottom:4px;"></div>
                        <div class="fw-semibold">{{ $cuti->nama_walikota }}</div>
                        <div class="small">NIP. {{ $cuti->nip_walikota ?? '-' }}</div>
                    </div>
                    @endif
                </div>
                <div class="col-6 text-end">
                    <div class="mb-1">Ditetapkan di : <strong>Bukittinggi</strong></div>
                    <div class="mb-3">Pada tanggal  : <strong>{{ $cuti->tanggal_kepala_dinas?->format('d F Y') }}</strong></div>

                    <div class="fw-bold mb-4" style="color:#198754;">KEPALA DINAS ...</div>

                    @if($cuti->tanda_tangan_kepala_dinas)
                        <div class="mb-2"><img src="{{ asset('storage/' . $cuti->tanda_tangan_kepala_dinas) }}" alt="Tanda Tangan Kepala Dinas" style="max-width:180px;"></div>
                    @else
                        <div style="border-top:2px solid #333;display:inline-block;width:280px;margin-bottom:4px;"></div>
                    @endif
                    <div class="fw-semibold">{{ $cuti->nama_kepala_dinas }}</div>
                    <div class="small">NIP. {{ $cuti->nip_kepala_dinas ?? '-' }}</div>

                    @if($cuti->nomor_surat)
                    <div class="mt-3 small">
                        Nomor Surat: <strong>{{ $cuti->nomor_surat }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
