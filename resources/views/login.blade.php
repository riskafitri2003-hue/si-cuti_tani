<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI-CUTI TANI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 50%, #1565c0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1a237e, #1565c0);
            padding: 30px 24px 24px;
            text-align: center;
            color: #fff;
        }
        .login-header .icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255,255,255,.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .login-header h4 {
            font-weight: 700;
            margin: 0;
        }
        .login-header p {
            opacity: .8;
            font-size: 13px;
            margin: 4px 0 0;
        }
        .login-body {
            padding: 28px 24px;
            background: #fff;
        }
        .form-control {
            border-radius: 10px;
            padding: 10px 14px;
            border: 2px solid #e0e0e0;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13,110,253,.12);
        }
        .input-group-custom {
            position: relative;
        }
        .input-group-custom .form-control {
            padding-left: 42px;
        }
        .input-group-custom .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 5;
        }
        .btn-login {
            background: linear-gradient(135deg, #1a237e, #1565c0);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 11px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(21,101,192,.4);
            color: #fff;
        }
        .demo-accounts {
            margin-top: 16px;
            padding: 12px 14px;
            background: #f8f9fa;
            border-radius: 10px;
            font-size: 12px;
            color: #888;
        }
        .demo-accounts summary {
            cursor: pointer;
            font-weight: 600;
            color: #666;
        }
        .demo-accounts code {
            color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="icon-wrap">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <h4>SI-CUTI TANI</h4>
            <p>Sistem Informasi Cuti Terintegrasi ASN Pertanian</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger py-2 mb-3">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="mb-3 input-group-custom">
                    <i class="bi bi-person-badge input-icon"></i>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" placeholder="Masukkan NIP" required autofocus>
                </div>
                <div class="mb-3 input-group-custom">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button class="btn-login" type="submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>
            <details class="demo-accounts">
                <summary><i class="bi bi-info-circle me-1"></i> Akun Contoh</summary>
                <div class="mt-2">
                    <div><code>admin</code> &mdash; Admin</div>
                    <div><code>198605102011012001</code> &mdash; Pegawai</div>
                    <div><code>atasan_langsung</code> &mdash; Atasan Langsung</div>
                    <div><code>kasubag</code> &mdash; Kasubag Umum</div>
                    <div><code>sekretaris</code> &mdash; Sekretaris</div>
                    <div><code>kepala_dinas</code> &mdash; Kepala Dinas</div>
                    <div><code>123456789</code> &mdash; User Baru</div>
                    <div class="mt-1 text-muted">Semua password: <code>password</code> (kecuali User Baru: <code>220703</code>)</div>
                </div>
            </details>
        </div>
    </div>
</body>
</html>
