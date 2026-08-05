@extends('layouts.app')
@section('title', 'Kelola Saldo Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:#1a237e;"><i class="bi bi-journal-text me-2"></i>Kelola Saldo Cuti</h4>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Unit Kerja</th>
                        <th class="text-center" style="width:90px;">N-2</th>
                        <th class="text-center" style="width:90px;">N-1</th>
                        <th class="text-center" style="width:90px;">N</th>
                        <th class="text-center" style="width:90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawais as $p)
                    <tr>
                        <form method="POST" action="{{ route('pegawai.update-saldo') }}">
                            @csrf
                            <td>{{ $pegawais->firstItem() + $loop->index }}</td>
                            <td class="fw-medium">{{ $p->nama }}</td>
                            <td class="font-monospace small">{{ $p->nip }}</td>
                            <td>{{ $p->unit_kerja }}</td>
                            @php $s = $p->saldoCutis->first(); @endphp
                                <td class="text-center">
                                    <input type="number" name="saldo[{{ $p->nip }}][saldo_n2]"
                                           value="{{ $s->saldo_n2 ?? 0 }}" class="form-control form-control-sm text-center"
                                           min="0" style="width:70px;">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="saldo[{{ $p->nip }}][saldo_n1]"
                                           value="{{ $s->saldo_n1 ?? 0 }}" class="form-control form-control-sm text-center"
                                           min="0" style="width:70px;">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="saldo[{{ $p->nip }}][saldo_n]"
                                           value="{{ $s->saldo_n ?? 0 }}" class="form-control form-control-sm text-center"
                                           min="0" style="width:70px;">
                                </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary rounded-pill px-3" title="Simpan">
                                    <i class="bi bi-save"></i>
                                </button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox me-1"></i>Belum ada data pegawai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($pegawais, 'links'))
            <div class="p-3 border-top">{{ $pegawais->links() }}</div>
        @endif
    </div>
</div>
@endsection
