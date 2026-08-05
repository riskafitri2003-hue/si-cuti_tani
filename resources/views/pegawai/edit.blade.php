@extends('layouts.app')
@section('title', 'Edit Pegawai')

@section('content')
<h4 class="mb-3" style="color:#1a237e;"><i class="bi bi-pencil me-2"></i>Edit Pegawai</h4>
<div class="card mb-3">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('pegawai.update', $pegawai) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input name="nama" class="form-control" value="{{ old('nama', $pegawai->nama) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP</label>
                    <input name="nip" class="form-control" value="{{ old('nip', $pegawai->nip) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input name="jabatan" class="form-control" value="{{ old('jabatan', $pegawai->jabatan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit Kerja</label>
                    <input name="unit_kerja" class="form-control" value="{{ old('unit_kerja', $pegawai->unit_kerja) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Masa Kerja</label>
                    <input name="masa_kerja" class="form-control" value="{{ old('masa_kerja', $pegawai->masa_kerja) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Telpon</label>
                    <input name="no_telpon" class="form-control" value="{{ old('no_telpon', $pegawai->no_telpon) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $pegawai->email) }}" placeholder="contoh: nama@email.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. WhatsApp</label>
                    <input name="wa" class="form-control" value="{{ old('wa', $pegawai->wa) }}" placeholder="contoh: 6281234567890">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Device ID / Token Fonnte <span class="text-muted small">(opsional)</span></label>
                    <input name="fonnte_device_id" class="form-control" value="{{ old('fonnte_device_id', $pegawai->fonnte_device_id) }}" placeholder="Kosongkan jika pakai token utama">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <input name="alamat" class="form-control" value="{{ old('alamat', $pegawai->alamat) }}">
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

<div class="card">
    <div class="card-header-custom">
        <i class="bi bi-journal me-1"></i> V. Catatan Cuti (Sisa Hak Cuti)
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('pegawai.update', $pegawai) }}">
            @csrf @method('PUT')
            <table class="table table-sm w-auto mb-0">
                <thead><tr><th>Tahun</th><th>Sisa (hari)</th><th>Keterangan</th></tr></thead>
                <tbody>
                @php $s = $pegawai->saldoCutis->first(); @endphp
                <tr>
                    <td class="align-middle fw-medium">N-2</td>
                    <td>
                        <input type="number" name="saldo[saldo_n2]"
                               value="{{ $s->saldo_n2 ?? 0 }}" class="form-control form-control-sm" style="width:100px">
                    </td>
                    <td>
                        <input type="text" name="saldo[keterangan_n2]"
                               value="{{ $s->keterangan_n2 ?? '' }}" class="form-control form-control-sm" style="width:180px">
                    </td>
                </tr>
                <tr>
                    <td class="align-middle fw-medium">N-1</td>
                    <td>
                        <input type="number" name="saldo[saldo_n1]"
                               value="{{ $s->saldo_n1 ?? 0 }}" class="form-control form-control-sm" style="width:100px">
                    </td>
                    <td>
                        <input type="text" name="saldo[keterangan_n1]"
                               value="{{ $s->keterangan_n1 ?? '' }}" class="form-control form-control-sm" style="width:180px">
                    </td>
                </tr>
                <tr>
                    <td class="align-middle fw-medium">N</td>
                    <td>
                        <input type="number" name="saldo[saldo_n]"
                               value="{{ $s->saldo_n ?? 0 }}" class="form-control form-control-sm" style="width:100px">
                    </td>
                    <td>
                        <input type="text" name="saldo[keterangan_n]"
                               value="{{ $s->keterangan_n ?? '' }}" class="form-control form-control-sm" style="width:180px">
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="mt-3">
                <button class="btn btn-gradient btn-sm rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan Saldo Cuti
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
