<?php

return [
    'roles' => [
        'SuperAdmin' => 'Akses penuh seluruh fitur dan data.',
        'Administrator' => 'Mengelola data utama, pengguna non-SuperAdmin, dan fitur administrasi.',
        'Pengurus' => 'Mengelola data operasional tertentu tanpa akses manajemen pengguna.',
        'Santri' => 'Mengakses data pribadi, jadwal, rekapan, QR code, dan absensi pribadi.',
    ],

    'permissions' => [
        'dashboard' => [
            'label' => 'Dashboard',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'santri.view' => [
            'label' => 'Data Pondok - Lihat',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'santri.create' => [
            'label' => 'Data Pondok - Tambah',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus'],
        ],
        'santri.edit' => [
            'label' => 'Data Pondok - Edit',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'santri.delete' => [
            'label' => 'Data Pondok - Hapus',
            'roles' => ['SuperAdmin', 'Administrator'],
        ],
        'users.view' => [
            'label' => 'Data Pengguna - Lihat',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'users.manage' => [
            'label' => 'Data Pengguna - Tambah/Hapus',
            'roles' => ['SuperAdmin', 'Administrator'],
        ],
        'superadmin.manage' => [
            'label' => 'Kelola Akun SuperAdmin',
            'roles' => ['SuperAdmin'],
        ],
        'attendance.manage' => [
            'label' => 'Daftar Kehadiran - Input/Edit',
            'roles' => ['SuperAdmin'],
        ],
        'attendance.scan' => [
            'label' => 'Scan Kehadiran',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus'],
        ],
        'attendance.report' => [
            'label' => 'Rekapan Kehadiran',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'qiyam.manage' => [
            'label' => 'Absensi Qiyamullail',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'qiyam.report' => [
            'label' => 'Rekapan Qiyamullail',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'schedule.view' => [
            'label' => 'Jadwal Pengajian - Lihat',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus', 'Santri'],
        ],
        'schedule.manage' => [
            'label' => 'Jadwal Pengajian - Tambah/Edit/Hapus',
            'roles' => ['SuperAdmin', 'Administrator', 'Pengurus'],
        ],
        'logs.view' => [
            'label' => 'Log Aktivitas',
            'roles' => ['SuperAdmin'],
        ],
        'rbac.manage' => [
            'label' => 'Menu RBAC',
            'roles' => ['SuperAdmin'],
            'locked' => ['SuperAdmin'],
        ],
    ],
];
