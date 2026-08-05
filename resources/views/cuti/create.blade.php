@extends('layouts.app')
@section('title', 'Ajukan Cuti')

@section('content')
<h4 class="mb-3" style="color:#1a237e;"><i class="bi bi-plus-circle me-2"></i>Formulir Permintaan Cuti</h4>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('cuti.store') }}" enctype="multipart/form-data" id="formCuti">
            @csrf

            @if(auth()->user()->isAdmin())
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-person me-1 text-primary"></i>Pegawai</label>
                <select name="nip" class="form-select" required onchange="showSaldo(this)">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($pegawais as $pegawai)
                        @php $sisa = $pegawai->saldoCutis->first()->saldo_n ?? 0; @endphp
                        <option value="{{ $pegawai->nip }}" data-sisa="{{ $sisa }}">
                            {{ $pegawai->nama }} ({{ $pegawai->nip }}) – Sisa: {{ $sisa }} hari
                        </option>
                    @endforeach
                </select>
                <div id="saldoInfo" class="mt-1 small text-muted"></div>
            </div>
            @endif

            <div class="card bg-light border-0 mb-3">
                <div class="card-body py-3">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-bookmark me-1 text-primary"></i>II. Jenis Cuti Yang Diambil</label>
                        <select name="kode_jenis_cuti" class="form-select" required>
                            <option value="">-- Pilih Kode Cuti --</option>
                            @foreach($jenisCutis as $jenis)
                                <option value="{{ $jenis->kode }}">{{ $jenis->kode }}. {{ $jenis->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label"><i class="bi bi-person-badge me-1 text-primary"></i>Atasan Langsung</label>
                        <select name="atasan_langsung_user_id" class="form-select" required>
                            <option value="">-- Pilih Atasan Langsung --</option>
                            @foreach($atasanLangsungs as $atasanLangsung)
                                <option value="{{ $atasanLangsung->user_id }}">{{ $atasanLangsung->nama }}</option>
                            @endforeach
                        </select>
                        <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Pilih atasan langsung yang membimbing Anda.</p>
                    </div>
                    <div class="mt-3 mb-0">
                        <label class="form-label"><i class="bi bi-chat-dots me-1 text-primary"></i>III. Alasan Cuti</label>
                        <textarea name="alasan_cuti" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
            </div>

            <div class="card bg-light border-0 mb-3">
                <div class="card-body py-3">
                    <label class="form-label"><i class="bi bi-calendar-range me-1 text-primary"></i>IV. Lamanya Cuti</label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Mulai Tanggal</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Sampai Dengan</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                    <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Lama cuti (hari) akan dihitung otomatis dari rentang tanggal di atas.</p>
                </div>
            </div>

            <div class="card bg-light border-0 mb-3">
                <div class="card-body py-3">
                    <label class="form-label"><i class="bi bi-geo-alt me-1 text-primary"></i>VI. Alamat Selama Menjalankan Cuti</label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="alamat_selama_cuti" class="form-control" placeholder="Alamat">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="telpon_selama_cuti" class="form-control" placeholder="No. Telpon">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-light border-0 mb-3">
                <div class="card-body py-3">
                    <label class="form-label"><i class="bi bi-paperclip me-1 text-primary"></i>VII. Dokumen Pendukung <span class="text-muted fw-normal">(opsional)</span></label>
                    <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Format: PDF, JPG, PNG. Maksimal 2MB. Kosongkan jika tidak perlu.</p>
                </div>
            </div>

            <div class="card bg-light border-0 mb-3">
                <div class="card-body py-3">
                    <label class="form-label"><i class="bi bi-pen me-1 text-primary"></i>VIII. Tanda Tangan Pegawai</label>
                    <div class="border rounded p-2 bg-white" style="max-width:400px;">
                        <canvas id="signature-pad" width="380" height="200" style="width:100%;height:auto;cursor:crosshair;border:1px dashed #ccc;border-radius:6px;"></canvas>
                    </div>
                    <input type="hidden" name="tanda_tangan_data" id="tanda-tangan-data">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearSignature()">
                            <i class="bi bi-eraser me-1"></i>Hapus Tanda Tangan
                        </button>
                    </div>
                    <p class="text-muted small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i>Gambarkan tanda tangan Anda di area di atas.</p>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-gradient rounded-pill px-4" type="submit" onclick="saveSignature()">
                    <i class="bi bi-send me-1"></i> Kirim Pengajuan
                </button>
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
var signaturePad;

document.addEventListener('DOMContentLoaded', function() {
    var canvas = document.getElementById('signature-pad');
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#000000'
    });

    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        var ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        signaturePad.clear();
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
});

function clearSignature() {
    signaturePad.clear();
    document.getElementById('tanda-tangan-data').value = '';
}

function saveSignature() {
    if (!signaturePad.isEmpty()) {
        document.getElementById('tanda-tangan-data').value = signaturePad.toDataURL('image/png');
    }
}

function showSaldo(select) {
    var opt = select.options[select.selectedIndex];
    var info = document.getElementById('saldoInfo');
    if (opt.value) {
        info.innerHTML = '<i class="bi bi-info-circle me-1"></i>Sisa saldo cuti tahun berjalan: <strong>' + opt.dataset.sisa + '</strong> hari';
    } else {
        info.innerHTML = '';
    }
}
</script>
@endsection
