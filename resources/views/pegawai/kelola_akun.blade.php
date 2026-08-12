@extends('layouts.app')
@section('title', 'Kelola Akun Pegawai')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:#1a237e;"><i class="bi bi-shield-lock me-2"></i>Kelola Akun Pegawai</h4>
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
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Unit Kerja</th>
                    <th>Role</th>
                    <th>Status Akun</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $i => $pegawai)
                <tr>
                    <td>{{ $pegawais->firstItem() + $i }}</td>
                    <td class="fw-medium">{{ $pegawai->nama }}</td>
                    <td class="font-monospace small">{{ $pegawai->nip }}</td>
                    <td>{{ $pegawai->unit_kerja ?? '-' }}</td>
                    <td>
                        @if($pegawai->user)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($pegawai->user->roleList() as $r)
                                    <span class="badge bg-primary rounded-pill">{{ \App\Models\User::ROLE_LABELS[$r] ?? $r }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($pegawai->user)
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                        @else
                            <span class="badge bg-secondary rounded-pill"><i class="bi bi-x-circle me-1"></i>Belum Ada Akun</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        @if($pegawai->user)
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $pegawai->nip }}">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>
                            <form action="{{ route('pegawai.akun.destroy', $pegawai) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus akun {{ $pegawai->user->nama }}? Data pegawai TIDAK akan dihapus.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createModal{{ $pegawai->nip }}">
                                <i class="bi bi-person-plus me-1"></i>Buat Akun
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox me-1"></i>Tidak ada data pegawai.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if(method_exists($pegawais, 'links'))
            <div class="p-3 border-top">{{ $pegawais->links() }}</div>
        @endif
    </div>
</div>

{{-- MODAL: BUAT AKUN --}}
@foreach($pegawais as $pegawai)
@if(! $pegawai->user)
<div class="modal fade" id="createModal{{ $pegawai->nip }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form method="POST" action="{{ route('pegawai.akun.store', $pegawai) }}">
                @csrf
                <div class="modal-header" style="background:#e8f5e9;border-radius:12px 12px 0 0;">
                    <h6 class="modal-title fw-bold" style="color:#198754;"><i class="bi bi-person-plus me-1"></i>Buat Akun</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="small"><strong>{{ $pegawai->nama }}</strong></div>
                        <div class="small text-muted">NIP: {{ $pegawai->nip }}</div>
                        <div class="small text-muted">{{ $pegawai->unit_kerja ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="3" placeholder="Minimal 3 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span> <span class="text-muted fw-normal">(bisa lebih dari satu)</span></label>
                        <div class="border rounded p-2 bg-white" style="max-height:180px;overflow:auto;">
                            @foreach(\App\Models\User::ROLE_LABELS as $val => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="role[]" value="{{ $val }}" id="role-create-{{ $pegawai->nip }}-{{ $val }}">
                                <label class="form-check-label small" for="role-create-{{ $pegawai->nip }}-{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4"><i class="bi bi-check-circle me-1"></i>Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($pegawai->user)
<div class="modal fade" id="editModal{{ $pegawai->nip }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form method="POST" action="{{ route('pegawai.akun.update', $pegawai) }}">
                @csrf @method('PUT')
                <div class="modal-header" style="background:#e3f2fd;border-radius:12px 12px 0 0;">
                    <h6 class="modal-title fw-bold" style="color:#0d6efd;"><i class="bi bi-pencil-square me-1"></i>Edit Akun</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="small"><strong>{{ $pegawai->user->nama }}</strong></div>
                        <div class="small text-muted">NIP: {{ $pegawai->nip }}</div>
                        <div class="small text-muted">Role saat ini:
                            @foreach($pegawai->user->roleList() as $r)
                                <span class="badge bg-primary">{{ \App\Models\User::ROLE_LABELS[$r] ?? $r }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ubah Role <span class="text-danger">*</span> <span class="text-muted fw-normal">(bisa lebih dari satu)</span></label>
                        <div class="border rounded p-2 bg-white" style="max-height:180px;overflow:auto;">
                            @foreach(\App\Models\User::ROLE_LABELS as $val => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="role[]" value="{{ $val }}" id="role-edit-{{ $pegawai->nip }}-{{ $val }}" @if(in_array($val, $pegawai->user->roleList())) checked @endif>
                                <label class="form-check-label small" for="role-edit-{{ $pegawai->nip }}-{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-muted fw-normal">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-control" minlength="3" placeholder="Masukkan password baru">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-circle me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection
