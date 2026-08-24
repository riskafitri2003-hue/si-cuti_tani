@extends('layouts.app')
@section('title', 'Pertimbangan Atasan Langsung')

@php $isTolak = request('status') === 'tolak'; @endphp

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
    <h4 class="mb-0" style="color:#0d6efd;"><i class="bi bi-person-check me-2"></i>VII-A. Pertimbangan Atasan Langsung</h4>
    @if($isTolak)
        <span class="badge bg-danger fs-6 rounded-pill px-3"><i class="bi bi-x-circle me-1"></i>Tolak</span>
    @else
        <span class="badge bg-success fs-6 rounded-pill px-3"><i class="bi bi-check-circle me-1"></i>Setujui</span>
    @endif
</div>

<div class="card mb-3" style="border-left:4px solid #0d6efd;">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3"><strong>{{ $cuti->pegawai->nama }}</strong></div>
            <div class="col-md-3">{{ $cuti->jenisCuti->nama }}</div>
            <div class="col-md-3">{{ $cuti->lama_cuti_hari }} hari</div>
            <div class="col-md-3">{{ $cuti->tanggal_mulai->format('d M Y') }} - {{ $cuti->tanggal_selesai->format('d M Y') }}</div>
        </div>
        <p class="text-muted small mt-2 mb-0"><i class="bi bi-chat-quote me-1"></i>{{ $cuti->alasan_cuti }}</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('cuti.atasan-langsung.store', $cuti) }}">
            @csrf

            <div class="row g-3">
                @if($isTolak)
                    <input type="hidden" name="status_atasan_langsung" value="tidak_disetujui">
                @else
                    <div class="col-12">
                        <label class="form-label fw-bold">Keputusan</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_atasan_langsung" value="disetujui" id="atasanLangsungSetuju" checked>
                                <label class="form-check-label fw-medium text-success" for="atasanLangsungSetuju">
                                    <i class="bi bi-check-circle me-1"></i>Setuju
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_atasan_langsung" value="tidak_disetujui" id="atasanLangsungTolak">
                                <label class="form-check-label fw-medium text-danger" for="atasanLangsungTolak">
                                    <i class="bi bi-x-circle me-1"></i>Tidak Setuju
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$isTolak)
                <div class="col-12">
                    <label class="form-label fw-bold">Rincian Hari</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small">Disetujui (hari)</label>
                            <input type="number" min="0" max="{{ $cuti->lama_cuti_hari }}" name="atasan_langsung_disetujui_hari" id="alSetujuHari" class="form-control" value="{{ $cuti->atasan_langsung_disetujui_hari ?? $cuti->lama_cuti_hari }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Ditangguhkan (hari) &mdash; otomatis</label>
                            <input type="number" min="0" name="atasan_langsung_ditangguhkan_hari" id="alTangguhkanHari" class="form-control bg-light" value="{{ $cuti->atasan_langsung_ditangguhkan_hari ?? 0 }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Tidak Disetujui (hari)</label>
                            <input type="number" min="0" max="{{ $cuti->lama_cuti_hari }}" name="atasan_langsung_tidak_disetujui_hari" id="alTolakHari" class="form-control" value="{{ $cuti->atasan_langsung_tidak_disetujui_hari ?? 0 }}">
                        </div>
                    </div>
                    <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Ditangguhkan = {{ $cuti->lama_cuti_hari }} &minus; disetujui &minus; tidak disetujui (dihitung otomatis).</p>
                </div>

                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan_atasan_langsung" class="form-control" rows="2" placeholder="Catatan (opsional)"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Atasan Langsung</label>
                    <input type="text" name="nama_atasan_langsung" class="form-control" value="{{ auth()->user()->nama }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP Atasan Langsung</label>
                    <input type="text" name="nip_atasan_langsung" class="form-control" placeholder="NIP" value="{{ auth()->user()->nip }}">
                </div>

                @if($isTolak)
                    <input type="hidden" name="atasan_langsung_tidak_disetujui_hari" value="{{ $cuti->lama_cuti_hari }}">
                @endif

                @if(!$isTolak)
                {{-- TANDA TANGAN ATASAN LANGSUNG --}}
                <div class="col-12">
                    <label class="form-label fw-bold"><i class="bi bi-pen me-1"></i>Tanda Tangan Atasan Langsung</label>
                    <div class="border rounded p-2 bg-white" style="max-width:400px;">
                        <canvas id="signature-pad-al" width="380" height="200" style="width:100%;height:auto;cursor:crosshair;border:1px dashed #ccc;border-radius:6px;"></canvas>
                    </div>
                    <input type="hidden" name="tanda_tangan_data" id="tanda-tangan-data-al">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearSignatureAL()">
                            <i class="bi bi-eraser me-1"></i>Hapus Tanda Tangan
                        </button>
                    </div>
                    <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Gambarkan tanda tangan Anda di area di atas.</p>
                </div>
                @endif
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn rounded-pill px-4 {{ $isTolak ? 'btn-reject' : 'btn-approve' }}" onclick="saveSignatureAL()">
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
var signaturePadAL;

document.addEventListener('DOMContentLoaded', function() {
    var canvas = document.getElementById('signature-pad-al');
    if (!canvas) return;

    signaturePadAL = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#000000'
    });

    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        var ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        signaturePadAL.clear();
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
});

function clearSignatureAL() {
    signaturePadAL.clear();
    document.getElementById('tanda-tangan-data-al').value = '';
}

function saveSignatureAL() {
    if (!signaturePadAL.isEmpty()) {
        document.getElementById('tanda-tangan-data-al').value = signaturePadAL.toDataURL('image/png');
    }
}

var lamaCutiAL = {{ $cuti->lama_cuti_hari }};

function recalcAL() {
    var sEl = document.getElementById('alSetujuHari');
    var tEl = document.getElementById('alTolakHari');
    var gEl = document.getElementById('alTangguhkanHari');
    if (!sEl || !tEl || !gEl) return;
    var s = parseInt(sEl.value, 10) || 0;
    var t = parseInt(tEl.value, 10) || 0;
    if (s + t > lamaCutiAL) {
        if (document.activeElement === sEl) {
            s = Math.max(lamaCutiAL - t, 0);
            sEl.value = s;
        } else {
            t = Math.max(lamaCutiAL - s, 0);
            tEl.value = t;
        }
    }
    gEl.value = lamaCutiAL - s - t;
}

document.addEventListener('DOMContentLoaded', function() {
    ['alSetujuHari', 'alTolakHari'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', recalcAL);
    });
    recalcAL();
});
</script>
@endsection
