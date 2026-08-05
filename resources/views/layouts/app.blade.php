<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Cuti Pegawai')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --blue-deep: #1a237e;
            --blue-primary: #0d6efd;
            --blue-light: #e3f2fd;
            --blue-accent: #1565c0;
            --blue-gradient: linear-gradient(135deg, #1a237e 0%, #0d47a1 50%, #1565c0 100%);
        }
        body {
            background: #f0f4f8;
            min-height: 100vh;
        }
        .wrapper { flex: 1; }

        /* Layout sidebar */
        .layout { display: flex; min-height: 100vh; }
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .main-content .container { flex: 1; }

        .sidebar {
            background: var(--blue-gradient);
            width: 260px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            flex-shrink: 0;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar-brand:hover { color: #fff; }
        .sidebar .nav-link {
            color: rgba(255,255,255,.85);
            font-weight: 500;
            padding: 0.65rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.15);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.2);
        }
        .sidebar-user {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.15);
        }
        .sidebar-user .btn-logout {
            background: rgba(255,255,255,.15);
            border: none;
            color: #fff;
        }
        .sidebar-user .btn-logout:hover {
            background: rgba(255,255,255,.25);
            color: #fff;
        }
        .topbar-mobile {
            background: var(--blue-gradient);
            color: #fff;
        }
        .topbar-mobile .navbar-toggler { border-color: rgba(255,255,255,.4); }
        .topbar-mobile .navbar-toggler-icon { filter: invert(1); }

        /* Navbar (tidak dipakai lagi, dipertahankan utk kompatibilitas cetak) */

        /* Navbar */
        .navbar-custom {
            background: var(--blue-gradient);
            box-shadow: 0 2px 12px rgba(26,35,126,0.3);
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,.85);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .navbar-custom .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.15);
        }
        .navbar-custom .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.2);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            color: #fff;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            transition: box-shadow 0.2s;
        }
        .card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,.1);
        }
        .card-header-custom {
            background: var(--blue-gradient);
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
        }
        .card-header-custom i {
            margin-right: 6px;
        }
        .card .table th {
            background: #f8faff;
            color: var(--blue-deep);
            font-weight: 600;
            border-bottom: 2px solid var(--blue-light);
        }
        .card .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #fafbff;
        }

        /* Stat cards */
        .stat-card {
            border-radius: 12px;
            border-left: 4px solid;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,.1);
        }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* Buttons */
        .btn-gradient {
            background: var(--blue-gradient);
            color: #fff;
            border: none;
            transition: all 0.2s;
        }
        .btn-gradient:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13,110,253,0.4);
            color: #fff;
        }
        .btn-outline-primary {
            border-color: var(--blue-primary);
            color: var(--blue-primary);
        }
        .btn-outline-primary:hover {
            background: var(--blue-primary);
            color: #fff;
        }

        /* Badges per level */
        .badge-atasan-langsung { background: #0d6efd; color: #fff; }
        .badge-kasubag { background: #0dcaf0; color: #fff; }
        .badge-sekretaris { background: #fd7e14; color: #fff; }
        .badge-kepala-dinas { background: #198754; color: #fff; }
        .badge-walikota { background: #6f42c1; color: #fff; }
        .badge-pending { background: #6c757d; color: #fff; }
        .badge-diajukan { background: #6c757d; color: #fff; }
        .badge-disetujui { background: #198754; color: #fff; }
        .badge-ditolak { background: #dc3545; color: #fff; }

        /* Tombol setuju / tolak */
        .btn-approve { background: #198754; color: #fff; border: none; }
        .btn-approve:hover { background: #157347; color: #fff; }
        .btn-reject { background: #dc3545; color: #fff; border: none; }
        .btn-reject:hover { background: #b02a37; color: #fff; }
        .btn-approve-outline { background: #fff; color: #198754; border: 2px solid #198754; }
        .btn-approve-outline:hover { background: #198754; color: #fff; }
        .btn-reject-outline { background: #fff; color: #dc3545; border: 2px solid #dc3545; }
        .btn-reject-outline:hover { background: #dc3545; color: #fff; }

        /* Ikon status di tabel */
        .icon-pending { color: #6c757d; font-size: 1.2rem; }
        .icon-approved { color: #198754; font-size: 1.2rem; }
        .icon-rejected { color: #dc3545; font-size: 1.2rem; }
        .icon-changed { color: #0d6efd; font-size: 1.2rem; }
        .icon-postponed { color: #fd7e14; font-size: 1.2rem; }
        .text-status { font-size: .75rem; }

        /* Section headers in show */
        .section-atasan-langsung { border-left: 4px solid #0d6efd; }
        .section-kasubag { border-left: 4px solid #0dcaf0; }
        .section-sekretaris { border-left: 4px solid #fd7e14; }
        .section-kepala-dinas { border-left: 4px solid #198754; }
        .section-walikota { border-left: 4px solid #6f42c1; }
        .section-header {
            padding: 10px 16px;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
        }
        .section-body { padding: 12px 16px; }

        /* Footer */
        .footer-custom {
            background: var(--blue-deep);
            color: rgba(255,255,255,.7);
            font-size: 13px;
            padding: 14px 0;
            margin-top: auto;
        }

        /* Form */
        .form-control:focus, .form-select:focus {
            border-color: var(--blue-primary);
            box-shadow: 0 0 0 3px rgba(13,110,253,.15);
        }
        .form-label {
            font-weight: 600;
            color: #444;
            margin-bottom: 4px;
        }

        /* Alert */
        .alert {
            border-radius: 10px;
            border: none;
        }

        /* Pagination */
        .pagination .page-link {
            color: var(--blue-primary);
            border-radius: 8px;
            margin: 0 2px;
        }
        .pagination .page-item.active .page-link {
            background: var(--blue-gradient);
            border: none;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .card { box-shadow: none; }
        }
    </style>
</head>
<body>
@auth
<div class="layout">
    {{-- Sidebar kiri (desktop) --}}
    <aside class="sidebar d-none d-md-flex flex-column no-print">
        <a class="sidebar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-calendar-check-fill"></i> Cuti Pegawai
        </a>
        <div class="flex-grow-1">
            @include('layouts.partials.sidebar_menu')
        </div>
        <div class="sidebar-user">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</span>
                <span style="min-width:0;">
                    <div class="text-white text-truncate">{{ auth()->user()->nama }}</div>
                    <div class="text-white-50 small text-truncate">{{ auth()->user()->role }}</div>
                </span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-logout btn-sm w-100 rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        {{-- Topbar mobile --}}
        <nav class="navbar navbar-custom topbar-mobile d-md-none w-100 no-print">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="{{ route('dashboard') }}">
                    <i class="bi bi-calendar-check-fill me-1"></i> Cuti Pegawai
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>

        <div class="container py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show no-print">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show no-print">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="footer-custom text-center no-print">
            <div class="container">
                <i class="bi bi-building me-1"></i> Sistem Cuti Pegawai &mdash; Dinas Pertanian dan Pangan
            </div>
        </footer>
    </div>
</div>

{{-- Sidebar mobile (offcanvas) --}}
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarMobile" style="background:var(--blue-gradient);color:#fff;width:260px;">
    <div class="offcanvas-header">
        <span class="sidebar-brand" style="border-bottom:none;padding:0;">
            <i class="bi bi-calendar-check-fill"></i> Cuti Pegawai
        </span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="flex-grow-1">
            @include('layouts.partials.sidebar_menu')
        </div>
        <div class="sidebar-user">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</span>
                <span style="min-width:0;">
                    <div class="text-white text-truncate">{{ auth()->user()->nama }}</div>
                    <div class="text-white-50 small text-truncate">{{ auth()->user()->role }}</div>
                </span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-logout btn-sm w-100 rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </button>
            </form>
        </div>
    </div>
</div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
