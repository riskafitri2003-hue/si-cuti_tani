@extends('layouts.app')
@section('title', 'Pengajuan Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:#1a237e;"><i class="bi bi-file-text me-2"></i>Pengajuan Cuti</h4>
    @if(auth()->user()->nip || auth()->user()->isAdmin())
        <a href="{{ route('cuti.create') }}" class="btn btn-gradient btn-sm rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Ajukan Cuti
        </a>
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-striped mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jenis Cuti</th>
                    <th>Tanggal</th>
                    <th>Lama</th>
                    <th><span class="badge-atasan-langsung badge">Atasan Langsung</span></th>
                    <th><span class="badge-kasubag badge">Kasubag</span></th>
                    <th><span class="badge-sekretaris badge">Sekretaris</span></th>
                    <th><span class="badge-kepala-dinas badge">Kepala Dinas</span></th>
                    <th><span class="badge" style="background:#3949ab;color:#fff;">Sekretaris Daerah</span></th>
                    <th><span class="badge" style="background:#6f42c1;color:#fff;">Wkota</span></th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                $statusIcons = [
                    'pending'         => ['icon' => 'bi-hourglass',      'class' => 'icon-pending',   'label' => 'Menunggu'],
                    'disetujui'       => ['icon' => 'bi-check-circle-fill', 'class' => 'icon-approved', 'label' => 'Disetujui'],
                    'tidak_disetujui' => ['icon' => 'bi-x-circle-fill',  'class' => 'icon-rejected',  'label' => 'Ditolak'],
                    'perubahan'       => ['icon' => 'bi-pencil-fill',    'class' => 'icon-changed',   'label' => 'Perubahan'],
                    'ditangguhkan'    => ['icon' => 'bi-pause-circle-fill','class' => 'icon-postponed','label' => 'Ditangguhkan'],
                ];
                @endphp
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
                    <td class="fw-medium">{{ $p->pegawai->nama }}</td>
                    <td>{{ $p->jenisCuti->nama }}</td>
                    <td class="small">{{ $p->tanggal_mulai->format('d/m') }} - {{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                    <td>{{ $p->lama_cuti_hari }} hr</td>
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
                    <td class="text-nowrap">
                        <a href="{{ route('cuti.show', $p) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->canBeAtasanLangsung() && $p->status === 'diajukan' && $p->atasan_langsung_user_id == auth()->id())
                            <a href="{{ route('cuti.atasan-langsung.form', $p) }}?status=setuju" class="btn btn-sm btn-approve rounded-pill px-3">
                                <i class="bi bi-check-circle me-1"></i>Setujui
                            </a>
                            <a href="{{ route('cuti.atasan-langsung.form', $p) }}?status=tolak" class="btn btn-sm btn-reject rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i>Tolak
                            </a>
                        @endif
                        @if(auth()->user()->isKasubag() && $p->status === 'diproses_kasubag')
                            <a href="{{ route('cuti.kasubag.form', $p) }}?status=setuju" class="btn btn-sm btn-approve rounded-pill px-3">
                                <i class="bi bi-check-circle me-1"></i>Setujui
                            </a>
                            <a href="{{ route('cuti.kasubag.form', $p) }}?status=tolak" class="btn btn-sm btn-reject rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i>Tolak
                            </a>
                        @endif
                        @if(auth()->user()->isSekretaris() && $p->status === 'diproses_sekretaris')
                            <a href="{{ route('cuti.sekretaris.form', $p) }}?status=setuju" class="btn btn-sm btn-approve rounded-pill px-3">
                                <i class="bi bi-check-circle me-1"></i>Setujui
                            </a>
                            <a href="{{ route('cuti.sekretaris.form', $p) }}?status=tolak" class="btn btn-sm btn-reject rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i>Tolak
                            </a>
                        @endif
                        @if(auth()->user()->isSekda() && $p->status === 'diproses_sekda')
                            <a href="{{ route('cuti.sekda.form', $p) }}" class="btn btn-sm rounded-pill px-3" style="background:#3949ab;color:#fff;">
                                <i class="bi bi-pen me-1"></i>Tanda Tangan
                            </a>
                        @endif
                        @if(auth()->user()->isKepalaDinas() && $p->status === 'diproses_kepala_dinas')
                            <a href="{{ route('cuti.kepala-dinas.form', $p) }}?status=setuju" class="btn btn-sm btn-approve rounded-pill px-3">
                                <i class="bi bi-check-circle me-1"></i>Setujui
                            </a>
                            <a href="{{ route('cuti.kepala-dinas.form', $p) }}?status=tolak" class="btn btn-sm btn-reject rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i>Tolak
                            </a>
                        @endif
                        @if(auth()->user()->isWalikota() && $p->status === 'diproses_walikota')
                            <a href="{{ route('cuti.walikota.form', $p) }}" class="btn btn-sm rounded-pill px-3" style="background:#6f42c1;color:#fff;">
                                <i class="bi bi-pen me-1"></i>Tanda Tangan
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="12" class="text-center text-muted py-4"><i class="bi bi-inbox me-1"></i>Belum ada pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if(method_exists($pengajuanCutis, 'links'))
            <div class="p-3 border-top">{{ $pengajuanCutis->links() }}</div>
        @endif
    </div>
</div>
@endsection
