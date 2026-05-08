<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#" target="_blank">PPM AL-MUSAWWA</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#" target="_blank">AM</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Starter</li>
            <li class="{{ (request()->routeIs('home*')) ? 'active' : '' }}">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>Home</span>
                </a>
            </li>
            {{-- @if(Auth::user()->role !='Santri') --}}
            <li class="{{ (request()->routeIs('santri*')) ? 'active' : '' }}">
                <a href="{{ route('santri.index') }}" class="nav-link">
                    <i class="fas fa-users"></i><span>Data Pondok</span>
                </a>
            </li>
            {{-- @endif --}}
            <li class="menu-header">User</li>
            <li class="{{ (request()->routeIs('pengguna*')) ? 'active' : '' }}">
                <a href="{{ route('pengguna.index') }}" class="nav-link">
                    <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                </a>
            </li>
            {{-- <li class="menu-header">Keuangan</li>
            <li class="{{ (request()->routeIs('biaya*')) ? 'active' : '' }}">
                <a href="{{ route('biaya.index') }}" class="nav-link">
                    <i class="far fa-file-alt"></i><span>Biaya Pembayaran</span>
                </a>
            </li>
            <li class="dropdown {{ (request()->routeIs('syahriah*') || request()->routeIs('registration*')) ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice"></i> <span>Pembayaran</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ (request()->routeIs('registration*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('registration.index') }}">Pendaftaran Baru</a>
                    </li>
                    <li class="{{ (request()->routeIs('syahriah*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('syahriah.index') }}">Syahriah (SPP)</a>
                    </li>
                </ul>
            </li>
            <li class="{{ (request()->routeIs('buku-kas*')) ? 'active' : '' }}">
                <a href="{{ route('buku-kas.index') }}" class="nav-link">
                    <i class="fas fa-book-open"></i><span>Buku Kas</span>
                </a>
            </li> --}}
            <li class="menu-header">Administrasi</li>
            {{-- <li class="{{ (request()->routeIs('surat-masuk*')) ? 'active' : '' }}">
                <a href="{{ route('surat-masuk.index') }}" class="nav-link">
                    <i class="fas fa-envelope"></i><span>Surat Masuk</span>
                </a>
            </li>
            <li class="{{ (request()->routeIs('surat-keluar*')) ? 'active' : '' }}">
                <a href="{{ route('surat-keluar.index') }}" class="nav-link">
                    <i class="fas fa-envelope-open-text"></i><span>Surat Keluar</span>
                </a>
            </li> --}}
            <li class="{{ (request()->routeIs('jadwal*')) ? 'active' : '' }}">
                <a href="{{ route('jadwal.index') }}" class="nav-link">
                    <i class="fas fa-calendar-alt"></i><span>Jadwal Pengajian</span>
                </a>
            </li>
            @if (Auth::user()->role == 'Superadmin')
            <li class="{{ (request()->routeIs('kehadiran.index') || request()->routeIs('kehadiran.store')) ? 'active' : '' }}">
                <a href="{{ route('kehadiran.index') }}" class="nav-link">
                    <i class="fas fa-clipboard-check"></i><span>Daftar Kehadiran</span>
                </a>
            </li>
            @endif
            <li class="{{ (request()->routeIs('kehadiran.report')) ? 'active' : '' }}">
                <a href="{{ route('kehadiran.report') }}" class="nav-link">
                    <i class="fas fa-chart-bar"></i><span>Rekapan Kehadiran</span>
                </a>
            </li>
            <li class="{{ (request()->routeIs('qiyam.index') || request()->routeIs('qiyam.store')) ? 'active' : '' }}">
                <a href="{{ route('qiyam.index') }}" class="nav-link">
                    <i class="fas fa-moon"></i><span>Absensi Qiyamullail</span>
                </a>
            </li>
            <li class="{{ (request()->routeIs('qiyam.report')) ? 'active' : '' }}">
                <a href="{{ route('qiyam.report') }}" class="nav-link">
                    <i class="fas fa-chart-bar"></i><span>Rekapan Qiyamullail</span>
                </a>
            </li>
            {{-- <li class="menu-header">Logs</li>
            <li class="{{ (request()->routeIs('logs.index')) ? 'active' : '' }}">
                <a href="{{ route('logs.index') }}" class="nav-link">
                    <i class="fas fa-history"></i><span>Log Aktivitas</span>
                </a>
            </li> --}}
        </ul>
    </aside>
</div>
