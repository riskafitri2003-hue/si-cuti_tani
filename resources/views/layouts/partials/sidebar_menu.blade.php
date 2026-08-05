<ul class="nav flex-column gap-1 p-2">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-grid-fill me-1"></i>Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('cuti.*') ? 'active' : '' }}" href="{{ route('cuti.index') }}">
            <i class="bi bi-file-text-fill me-1"></i>Pengajuan Cuti
        </a>
    </li>
    @if(auth()->user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
            <i class="bi bi-bar-chart-fill me-1"></i>Laporan
        </a>
    </li>
    @endif
    @if(auth()->user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('saran.*') ? 'active' : '' }}" href="{{ route('saran.index') }}">
            <i class="bi bi-lightbulb-fill me-1"></i>Saran/Masukan
        </a>
    </li>
    @endif
    @if(auth()->user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pegawai.kelola-akun') ? 'active' : '' }}" href="{{ route('pegawai.kelola-akun') }}">
            <i class="bi bi-shield-lock me-1"></i>Kelola Akun
        </a>
    </li>
    @endif
    @if(auth()->user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pegawai.kelola-saldo') ? 'active' : '' }}" href="{{ route('pegawai.kelola-saldo') }}">
            <i class="bi bi-journal-text me-1"></i>Kelola Saldo
        </a>
    </li>
    @endif
    @if(auth()->user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}" href="{{ route('pegawai.index') }}">
            <i class="bi bi-people-fill me-1"></i>Data Pegawai
        </a>
    </li>
    @endif
</ul>
