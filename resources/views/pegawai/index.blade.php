@extends('layouts.app')
@section('title', 'Data Pegawai')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:#1a237e;"><i class="bi bi-people me-2"></i>Data Pegawai</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('pegawai.kelola-akun') }}" class="btn btn-sm rounded-pill px-4" style="background:#0d6efd;color:#fff;">
            <i class="bi bi-shield-lock me-1"></i> Kelola Akun
        </a>
        <a href="{{ route('pegawai.create') }}" class="btn btn-gradient btn-sm rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pegawai
        </a>
    </div>
</div>

<form class="mb-3" method="GET">
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="Cari nama atau NIP...">
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-striped mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Unit Kerja</th>
                    <th>Kontak</th>
                    <th>Akun</th>
                    <th>Sisa Cuti</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pegawais as $pegawai)
                <tr>
                    <td class="fw-medium">{{ $pegawai->nama }}</td>
                    <td class="font-monospace small">{{ $pegawai->nip }}</td>
                    <td>{{ $pegawai->unit_kerja }}</td>
                    <td class="small">
                        @if($pegawai->email)
                            <div><i class="bi bi-envelope me-1 text-primary"></i>{{ $pegawai->email }}</div>
                        @endif
                        @if($pegawai->wa)
                            <div>
                                <i class="bi bi-whatsapp me-1 text-success"></i>
                                @if($pegawai->email)
                                    <a href="https://wa.me/{{ $pegawai->wa }}" target="_blank" class="text-decoration-none">{{ $pegawai->wa }}</a>
                                @else
                                    <a href="https://wa.me/{{ $pegawai->wa }}" target="_blank" class="text-decoration-none">{{ $pegawai->wa }}</a>
                                @endif
                            </div>
                        @endif
                        @if(!$pegawai->email && !$pegawai->wa)
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($pegawai->user)
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Ada</span>
                            <small class="text-muted d-block small">{{ $pegawai->user->roleNames() }}</small>
                        @else
                            <span class="badge bg-secondary rounded-pill"><i class="bi bi-x-circle me-1"></i>Belum</span>
                        @endif
                    </td>
                    <td class="small">
                        @php $s = $pegawai->saldoCutis->first(); @endphp
                        @if($s)
                            <span class="fw-medium" style="color:#6f42c1;">N: {{ $s->saldo_n }}</span>
                            <span class="text-muted"> | N-1: {{ $s->saldo_n1 }}</span>
                            <span class="text-muted"> | N-2: {{ $s->saldo_n2 }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        @if(!$pegawai->user)
                            <a href="{{ route('pegawai.create-account.form', $pegawai) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="bi bi-person-plus me-1"></i>Buat Akun
                            </a>
                        @endif
                        <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form action="{{ route('pegawai.destroy', $pegawai) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus data pegawai ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @if(method_exists($pegawais, 'links'))
            <div class="p-3 border-top">{{ $pegawais->links() }}</div>
        @endif
    </div>
</div>
@endsection
