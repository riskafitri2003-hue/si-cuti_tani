@extends('layouts.app')
@section('title', 'Riwayat Cuti Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:#1a237e;"><i class="bi bi-clock-history me-2"></i>Riwayat Cuti Saya</h4>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-striped mb-0 align-middle">
            <thead>
                <tr>
                    <th>Jenis Cuti</th>
                    <th>Tgl Pengajuan</th>
                    <th>No. Formulir</th>
                    <th>Tanggal Cuti</th>
                    <th>Lama</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanCutis as $p)
                <tr>
                    <td class="fw-medium">{{ $p->jenisCuti->nama }}</td>
                    <td class="small">{{ $p->tanggal_pengajuan ? $p->tanggal_pengajuan->format('d M Y') : '-' }}</td>
                    <td class="small">{{ $p->nomor_formulir ?: '-' }}</td>
                    <td class="small">{{ $p->tanggal_mulai->format('d M Y') }} - {{ $p->tanggal_selesai->format('d M Y') }}</td>
                    <td>{{ $p->lama_cuti_hari }} hari</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('cuti.show', $p) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i>Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox me-1"></i>Belum ada pengajuan cuti.</td></tr>
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