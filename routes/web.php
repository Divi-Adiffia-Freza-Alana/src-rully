<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:produk.lihat')->group(function () {
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');

        Route::middleware('permission:produk.kelola')->group(function () {
            Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
            Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
            Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
            Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
            Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');
        });
    });

    Route::middleware('permission:stok.lihat')->group(function () {
        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/stok/{product}/riwayat', [StokController::class, 'riwayat'])->name('stok.riwayat');

        Route::middleware('permission:stok.kelola')->group(function () {
            Route::post('/stok/masuk', [StokController::class, 'storeMasuk'])->name('stok.masuk');
            Route::post('/stok/opname', [StokController::class, 'storeOpname'])->name('stok.opname');
        });
    });

    Route::middleware('permission:penjualan.lihat')->group(function () {
        Route::get('/penjualan/riwayat', [PenjualanController::class, 'riwayat'])->name('penjualan.riwayat');
        Route::get('/penjualan/{penjualan}/nota', [PenjualanController::class, 'nota'])->name('penjualan.nota');
    });

    Route::middleware('permission:penjualan.kelola')->group(function () {
        Route::get('/penjualan', [PenjualanController::class, 'create'])->name('penjualan.create');
        Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    });

    Route::middleware('permission:laporan.lihat')->group(function () {
        Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    });

    Route::middleware('permission:karyawan.lihat')->group(function () {
        Route::get('/karyawan', [UserController::class, 'index'])->name('karyawan.index');

        Route::middleware('permission:karyawan.kelola')->group(function () {
            Route::get('/karyawan/create', [UserController::class, 'create'])->name('karyawan.create');
            Route::post('/karyawan', [UserController::class, 'store'])->name('karyawan.store');
            Route::get('/karyawan/{karyawan}/edit', [UserController::class, 'edit'])->name('karyawan.edit');
            Route::put('/karyawan/{karyawan}', [UserController::class, 'update'])->name('karyawan.update');
            Route::delete('/karyawan/{karyawan}', [UserController::class, 'destroy'])->name('karyawan.destroy');
        });
    });

    Route::middleware('permission:role.lihat')->group(function () {
        Route::get('/role', [RoleController::class, 'index'])->name('role.index');

        Route::middleware('permission:role.kelola')->group(function () {
            Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
            Route::post('/role', [RoleController::class, 'store'])->name('role.store');
            Route::get('/role/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');
            Route::put('/role/{role}', [RoleController::class, 'update'])->name('role.update');
            Route::delete('/role/{role}', [RoleController::class, 'destroy'])->name('role.destroy');
        });
    });
});
