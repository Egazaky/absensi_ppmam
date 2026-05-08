<?php

use \App\Http\Controllers\Web\CashBookController;
use \App\Http\Controllers\Web\CostController;
use App\Http\Controllers\Web\KehadiranController;
use App\Http\Controllers\Web\QiyamullailController;
use App\Http\Controllers\Web\LogActivityController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\InMailController;
use App\Http\Controllers\Web\OutMailController;
use \App\Http\Controllers\Web\RegistrationCostController;
use App\Http\Controllers\Web\SantriController;
use \App\Http\Controllers\Web\SyahriahController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ScheduleController;
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

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::resource('santri', SantriController::class);
    Route::resource('pengguna', UserController::class);
    Route::get('log-aktivitas', [LogActivityController::class, 'index'])->name('logs.index');

    // Biaya Pembayaran Pesantren
    Route::get('biaya', [CostController::class, 'index'])->name('biaya.index');
    Route::get('biaya/edit', [CostController::class, 'edit'])->name('biaya.edit');
    Route::patch('biaya/edit', [CostController::class, 'update'])->name('biaya.update');

    // Pembayaran Pendaftaran Santri
    Route::get('pembayaran-pendaftaran', [RegistrationCostController::class, 'index'])->name('registration.index');
    Route::get('pembayaran-pendaftaran/create', [RegistrationCostController::class, 'create'])->name('registration.create');
    Route::post('pembayaran-pendaftaran', [RegistrationCostController::class, 'store'])->name('registration.store');
    Route::get('pembayaran-pendaftaran/print/{id}', [RegistrationCostController::class, 'print'])->name('registration.print');
    Route::delete('pembayaran-pendaftaran/{id}', [RegistrationCostController::class, 'destroy'])->name('registration.destroy');

    // Syahriah (SPP)
    Route::get('syahriah', [SyahriahController::class, 'index'])->name('syahriah.index');
    Route::get('syahriah/create', [SyahriahController::class, 'create'])->name('syahriah.create');
    Route::post('syahriah', [SyahriahController::class, 'store'])->name('syahriah.store');
    Route::get('syahriah/print/{id}', [SyahriahController::class, 'print'])->name('syahriah.print');
    Route::delete('syahriah/{id}', [SyahriahController::class, 'destroy'])->name('syahriah.destroy');

    // Buku Kas
    Route::get('buku-kas', [CashBookController::class, 'index'])->name('buku-kas.index');
    Route::get('buku-kas/debit/create', [CashBookController::class, 'createDebit'])->name('buku-kas.debit.create');
    Route::post('buku-kas/debit', [CashBookController::class, 'storeDebit'])->name('buku-kas.debit.store');
    Route::get('buku-kas/credit/create', [CashBookController::class, 'createCredit'])->name('buku-kas.credit.create');
    Route::post('buku-kas/credit', [CashBookController::class, 'storeCredit'])->name('buku-kas.credit.store');
    Route::delete('buku-kas/{id}', [CashBookController::class, 'destroy'])->name('buku-kas.destroy');

    Route::resource('surat-masuk', InMailController::class);
    Route::resource('surat-keluar', OutMailController::class);

    // Daftar Kehadiran
    Route::get('kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
    Route::post('kehadiran', [KehadiranController::class, 'store'])->name('kehadiran.store');
    Route::post('kehadiran/toggle', [KehadiranController::class, 'toggle'])->name('kehadiran.toggle');
    // Debug route to inspect attendances for a date (SuperAdmin only)
    Route::get('kehadiran/debug/{date}', [KehadiranController::class, 'debugDate'])->name('kehadiran.debug');
    Route::get('kehadiran/scan', [KehadiranController::class, 'scan'])->name('kehadiran.scan');
    Route::get('kehadiran/qrcode/{id}', [KehadiranController::class, 'qrcode'])->name('kehadiran.qrcode');
    Route::get('rekapan-kehadiran', [KehadiranController::class, 'report'])->name('kehadiran.report');
    Route::get('rekapan-kehadiran/pdf', [KehadiranController::class, 'exportPdf'])->name('kehadiran.export.pdf');
    Route::get('rekapan-kehadiran/excel', [KehadiranController::class, 'exportExcel'])->name('kehadiran.export.excel');

    // Absensi Qiyamullail
    Route::get('absensi-qiyam', [QiyamullailController::class, 'index'])->name('qiyam.index');
    Route::post('absensi-qiyam', [QiyamullailController::class, 'store'])->name('qiyam.store');
    Route::get('rekapan-qiyam', [QiyamullailController::class, 'report'])->name('qiyam.report');

    Route::resource('jadwal', ScheduleController::class);
});
