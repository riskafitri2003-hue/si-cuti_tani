@extends('layouts.app')
@section('title', 'Buat Akun - ' . $pegawai->nama)

@section('content')
<h4 class="mb-3" style="color:#1a237e;"><i class="bi bi-person-plus me-2"></i>Buat Akun</h4>

<div class="card mb-3">
    <div class="card-body">
        <table class="table table-sm w-auto mb-0">
            <tr><th class="border-0 ps-0">Nama</th><td class="border-0">: {{ $pegawai->nama }}</td></tr>
            <tr><th class="ps-0">NIP</th><td>: <span class="font-monospace">{{ $pegawai->nip }}</span></td></tr>
            <tr><th class="ps-0">Unit Kerja</th><td>: {{ $pegawai->unit_kerja ?? '-' }}</td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('pegawai.create-account.store', $pegawai) }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="3" placeholder="Minimal 3 karakter">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role / Level <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="pegawai">Pegawai</option>
                        <option value="atasan_langsung">Atasan Langsung</option>
                        <option value="kasubag">Kasubag Umum</option>
                        <option value="sekretaris">Sekretaris</option>
                        <option value="kepala_dinas">Kepala Dinas</option>
                        <option value="sekda">Sekretaris Daerah</option>
                        <option value="walikota">Walikota</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-gradient rounded-pill px-4">
                    <i class="bi bi-person-check me-1"></i> Buat Akun
                </button>
                <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
