@extends('layouts.app')
@section('title', 'Laporan Pengajuan Cuti')

@php
$statusIcons = [
    'pending'         => ['icon' => 'bi-hourglass',      'class' => 'icon-pending',   'label' => 'Menunggu'],
    'disetujui'       => ['icon' => 'bi-check-circle-fill', 'class' => 'icon-approved', 'label' => 'Disetujui'],
    'tidak_disetujui' => ['icon' => 'bi-x-circle-fill',  'class' => 'icon-rejected',  'label' => 'Ditolak'],
    'perubahan'       => ['icon' => 'bi-pencil-fill',    'class' => 'icon-changed',   'label' => 'Perubahan'],
    'ditangguhkan'    => ['icon' => 'bi-pause-circle-fill','class' => 'icon-postponed','label' => 'Ditangguhkan'],
];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:#1a237e;"><i class="bi bi-bar-chart-fill me-2"></i>Laporan Pengajuan Cuti</h4>
    <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-3 no-print">
        <i class="bi bi-printer me-1"></i>Cetak
    </button>
</div>

{{-- FILTER --}}
<div class="card mb-3 no-print">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Pegawai</label>
                <select name="nip" class="form-select form-select-sm">
                    <option value="">Semua Pegawai</option>
                    @foreach($pegawais as $peg)
                        <option value="{{ $peg->nip }}" {{ request('nip') == $peg->nip ? 'selected' : '' }}>{{ $peg->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Jenis Cuti</label>
                <select name="kode_jenis_cuti" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisCutis as $jc)
                        <option value="{{ $jc->kode }}" {{ request('kode_jenis_cuti') == $jc->kode ? 'selected' : '' }}>{{ $jc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-gradient btn-sm rounded-pill px-3 w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

{{-- STATISTIK --}}
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card text-center py-2" style="border-left:4px solid #0d6efd;">
            <div class="card-body py-1">
                <div class="fs-4 fw-bold" style="color:#0d6efd;">{{ $total }}</div>
                <div class="small text-muted"><i class="bi bi-file-text me-1"></i>Total Pengajuan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-2" style="border-left:4px solid #198754;">
            <div class="card-body py-1">
                <div class="fs-4 fw-bold" style="color:#198754;">{{ $disetujui }}</div>
                <div class="small text-muted"><i class="bi bi-check-circle me-1"></i>Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-2" style="border-left:4px solid #dc3545;">
            <div class="card-body py-1">
                <div class="fs-4 fw-bold" style="color:#dc3545;">{{ $ditolak }}</div>
                <div class="small text-muted"><i class="bi bi-x-circle me-1"></i>Ditolak</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-2" style="border-left:4px solid #6c757d;">
            <div class="card-body py-1">
                <div class="fs-4 fw-bold" style="color:#6c757d;">{{ $proses }}</div>
                <div class="small text-muted"><i class="bi bi-hourglass me-1"></i>Proses</div>
            </div>
        </div>
    </div>
</div>

{{-- GRAFIK --}}
<div class="row g-3 mb-3 no-print">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-pie-chart me-1"></i>Distribusi Status</h6>
                <canvas id="chartStatus" height="180"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-bar-chart me-1"></i>Pengajuan per Jenis Cuti</h6>
                <canvas id="chartJenis" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- TABEL --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0 align-middle" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Unit Kerja</th>
                        <th>Jenis Cuti</th>
                        <th>Lama</th>
                        <th>Tgl Mulai</th>
                        <th>Tgl Selesai</th>
                        <th>Alasan</th>
                        <th class="text-center"><span class="badge-atasan-langsung badge">Atasan Langsung</span></th>
                        <th class="text-center"><span class="badge-kasubag badge">Kasubag</span></th>
                        <th class="text-center"><span class="badge-sekretaris badge">Sekretaris</span></th>
                    <th class="text-center"><span class="badge-kepala-dinas badge">Kepala Dinas</span></th>
                    <th class="text-center"><span class="badge" style="background:#3949ab;color:#fff;">Sekretaris Daerah</span></th>
                    <th class="text-center"><span class="badge" style="background:#6f42c1;color:#fff;">Wkota</span></th>
                    <th>Status</th>
                        <th>Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = $pengajuanCutis->firstItem(); @endphp
                    @forelse($pengajuanCutis as $p)
                    @php
                        $ikab = $statusIcons[$p->status_atasan_langsung] ?? $statusIcons['pending'];
                        $ikas = $statusIcons[$p->status_kasubag] ?? $statusIcons['pending'];
                        $isek = $statusIcons[$p->status_sekretaris] ?? $statusIcons['pending'];
                        $isekda = $statusIcons[$p->status_sekda] ?? $statusIcons['pending'];
                        $ikad = $statusIcons[$p->status_kepala_dinas] ?? $statusIcons['pending'];
                        $iwk = $statusIcons[$p->status_walikota] ?? $statusIcons['pending'];
                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td class="fw-medium">{{ $p->pegawai->nama }}</td>
                        <td class="font-monospace small">{{ $p->pegawai->nip }}</td>
                        <td>{{ $p->pegawai->unit_kerja }}</td>
                        <td>{{ $p->jenisCuti->nama }}</td>
                        <td>{{ $p->lama_cuti_hari }} hr</td>
                        <td class="small">{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                        <td class="small">{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                        <td class="small text-muted" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $p->alasan_cuti }}">{{ $p->alasan_cuti }}</td>
                        <td class="text-center">
                            <i class="bi {{ $ikab['icon'] }} {{ $ikab['class'] }}"></i>
                            <div class="text-status">{{ $ikab['label'] }}</div>
                        </td>
                        <td class="text-center">
                            <i class="bi {{ $ikas['icon'] }} {{ $ikas['class'] }}"></i>
                            <div class="text-status">{{ $ikas['label'] }}</div>
                        </td>
                        <td class="text-center">
                            <i class="bi {{ $isek['icon'] }} {{ $isek['class'] }}"></i>
                            <div class="text-status">{{ $isek['label'] }}</div>
                        </td>
                        <td class="text-center">
                            <i class="bi {{ $ikad['icon'] }} {{ $ikad['class'] }}"></i>
                            <div class="text-status">{{ $ikad['label'] }}</div>
                        </td>
                        <td class="text-center">
                            @if($p->isKepalaDinasApplicant())
                                <i class="bi {{ $isekda['icon'] }} {{ $isekda['class'] }}"></i>
                                <div class="text-status">{{ $isekda['label'] }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array($p->jenisCuti->kode, [2, 7]) || $p->isKepalaDinasApplicant())
                                <i class="bi {{ $iwk['icon'] }} {{ $iwk['class'] }}"></i>
                                <div class="text-status">{{ $iwk['label'] }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                        <td class="small">{{ $p->tanggal_pengajuan?->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="17" class="text-center text-muted py-4"><i class="bi bi-inbox me-1"></i>Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($pengajuanCutis, 'links'))
            <div class="p-3 border-top">{{ $pengajuanCutis->links() }}</div>
        @endif
    </div>
</div>

{{-- CETAK FOOTER --}}
@if(request()->has('cetak'))
<style>
    .no-print { display: none !important; }
    body { background: #fff; }
    .card { box-shadow: none !important; }
</style>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Disetujui', 'Ditolak', 'Diproses'],
        datasets: [{
            data: [{{ $disetujui }}, {{ $ditolak }}, {{ $proses }}],
            backgroundColor: ['#198754', '#dc3545', '#6c757d'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }
        }
    }
});
new Chart(document.getElementById('chartJenis'), {
    type: 'bar',
    data: {
        labels: @json($chartJenisLabel),
        datasets: [{
            label: 'Jumlah',
            data: @json($chartJenisData),
            backgroundColor: '#0d6efd',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
