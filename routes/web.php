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
use App\Http\Controllers\LaporanController; 

// Rute untuk autentikasi (login, logout, register)
Route::get('/login', [LoginController::class, 'loginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rute untuk registrasi
Route::get('/register', [RegisterController::class, 'registerForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// Rute yang membutuhkan autentikasi
// Semua rute di dalam grup ini hanya bisa diakses oleh pengguna yang sudah login
Route::middleware(['auth'])->group(function () {

    // Rute untuk halaman utama (Dashboard)
    Route::get('/admin-dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute untuk manajemen Pendapatan
    Route::resource('pendapatan', PendapatanController::class);

    // Rute untuk manajemen Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class);

    // Rute Barang(item)
    Route::resource('barang', BarangController::class);

    // Rute Stok
    Route::get('stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('stok/create', [StokController::class, 'create'])->name('stok.create');
    Route::post('stok', [StokController::class, 'store'])->name('stok.store');

    // Rute Unit
    Route::resource('unit', UnitController::class);

    // Rute Rekap dan Laporan
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel'); // Tambahkan rute ini
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf'); // Tambahkan rute ini

    // Rute Struk
    Route::get('/struk', [StrukController::class, 'index'])->name('struk.index');
    Route::get('/struk/{jenis}/{id}', [StrukController::class, 'show'])->name('struk.show');

});

// Rute utama (homepage) yang mengarahkan ke halaman login jika belum terautentikasi
Route::get('/', function () {
    return redirect()->route('login.form');
});