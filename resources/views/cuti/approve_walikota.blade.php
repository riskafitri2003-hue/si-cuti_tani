@extends('layouts.app')
@section('title', 'Tanda Tangan Wali Kota')

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
    <h4 class="mb-0" style="color:#6f42c1;"><i class="bi bi-pen me-2"></i>VII-E. Tanda Tangan Wali Kota Bukittinggi</h4>
    <span class="badge bg-info fs-6 rounded-pill px-3"><i class="bi bi-pen me-1"></i>Tanda Tangan</span>
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

@if($cuti->isKepalaDinasApplicant())
<div class="card mb-3" style="background:#e8eaf6;border:1px solid #9fa8da;">
    <div class="card-body py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pengajuan cuti Kepala Dinas telah ditandatangani Sekretaris Daerah. Cukup beri tanda tangan Wali Kota, tanpa persetujuan setuju/tolak.</small>
        @if($cuti->nama_sekda)
            <div class="small mt-2"><i class="bi bi-person-check me-1"></i>Sekretaris Daerah: <strong>{{ $cuti->nama_sekda }}</strong> ({{ $cuti->tanggal_sekda?->format('d M Y') }})</div>
        @endif
    </div>
</div>
@else
<div class="card mb-3" style="background:#f3e5f5;border:1px solid #ce93d8;">
    <div class="card-body py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pengajuan ini tidak memerlukan persetujuan (setuju/tolak) dari Wali Kota. Cukup beri tanda tangan untuk mengesahkan cuti.</small>
        <span class="badge badge-kepala-dinas">{{ $cuti->status_kepala_dinas }}</span>
        <span class="text-muted small">{{ $cuti->catatan_kepala_dinas }}</span>
        @if($cuti->nomor_surat)
            <br><small class="text-muted">Nomor Surat: <strong>{{ $cuti->nomor_surat }}</strong></small>
        @endif
    </div>
</div>
@endif

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('cuti.walikota.store', $cuti) }}" id="formWalikota">
            @csrf

            <input type="hidden" name="status_walikota" value="disetujui">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Wali Kota</label>
                    <input type="text" name="nama_walikota" class="form-control" value="{{ auth()->user()->nama }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP Wali Kota</label>
                    <input type="text" name="nip_walikota" class="form-control" placeholder="NIP" value="{{ auth()->user()->nip }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold"><i class="bi bi-pen me-1"></i>Tanda Tangan Wali Kota</label>
                    <div class="border rounded p-2 bg-white" style="max-width:400px;">
                        <canvas id="signature-pad-wk" width="380" height="200" style="width:100%;height:auto;cursor:crosshair;border:1px dashed #ccc;border-radius:6px;"></canvas>
                    </div>
                    <input type="hidden" name="tanda_tangan_data" id="tanda-tangan-data-wk">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearSignatureWK()">
                            <i class="bi bi-eraser me-1"></i>Hapus Tanda Tangan
                        </button>
                    </div>
                    <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Gambarkan tanda tangan Anda di area di atas.</p>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-approve rounded-pill px-4" onclick="saveSignatureWK()">
                    <i class="bi bi-pen me-1"></i>Tanda Tangani
                </button>
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
var signaturePadWK;

document.addEventListener('DOMContentLoaded', function() {
    var canvas = document.getElementById('signature-pad-wk');
    if (!canvas) return;

    signaturePadWK = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#000000'
    });

    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        var ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        signaturePadWK.clear();
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
});

function clearSignatureWK() {
    signaturePadWK.clear();
    document.getElementById('tanda-tangan-data-wk').value = '';
}

function saveSignatureWK() {
    var el = document.getElementById('tanda-tangan-data-wk');
    if (signaturePadWK.isEmpty()) {
        alert('Silakan gambar tanda tangan Anda terlebih dahulu.');
        event.preventDefault();
        return;
    }
    el.value = signaturePadWK.toDataURL('image/png');
}
</script>
@endsection
