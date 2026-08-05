@extends('layouts.app')
@section('title', 'Persetujuan Kasubag Umum')

@php $isTolak = request('status') === 'tolak'; @endphp

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
    <h4 class="mb-0" style="color:#0dcaf0;"><i class="bi bi-person-check me-2"></i>VII-B. Persetujuan Kasubag Umum</h4>
    @if($isTolak)
        <span class="badge bg-danger fs-6 rounded-pill px-3"><i class="bi bi-x-circle me-1"></i>Tolak</span>
    @else
        <span class="badge bg-success fs-6 rounded-pill px-3"><i class="bi bi-check-circle me-1"></i>Setujui</span>
    @endif
</div>

<div class="card mb-3" style="border-left:4px solid #0dcaf0;">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3"><strong>{{ $cuti->pegawai->nama }}</strong></div>
            <div class="col-md-3">{{ $cuti->jenisCuti->nama }}</div>
            <div class="col-md-3">{{ $cuti->lama_cuti_hari }} hari</div>
            <div class="col-md-3">{{ $cuti->tanggal_mulai->format('d M Y') }} - {{ $cuti->tanggal_selesai->format('d M Y') }}</div>
        </div>
    </div>
</div>

<div class="card mb-3" style="background:#f0f9ff;border:1px solid #b3e5fc;">
    <div class="card-body py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pertimbangan Atasan Langsung:</small>
        <span class="badge badge-atasan-langsung">{{ $cuti->status_atasan_langsung }}</span>
        <span class="text-muted small">{{ $cuti->catatan_atasan_langsung }}</span>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('cuti.kasubag.store', $cuti) }}">
            @csrf

            <div class="row g-3">
                @if($isTolak)
                    <input type="hidden" name="status_kasubag" value="tidak_disetujui">
                @else
                    <div class="col-12">
                        <label class="form-label fw-bold">Keputusan</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_kasubag" value="disetujui" id="kasubagSetuju" checked>
                                <label class="form-check-label fw-medium text-success" for="kasubagSetuju">
                                    <i class="bi bi-check-circle me-1"></i>Setuju
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_kasubag" value="tidak_disetujui" id="kasubagTolak">
                                <label class="form-check-label fw-medium text-danger" for="kasubagTolak">
                                    <i class="bi bi-x-circle me-1"></i>Tidak Setuju
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan_kasubag" class="form-control" rows="2" placeholder="Catatan (opsional)"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Kasubag Umum</label>
                    <input type="text" name="nama_kasubag" class="form-control" value="{{ auth()->user()->nama }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP Kasubag Umum</label>
                    <input type="text" name="nip_kasubag" class="form-control" placeholder="NIP" value="{{ auth()->user()->nip }}">
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
