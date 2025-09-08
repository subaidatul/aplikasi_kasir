<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\StrukController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini tempat Anda bisa mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam sebuah grup
| yang berisi middleware "web". Sekarang buatlah sesuatu yang hebat!
|
*/

// Rute Halaman Utama (Redirect ke Halaman Login)
Route::get('/', function () {
    return redirect()->route('login.form');
});

// Grup Rute untuk Autentikasi
Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', [LoginController::class, 'loginForm'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    
    // Rute Registrasi
    Route::get('/register', [RegisterController::class, 'registerForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    // Rute Lupa Sandi
    Route::get('forgot-password', [PasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [PasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [PasswordController::class, 'reset'])->name('password.update');
});

// Rute untuk Logout (Harus terautentikasi)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Grup Rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {

    // Grup Rute untuk Hak Akses 'admin'
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Rute untuk manajemen Pengeluaran
        Route::resource('pengeluaran', PengeluaranController::class);

        // Rute Barang (item)
        Route::resource('barang', BarangController::class);

        // Rute Unit
        Route::resource('unit', UnitController::class);

        // Rute Stok
        Route::get('stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('stok/create', [StokController::class, 'create'])->name('stok.create');
        Route::post('stok', [StokController::class, 'store'])->name('stok.store');
        Route::get('stok/{stok}/edit', [StokController::class, 'edit'])->name('stok.edit');
        Route::put('stok/{stok}', [StokController::class, 'update'])->name('stok.update');
        Route::delete('stok/{stok}', [StokController::class, 'destroy'])->name('stok.destroy');
        Route::get('stok/keluar/create', [StokController::class, 'createStokKeluar'])->name('stok.create_stok_keluar');
        Route::post('stok/keluar', [StokController::class, 'storeStokKeluar'])->name('stok.storeStokKeluar');

        // Rute Rekap dan Laporan
        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

        // Rute Struk
        Route::get('/struk', [StrukController::class, 'index'])->name('struk.index');
        Route::get('/struk/{jenis}/{id}', [StrukController::class, 'show'])->name('struk.show');

        // Rute untuk manajemen Akun
        Route::get('/accounts', [AccountController::class, 'index'])->name('account.index');
        Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('account.destroy');
    });

    // Grup Rute untuk Hak Akses 'admin' dan 'pegawai'
    Route::middleware('role:admin,pegawai')->group(function () {
        // Rute untuk halaman utama (Dashboard)
       Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Rute untuk manajemen Pendapatan
        Route::resource('pendapatan', PendapatanController::class);

         // Rute untuk mencetak struk pendapatan
        Route::get('/pendapatan/{id}/cetak_struk', [PendapatanController::class, 'cetakStruk'])->name('pendapatan.cetakStruk');

    });

});