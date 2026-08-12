@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <div class="user-avatar" style="width:42px;height:42px;font-size:17px;background:#e3f2fd;color:#1565c0;">
        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
    </div>
    <div>
        <h4 class="mb-0" style="color:#1a237e;">Selamat datang, {{ auth()->user()->nama }}</h4>
        <small class="text-muted">Role: <span class="badge bg-primary bg-opacity-10 text-primary">{{ auth()->user()->role }}</span></small>
    </div>
</div>

{{-- SALDO CUTI (khusus pegawai) --}}
@if(auth()->user()->isPegawai() && $saldoCutis->isNotEmpty())
    @php $totalSisa = $saldoCutis->sum('saldo'); @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0" style="border-left-color:#6f42c1;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background:#f3e5f5;color:#6f42c1;">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Sisa Cuti</div>
                            <div class="fs-3 fw-bold" style="color:#6f42c1;">{{ $totalSisa }} hari</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php $s = $saldoCutis->first(); @endphp
        @if($s)
        <div class="col-md-2">
            <div class="card stat-card border-0 text-center h-100" style="border-left-color:#0d6efd;">
                <div class="card-body d-flex flex-column justify-content-center py-2">
                    <div class="text-muted small fw-medium">N-2</div>
                    <div class="fs-4 fw-bold" style="color:#1a237e;">{{ $s->saldo_n2 }}</div>
                    <div class="small text-muted">hari</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card border-0 text-center h-100" style="border-left-color:#0d6efd;">
                <div class="card-body d-flex flex-column justify-content-center py-2">
                    <div class="text-muted small fw-medium">N-1</div>
                    <div class="fs-4 fw-bold" style="color:#1a237e;">{{ $s->saldo_n1 }}</div>
                    <div class="small text-muted">hari</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card border-0 text-center h-100" style="border-left-color:#0d6efd;">
                <div class="card-body d-flex flex-column justify-content-center py-2">
                    <div class="text-muted small fw-medium">N</div>
                    <div class="fs-4 fw-bold" style="color:#1a237e;">{{ $s->saldo_n }}</div>
                    <div class="small text-muted">hari</div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0" style="border-left-color:#0d6efd;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e3f2fd;color:#0d6efd;">
                    <i class="bi bi-file-text"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Pengajuan</div>
                    <div class="fs-3 fw-bold" style="color:#1a237e;">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0" style="border-left-color:#0dcaf0;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8f8ff;color:#0dcaf0;">
                    <i class="bi bi-send"></i>
                </div>
                <div>
                    <div class="text-muted small">Baru Diajukan</div>
                    <div class="fs-3 fw-bold" style="color:#0dcaf0;">{{ $stats['diajukan'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0" style="border-left-color:#198754;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8f5e9;color:#198754;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small">Disetujui</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['disetujui'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0" style="border-left-color:#dc3545;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fbe9e7;color:#dc3545;">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <div class="text-muted small">Ditolak</div>
                    <div class="fs-3 fw-bold text-danger">{{ $stats['ditolak'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <i class="bi bi-list-ul"></i>
        @if(auth()->user()->isAtasanLangsung())
            Pengajuan Menunggu Pertimbangan Atasan Langsung
        @elseif(auth()->user()->isKasubag())
            Pengajuan Menunggu Persetujuan Kasubag Umum
        @elseif(auth()->user()->isSekretaris())
            Pengajuan Menunggu Persetujuan Sekretaris
        @elseif(auth()->user()->isKepalaDinas())
            Pengajuan Menunggu Keputusan Kepala Dinas
        @elseif(auth()->user()->isWalikota())
            Pengajuan Menunggu Tanda Tangan Wali Kota
        @else
            Pengajuan Terbaru
        @endif
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jenis Cuti</th>
                    <th>Tanggal</th>
                    <th>Lama</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanCutis as $p)
                <tr>
                    <td class="fw-medium">{{ $p->pegawai->nama }}</td>
                    <td>{{ $p->jenisCuti->nama }}</td>
                    <td>{{ $p->tanggal_mulai->format('d M Y') }} - {{ $p->tanggal_selesai->format('d M Y') }}</td>
                    <td>{{ $p->lama_cuti_hari }} hari</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                    <td>
                        <a href="{{ route('cuti.show', $p) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i>Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox me-1"></i>Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
