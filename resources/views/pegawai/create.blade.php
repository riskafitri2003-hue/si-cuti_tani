@extends('layouts.app')
@section('title', 'Tambah Pegawai')

@section('content')
<h4 class="mb-3" style="color:#1a237e;"><i class="bi bi-person-plus me-2"></i>Tambah Pegawai</h4>
<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('pegawai.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input name="nama" class="form-control" value="{{ old('nama') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP</label>
                    <input name="nip" class="form-control" value="{{ old('nip') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input name="jabatan" class="form-control" value="{{ old('jabatan') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit Kerja</label>
                    <input name="unit_kerja" class="form-control" value="{{ old('unit_kerja') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Masa Kerja</label>
                    <input name="masa_kerja" class="form-control" placeholder="contoh: 14 tahun 3 bulan" value="{{ old('masa_kerja') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Telpon</label>
                    <input name="no_telpon" class="form-control" value="{{ old('no_telpon') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contoh: nama@email.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. WhatsApp</label>
                    <input name="wa" class="form-control" value="{{ old('wa') }}" placeholder="contoh: 6281234567890">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Device ID / Token Fonnte <span class="text-muted small">(opsional)</span></label>
                    <input name="fonnte_device_id" class="form-control" value="{{ old('fonnte_device_id') }}" placeholder="Kosongkan jika pakai token utama">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <input name="alamat" class="form-control" value="{{ old('alamat') }}">
                </div>
            </div>

            <div class="card bg-light border-0 mt-4 p-3">
                <h6 class="mb-2" style="color:#0d6efd;"><i class="bi bi-shield-lock me-1"></i> Akun Login (opsional)</h6>
                <p class="small text-muted mb-3">Kosongkan password jika belum ingin membuat akun login.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-muted small">(min. 3 karakter)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak perlu">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Role / Level</label>
                        <select name="role" class="form-select">
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
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-gradient rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
