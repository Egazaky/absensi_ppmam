<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\KehadiranController;
use App\Http\Controllers\Web\LogActivityController;
use App\Http\Controllers\Web\QiyamullailController;
use App\Http\Controllers\Web\RbacController;
use App\Http\Controllers\Web\SantriController;
use App\Http\Controllers\Web\ScheduleController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('home');
});

// TEMPORARY DEBUG ROUTE - remove after fixing
Route::get('/debug-db', function () {
    $users = \App\Models\User::select('email', 'role')->get();
    $santriCount = \App\Models\Santri::count();
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');

    return response()->json([
        'users' => $users,
        'santri_count' => $santriCount,
        'tables' => $tables,
        'db_connection' => config('database.default'),
        'db_host' => config('database.connections.mysql.host'),
        'mysql_url_set' => !empty(env('MYSQL_URL')),
    ]);
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home')
        ->middleware('permission:dashboard');
    Route::resource('santri', SantriController::class)->only(['create', 'store'])
        ->middleware('permission:santri.create');
    Route::resource('santri', SantriController::class)->only(['index', 'show'])
        ->middleware('permission:santri.view');
    Route::resource('santri', SantriController::class)->only(['edit', 'update'])
        ->middleware('permission:santri.edit');
    Route::delete('santri/{santri}', [SantriController::class, 'destroy'])->name('santri.destroy')
        ->middleware('permission:santri.delete');

    Route::resource('pengguna', UserController::class)->only(['index', 'edit', 'update'])
        ->middleware('permission:users.view');
    Route::resource('pengguna', UserController::class)->only(['create', 'store', 'destroy'])
        ->middleware('permission:users.manage');
    Route::get('log-aktivitas', [LogActivityController::class, 'index'])->name('logs.index')
        ->middleware('permission:logs.view');
    Route::get('rbac', [RbacController::class, 'index'])->name('rbac.index')
        ->middleware('permission:rbac.manage');
    Route::post('rbac/toggle', [RbacController::class, 'toggle'])->name('rbac.toggle')
        ->middleware('permission:rbac.manage');

    // Daftar Kehadiran
    Route::get('kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index')
        ->middleware('permission:attendance.manage');
    Route::post('kehadiran', [KehadiranController::class, 'store'])->name('kehadiran.store')
        ->middleware('permission:attendance.manage');
    Route::post('kehadiran/toggle', [KehadiranController::class, 'toggle'])->name('kehadiran.toggle')
        ->middleware('permission:attendance.scan');
    // Debug route to inspect attendances for a date (SuperAdmin only)
    Route::get('kehadiran/debug/{date}', [KehadiranController::class, 'debugDate'])->name('kehadiran.debug')
        ->middleware('permission:attendance.manage');
    Route::get('kehadiran/scan', [KehadiranController::class, 'scan'])->name('kehadiran.scan')
        ->middleware('permission:attendance.scan');
    Route::get('kehadiran/qrcode/{id}', [KehadiranController::class, 'qrcode'])->name('kehadiran.qrcode')
        ->middleware('permission:santri.view');
    Route::get('rekapan-kehadiran', [KehadiranController::class, 'report'])->name('kehadiran.report')
        ->middleware('permission:attendance.report');
    Route::get('rekapan-kehadiran/pdf', [KehadiranController::class, 'exportPdf'])->name('kehadiran.export.pdf')
        ->middleware('permission:attendance.report');
    Route::get('rekapan-kehadiran/excel', [KehadiranController::class, 'exportExcel'])->name('kehadiran.export.excel')
        ->middleware('permission:attendance.report');

    // Absensi Qiyamullail
    Route::get('absensi-qiyam', [QiyamullailController::class, 'index'])->name('qiyam.index')
        ->middleware(['permission:qiyam.manage', 'qiyam.time']);
    Route::post('absensi-qiyam', [QiyamullailController::class, 'store'])->name('qiyam.store')
        ->middleware(['permission:qiyam.manage', 'qiyam.time']);
    Route::get('rekapan-qiyam', [QiyamullailController::class, 'report'])->name('qiyam.report')
        ->middleware('permission:qiyam.report');
    Route::get('rekapan-qiyam/pdf', [QiyamullailController::class, 'exportPdf'])->name('qiyam.export.pdf')
        ->middleware('permission:qiyam.report');
    Route::get('rekapan-qiyam/excel', [QiyamullailController::class, 'exportExcel'])->name('qiyam.export.excel')
        ->middleware('permission:qiyam.report');

    Route::get('jadwal', [ScheduleController::class, 'index'])->name('jadwal.index')
        ->middleware('permission:schedule.view');
    Route::get('jadwal/create', [ScheduleController::class, 'create'])->name('jadwal.create')
        ->middleware('permission:schedule.manage');
    Route::post('jadwal', [ScheduleController::class, 'store'])->name('jadwal.store')
        ->middleware('permission:schedule.manage');
    Route::get('jadwal/{jadwal}/edit', [ScheduleController::class, 'edit'])->name('jadwal.edit')
        ->whereUuid('jadwal')
        ->middleware('permission:schedule.manage');
    Route::put('jadwal/{jadwal}', [ScheduleController::class, 'update'])->name('jadwal.update')
        ->whereUuid('jadwal')
        ->middleware('permission:schedule.manage');
    Route::patch('jadwal/{jadwal}', [ScheduleController::class, 'update'])
        ->whereUuid('jadwal')
        ->middleware('permission:schedule.manage');
    Route::delete('jadwal/{jadwal}', [ScheduleController::class, 'destroy'])->name('jadwal.destroy')
        ->whereUuid('jadwal')
        ->middleware('permission:schedule.manage');
    Route::get('jadwal/{jadwal}', [ScheduleController::class, 'show'])->name('jadwal.show')
        ->whereUuid('jadwal')
        ->middleware('permission:schedule.view');
});
