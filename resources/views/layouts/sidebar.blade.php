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
            @if(Auth::user()->canAccess('santri.view'))
            <li class="{{ (request()->routeIs('santri*')) ? 'active' : '' }}">
                <a href="{{ route('santri.index') }}" class="nav-link">
                    <i class="fas fa-users"></i><span>Data Pondok</span>
                </a>
            </li>
            @endif
            <li class="menu-header">User</li>
            @if(Auth::user()->canAccess('users.view'))
            <li class="{{ (request()->routeIs('pengguna*')) ? 'active' : '' }}">
                <a href="{{ route('pengguna.index') }}" class="nav-link">
                    <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->canAccess('rbac.manage'))
            <li class="{{ (request()->routeIs('rbac*')) ? 'active' : '' }}">
                <a href="{{ route('rbac.index') }}" class="nav-link">
                    <i class="fas fa-user-shield"></i><span>RBAC</span>
                </a>
            </li>
            @endif
            <li class="menu-header">Administrasi</li>
            @if(Auth::user()->canAccess('schedule.view'))
            <li class="{{ (request()->routeIs('jadwal*')) ? 'active' : '' }}">
                <a href="{{ route('jadwal.index') }}" class="nav-link">
                    <i class="fas fa-calendar-alt"></i><span>Jadwal Pengajian</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->canAccess('attendance.manage'))
            <li class="{{ (request()->routeIs('kehadiran.index') || request()->routeIs('kehadiran.store')) ? 'active' : '' }}">
                <a href="{{ route('kehadiran.index') }}" class="nav-link">
                    <i class="fas fa-clipboard-check"></i><span>Daftar Kehadiran</span>
                </a>
            </li>
            @endif
            @if(Auth::user()->canAccess('attendance.report'))
            <li class="{{ (request()->routeIs('kehadiran.report')) ? 'active' : '' }}">
                <a href="{{ route('kehadiran.report') }}" class="nav-link">
                    <i class="fas fa-chart-bar"></i><span>Rekapan Kehadiran</span>
                </a>
            </li>
            @endif
            @if(Auth::user()->canAccess('qiyam.manage'))
            <li class="{{ (request()->routeIs('qiyam.index') || request()->routeIs('qiyam.store')) ? 'active' : '' }}">
                <a href="{{ route('qiyam.index') }}" class="nav-link">
                    <i class="fas fa-moon"></i><span>Absensi Qiyamullail</span>
                </a>
            </li>
            @endif
            @if(Auth::user()->canAccess('qiyam.report'))
            <li class="{{ (request()->routeIs('qiyam.report')) ? 'active' : '' }}">
                <a href="{{ route('qiyam.report') }}" class="nav-link">
                    <i class="fas fa-chart-bar"></i><span>Rekapan Qiyamullail</span>
                </a>
            </li>
            @endif
            @if(Auth::user()->canAccess('logs.view'))
            <li class="menu-header">Logs</li>
            <li class="{{ (request()->routeIs('logs.index')) ? 'active' : '' }}">
                <a href="{{ route('logs.index') }}" class="nav-link">
                    <i class="fas fa-history"></i><span>Log Aktivitas</span>
                </a>
            </li>
            @endif
        </ul>
    </aside>
</div>
