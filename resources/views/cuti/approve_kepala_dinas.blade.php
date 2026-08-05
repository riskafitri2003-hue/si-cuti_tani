@extends('layouts.app')
@section('title', 'Keputusan Kepala Dinas')

@php $isTolak = request('status') === 'tolak'; @endphp

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
    <h4 class="mb-0" style="color:#198754;"><i class="bi bi-check2-square me-2"></i>VII-D. Keputusan Kepala Dinas</h4>
    @if($isTolak)
        <span class="badge bg-danger fs-6 rounded-pill px-3"><i class="bi bi-x-circle me-1"></i>Tolak</span>
    @else
        <span class="badge bg-success fs-6 rounded-pill px-3"><i class="bi bi-check-circle me-1"></i>Setujui</span>
    @endif
</div>

<div class="card mb-3" style="border-left:4px solid #198754;">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3"><strong>{{ $cuti->pegawai->nama }}</strong></div>
            <div class="col-md-3">{{ $cuti->jenisCuti->nama }}</div>
            <div class="col-md-3">{{ $cuti->lama_cuti_hari }} hari</div>
            <div class="col-md-3">{{ $cuti->tanggal_mulai->format('d M Y') }} - {{ $cuti->tanggal_selesai->format('d M Y') }}</div>
        </div>
    </div>
</div>

<div class="card mb-3" style="background:#e8f5e9;border:1px solid #a5d6a7;">
    <div class="card-body py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Persetujuan Sekretaris:</small>
        <span class="badge badge-sekretaris">{{ $cuti->status_sekretaris }}</span>
        <span class="text-muted small">{{ $cuti->catatan_sekretaris }}</span>
    </div>
</div>

@if($cuti->needsWalikota())
<div class="card mb-3" style="background:#fff3e0;border:1px solid #ffcc80;">
    <div class="card-body py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i><strong>Alur Khusus:</strong> Setelah Kepala Dinas menandatangani, pengajuan ini akan diteruskan ke Wali Kota untuk persetujuan akhir.</small>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('cuti.kepala-dinas.store', $cuti) }}" id="formKepalaDinas">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-file-earmark me-1"></i>Nomor Surat</label>
                    <input type="text" name="nomor_surat" class="form-control" placeholder="contoh: 851/...">
                </div>
                <div class="col-md-6"></div>

                @if($isTolak)
                    <input type="hidden" name="status_kepala_dinas" value="tidak_disetujui">
                @else
                    <div class="col-12">
                        <label class="form-label fw-bold">Keputusan</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_kepala_dinas" value="disetujui" id="kepalaDinasSetuju" checked>
                                <label class="form-check-label fw-medium text-success" for="kepalaDinasSetuju">
                                    <i class="bi bi-check-circle me-1"></i>Setuju & Tanda Tangan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_kepala_dinas" value="tidak_disetujui" id="kepalaDinasTolak">
                                <label class="form-check-label fw-medium text-danger" for="kepalaDinasTolak">
                                    <i class="bi bi-x-circle me-1"></i>Tidak Setuju
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan_kepala_dinas" class="form-control" rows="2" placeholder="Catatan (opsional)"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Kepala Dinas</label>
                    <input type="text" name="nama_kepala_dinas" class="form-control" value="{{ auth()->user()->nama }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP Kepala Dinas</label>
                    <input type="text" name="nip_kepala_dinas" class="form-control" placeholder="NIP" value="{{ auth()->user()->nip }}">
                </div>

                @if(!$isTolak)
                {{-- TANDA TANGAN KEPALA DINAS --}}
                <div class="col-12">
                    <label class="form-label fw-bold"><i class="bi bi-pen me-1"></i>Tanda Tangan Kepala Dinas</label>
                    <div class="border rounded p-2 bg-white" style="max-width:400px;">
                        <canvas id="signature-pad-kd" width="380" height="200" style="width:100%;height:auto;cursor:crosshair;border:1px dashed #ccc;border-radius:6px;"></canvas>
                    </div>
                    <input type="hidden" name="tanda_tangan_data" id="tanda-tangan-data-kd">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearSignatureKD()">
                            <i class="bi bi-eraser me-1"></i>Hapus Tanda Tangan
                        </button>
                    </div>
                    <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Gambarkan tanda tangan Anda di area di atas.</p>
                </div>
                @endif
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn rounded-pill px-4 {{ $isTolak ? 'btn-reject' : 'btn-approve' }}" onclick="saveSignatureKD()">
                    <i class="bi {{ $isTolak ? 'bi-x-circle' : 'bi-check-circle' }} me-1"></i>
                    {{ $isTolak ? 'Tolak' : 'Setujui' }}
                </button>
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
var signaturePadKD;

document.addEventListener('DOMContentLoaded', function() {
    var canvas = document.getElementById('signature-pad-kd');
    if (!canvas) return;

    signaturePadKD = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#000000'
    });

    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        var ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        signaturePadKD.clear();
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
});

function clearSignatureKD() {
    signaturePadKD.clear();
    document.getElementById('tanda-tangan-data-kd').value = '';
}

function saveSignatureKD() {
    if (!signaturePadKD.isEmpty()) {
        document.getElementById('tanda-tangan-data-kd').value = signaturePadKD.toDataURL('image/png');
    }
}
</script>
@endsection
