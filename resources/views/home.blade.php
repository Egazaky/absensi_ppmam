@extends('layouts.home')
@section('title_page','Dashboard')
@section('content')

<style>
    .dashboard-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #1e2a4a 100%);
        border-radius: 16px;
        color: #fff;
        padding: 32px;
        margin-bottom: 28px;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(79, 140, 255, 0.1);
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(79, 140, 255, 0.06);
        border-radius: 50%;
        bottom: -120px;
        right: -80px;
    }

    .dashboard-hero::before {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        background: rgba(79, 140, 255, 0.04);
        border-radius: 50%;
        top: -70px;
        right: 60px;
    }

    .dashboard-hero h2 {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.02em;
        position: relative;
        z-index: 2;
        color: #f1f5f9 !important;
    }

    .dashboard-hero p {
        margin-bottom: 0;
        opacity: .8;
        font-size: 15px;
        position: relative;
        z-index: 2;
        color: #94a3b8 !important;
    }

    .dashboard-stat {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        background: var(--bg-card);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dashboard-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.25) !important;
    }

    .dashboard-stat .stat-icon {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-bottom: 16px;
        font-size: 18px;
    }

    .dashboard-stat .stat-icon.bg-primary-soft {
        background-color: rgba(79, 140, 255, 0.12);
        color: var(--primary);
    }

    .dashboard-stat .stat-icon.bg-info-soft {
        background-color: rgba(6, 182, 212, 0.12);
        color: var(--info);
    }

    .dashboard-stat .stat-icon.bg-warning-soft {
        background-color: rgba(245, 158, 11, 0.12);
        color: var(--warning);
    }

    .dashboard-stat .stat-icon.bg-success-soft {
        background-color: rgba(34, 197, 94, 0.12);
        color: var(--success);
    }

    .dashboard-stat .stat-label {
        color: var(--text-dim);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .dashboard-stat .stat-value {
        color: var(--text-main);
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
    }

    .quick-actions {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        margin-top: 10px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.15);
    }

    .quick-actions h5 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 16px;
    }

    .quick-actions .btn {
        margin: 0 10px 10px 0;
        padding: 10px 20px !important;
    }
</style>

<div class="dashboard-hero">
    <h2>Selamat datang, {{ Auth::user()->santris?->name ?? Auth::user()->role }}</h2>
    <p>Ringkasan data pondok dan aktivitas kehadiran hari ini.</p>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        <div class="dashboard-stat">
            <div class="stat-icon bg-primary-soft">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label">Santri</div>
            <div class="stat-value">{{ $santri }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        <div class="dashboard-stat">
            <div class="stat-icon bg-info-soft">
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="stat-label">Pengguna</div>
            <div class="stat-value">{{ $users }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        <div class="dashboard-stat">
            <div class="stat-icon bg-warning-soft">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-label">Jadwal Pengajian</div>
            <div class="stat-value">{{ $schedules }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        <div class="dashboard-stat">
            <div class="stat-icon bg-success-soft">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-label">Kehadiran Hari Ini</div>
            <div class="stat-value">{{ $todayAttendances }}</div>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h5>Akses Cepat</h5>
    @if(Auth::user()->canAccess('santri.view'))
        <a href="{{ route('santri.index') }}" class="btn btn-primary"><i class="fas fa-users"></i> Data Pondok</a>
    @endif
    @if(Auth::user()->canAccess('schedule.view'))
        <a href="{{ route('jadwal.index') }}" class="btn btn-warning"><i class="fas fa-calendar-alt"></i> Jadwal</a>
    @endif
    @if(Auth::user()->canAccess('attendance.report'))
        <a href="{{ route('kehadiran.report') }}" class="btn btn-success"><i class="fas fa-chart-bar"></i> Rekapan Kehadiran</a>
    @endif
    @if(Auth::user()->canAccess('qiyam.report'))
        <a href="{{ route('qiyam.report') }}" class="btn btn-info"><i class="fas fa-moon"></i> Rekapan Qiyam</a>
    @endif
</div>

@endsection
