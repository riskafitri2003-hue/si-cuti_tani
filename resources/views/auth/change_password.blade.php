@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header-custom">
                <i class="bi bi-key"></i> Ganti Password
            </div>
            <div class="card-body">
                <div class="mb-3 small text-muted">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ auth()->user()->nama }} ({{ auth()->user()->roleNames() }})
                </div>
                <form method="POST" action="{{ route('password.change.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Lama</label>
                        <input type="password" name="current_password" id="current_password"
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" name="new_password" id="new_password"
                               class="form-control @error('new_password') is-invalid @enderror" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                               class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-gradient">
                            <i class="bi bi-check-circle me-1"></i> Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
