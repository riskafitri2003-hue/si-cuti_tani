@extends('layouts.app')
@section('title', 'Keputusan Wali Kota')

@php $isTolak = request('status') === 'tolak'; @endphp

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
    <h4 class="mb-0" style="color:#6f42c1;"><i class="bi bi-check2-square me-2"></i>VII-E. Keputusan Wali Kota Bukittinggi</h4>
    @if($isTolak)
        <span class="badge bg-danger fs-6 rounded-pill px-3"><i class="bi bi-x-circle me-1"></i>Tolak</span>
    @else
        <span class="badge bg-success fs-6 rounded-pill px-3"><i class="bi bi-check-circle me-1"></i>Setujui</span>
    @endif
</div>

<div class="card mb-3" style="border-left:4px solid #6f42c1;">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3"><strong>{{ $cuti->pegawai->nama }}</strong></div>
            <div class="col-md-3">{{ $cuti->jenisCuti->nama }}</div>
            <div class="col-md-3">{{ $cuti->lama_cuti_hari }} hari</div>
            <div class="col-md-3">{{ $cuti->tanggal_mulai->format('d M Y') }} - {{ $cuti->tanggal_selesai->format('d M Y') }}</div>
        </div>
    </div>
</div>

<div class="card mb-3" style="background:#f3e5f5;border:1px solid #ce93d8;">
    <div class="card-body py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Persetujuan Kepala Dinas:</small>
        <span class="badge badge-kepala-dinas">{{ $cuti->status_kepala_dinas }}</span>
        <span class="text-muted small">{{ $cuti->catatan_kepala_dinas }}</span>
        @if($cuti->nomor_surat)
            <br><small class="text-muted">Nomor Surat: <strong>{{ $cuti->nomor_surat }}</strong></small>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('cuti.walikota.store', $cuti) }}">
            @csrf

            <div class="row g-3">
                @if($isTolak)
                    <input type="hidden" name="status_walikota" value="tidak_disetujui">
                @else
                    <div class="col-12">
                        <label class="form-label fw-bold">Keputusan</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_walikota" value="disetujui" id="walikotaSetuju" checked>
                                <label class="form-check-label fw-medium text-success" for="walikotaSetuju">
                                    <i class="bi bi-check-circle me-1"></i>Setuju & Tanda Tangan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_walikota" value="tidak_disetujui" id="walikotaTolak">
                                <label class="form-check-label fw-medium text-danger" for="walikotaTolak">
                                    <i class="bi bi-x-circle me-1"></i>Tidak Setuju
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan_walikota" class="form-control" rows="2" placeholder="Catatan (opsional)"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Wali Kota</label>
                    <input type="text" name="nama_walikota" class="form-control" value="{{ auth()->user()->nama }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP Wali Kota</label>
                    <input type="text" name="nip_walikota" class="form-control" placeholder="NIP" value="{{ auth()->user()->nip }}">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn rounded-pill px-4 {{ $isTolak ? 'btn-reject' : 'btn-approve' }}">
                    <i class="bi {{ $isTolak ? 'bi-x-circle' : 'bi-check-circle' }} me-1"></i>
                    {{ $isTolak ? 'Tolak' : 'Setujui' }}
                </button>
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
