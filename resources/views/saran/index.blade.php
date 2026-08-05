@extends('layouts.app')
@section('title', 'Rekap Saran / Masukan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:#1a237e;"><i class="bi bi-lightbulb me-2"></i>Rekap Saran / Masukan</h4>
</div>

<form class="mb-3" method="GET">
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="Cari nama atau NIP...">
    </div>
</form>

<div class="d-flex flex-column gap-3">
    @forelse($saranCutis as $saran)
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <strong>{{ $saran->pegawai?->nama ?? '-' }}</strong>
                    <span class="font-monospace small text-muted">{{ $saran->nip }}</span>
                </div>
                <small class="text-muted text-nowrap">{{ $saran->created_at?->format('d M Y H:i') }}</small>
            </div>
            <hr class="my-2">
            <div class="d-flex flex-column gap-1 small">
                <div class="d-flex justify-content-between align-items-center">
                    <span>1. Pengguna kesulitan memahami menu aplikasi.</span>
                    @if($saran->kesulitan_menu)
                        <span class="badge bg-danger rounded-pill">Ya</span>
                    @else
                        <span class="text-muted">Tidak</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>2. Pengajuan cuti gagal dikirim.</span>
                    @if($saran->pengajuan_gagal)
                        <span class="badge bg-danger rounded-pill">Ya</span>
                    @else
                        <span class="text-muted">Tidak</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>3. File pendukung tidak dapat diunggah.</span>
                    @if($saran->file_gagal)
                        <span class="badge bg-danger rounded-pill">Ya</span>
                    @else
                        <span class="text-muted">Tidak</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>4. Halaman aplikasi lambat dibuka.</span>
                    @if($saran->halaman_lambat)
                        <span class="badge bg-danger rounded-pill">Ya</span>
                    @else
                        <span class="text-muted">Tidak</span>
                    @endif
                </div>
            </div>
            <hr class="my-2">
            <div class="small">
                <span class="fw-medium">Saran:</span>
                <span class="text-muted">{{ $saran->saran ?: '-' }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center text-muted py-4">
            <i class="bi bi-inbox me-1"></i>Belum ada saran/masukan.
        </div>
    </div>
    @endforelse
</div>

@if(method_exists($saranCutis, 'links'))
    <div class="mt-3">{{ $saranCutis->links() }}</div>
@endif
@endsection
