@extends('layouts.app')
@section('title', 'Tanda Tangan Sekretaris Daerah')

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
    <h4 class="mb-0" style="color:#0d6efd;"><i class="bi bi-pen me-2"></i>Tanda Tangan Sekretaris Daerah</h4>
</div>

<div class="card mb-3" style="border-left:4px solid #0d6efd;">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3"><strong>{{ $cuti->pegawai->nama }}</strong></div>
            <div class="col-md-3">{{ $cuti->jenisCuti->nama }}</div>
            <div class="col-md-3">{{ $cuti->lama_cuti_hari }} hari</div>
            <div class="col-md-3">{{ $cuti->tanggal_mulai->format('d M Y') }} - {{ $cuti->tanggal_selesai->format('d M Y') }}</div>
        </div>
    </div>
</div>

<div class="card mb-3" style="background:#e3f2fd;border:1px solid #90caf9;">
    <div class="card-body py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pengajuan cuti Kepala Dinas tidak memerlukan persetujuan (setuju/tolak), cukup diberi tanda tangan oleh Sekretaris Daerah. Setelah ini diteruskan ke Wali Kota untuk tanda tangan.</small>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('cuti.sekda.store', $cuti) }}" id="formSekda">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Sekretaris Daerah</label>
                    <input type="text" name="nama_sekda" class="form-control" value="{{ auth()->user()->nama }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP Sekretaris Daerah</label>
                    <input type="text" name="nip_sekda" class="form-control" placeholder="NIP" value="{{ auth()->user()->nip }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold"><i class="bi bi-pen me-1"></i>Tanda Tangan Sekretaris Daerah</label>
                    <div class="border rounded p-2 bg-white" style="max-width:400px;">
                        <canvas id="signature-pad-sekda" width="380" height="200" style="width:100%;height:auto;cursor:crosshair;border:1px dashed #ccc;border-radius:6px;"></canvas>
                    </div>
                    <input type="hidden" name="tanda_tangan_data" id="tanda-tangan-data-sekda">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearSignatureSekda()">
                            <i class="bi bi-eraser me-1"></i>Hapus Tanda Tangan
                        </button>
                    </div>
                    <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Gambarkan tanda tangan Anda di area di atas.</p>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-approve rounded-pill px-4" onclick="saveSignatureSekda()">
                    <i class="bi bi-check-circle me-1"></i>Tanda Tangani & Lanjutkan
                </button>
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
var signaturePadSekda;

document.addEventListener('DOMContentLoaded', function() {
    var canvas = document.getElementById('signature-pad-sekda');
    if (!canvas) return;

    signaturePadSekda = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#000000'
    });

    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        var ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        signaturePadSekda.clear();
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
});

function clearSignatureSekda() {
    signaturePadSekda.clear();
    document.getElementById('tanda-tangan-data-sekda').value = '';
}

function saveSignatureSekda() {
    var el = document.getElementById('tanda-tangan-data-sekda');
    if (signaturePadSekda.isEmpty()) {
        alert('Silakan gambar tanda tangan Anda terlebih dahulu.');
        event.preventDefault();
        return;
    }
    el.value = signaturePadSekda.toDataURL('image/png');
}
</script>
@endsection
