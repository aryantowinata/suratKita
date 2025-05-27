<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\PegawaiMiddleware;
use App\Http\Middleware\PimpinanMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['redirect.auth'])->group(function () {
    Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/auth/postLogin', [LoginController::class, 'postLogin'])->name('auth.postLogin');
    Route::get('/login/token/{token}', [LoginController::class, 'loginWithToken'])->name('login.token');

});

Route::middleware([AdminMiddleware::class])->name('admin.')->group(function () {
    Route::get('/admin/surat-masuk/download/{id}', [AdminController::class, 'download'])->name('surat-masuk.download');
    Route::get('/admin/laporan/cetak-pdf', [AdminController::class, 'cetakPDF'])->name('laporan.pdf');
    Route::put('/admin/surat-masuk/update-role/{id}', [AdminController::class, 'updateRole'])->name('surat-masuk.updateRole');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboardAdmin'])->name('dashboard');
    Route::get('/admin/suratMasuk', [AdminController::class, 'suratMasuk'])->name('suratMasuk');
    Route::get('/admin/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/admin/bidang', [AdminController::class, 'bidang'])->name('bidang');
    Route::post('/admin/bidang', [AdminController::class, 'storeBidang'])->name('storeBidang');
    Route::put('/admin/bidang/update/{id}', [AdminController::class, 'updateBidang'])->name('updateBidang');
    Route::delete('/admin/hapusBidang/{id}', [AdminController::class, 'hapusBidang'])->name('hapusBidang');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('logout');
    Route::post('/admin/storeSuratMasuk', [AdminController::class, 'storeSuratMasuk'])->name('storeSuratMasuk');
    Route::put('/admin/suratMasuk/update/{id}', [AdminController::class, 'updateSuratMasuk'])->name('updateSuratMasuk');
    Route::delete('/admin/hapusSuratMasuk/{id}', [AdminController::class, 'hapusSuratMasuk'])->name('hapusSuratMasuk');
    Route::get('/admin/arsipSurat', [AdminController::class, 'arsipSurat'])->name('arsipSurat');
    Route::get('/admin/arsipSurat/download/{id}', [AdminController::class, 'downloadArsipSurat'])->name('downloadArsipSurat');
    Route::put('/admin/arsipSurat/update-status/{id}', [AdminController::class, 'updateStatusArsip'])->name('updateStatusArsip');
    Route::put('/admin/suratMasuk/status/{id}', [AdminController::class, 'updateStatusSuratMasuk'])->name('updateStatusSuratMasuk');
    Route::put('/admin/arsipSurat/update/{id}', [AdminController::class, 'updateArsipSurat'])->name('updateArsipSurat');
    Route::delete('/admin/arsipSurat/hapus/{id}', [AdminController::class, 'hapusArsipSurat'])->name('hapusArsipSurat');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('users');
    Route::post('/admin/storeUsers', [AdminController::class, 'storeUsers'])->name('storeUsers');
    Route::put('/admin/users/update/{id}', [AdminController::class, 'updateUsers'])->name('updateUsers');
    Route::delete('/admin/users/hapus/{id}', [AdminController::class, 'hapusUsers'])->name('hapusUsers');
    Route::get('/admin/profile', [AdminController::class, 'profileAdmin'])->name('profile');
    Route::post('/admin/profile/update', [AdminController::class, 'updateProfileAdmin'])->name('updateProfileAdmin');
    Route::post('/admin/update-password', [AdminController::class, 'updatePasswordAdmin'])->name('updatePasswordAdmin');
    Route::put('/admin/suratKeluar/status/{id}', [AdminController::class, 'updateStatusSuratKeluar'])->name('updateStatusSuratKeluar');
    Route::get('/admin/surat-keluar/download/{id}', [AdminController::class, 'downloadSuratKeluar'])->name('surat-keluar.download');
    Route::get('/admin/suratKeluar', [AdminController::class, 'suratKeluar'])->name('suratKeluar');
    Route::post('/admin/storeSuratKeluar', [AdminController::class, 'storeSuratKeluar'])->name('storeSuratKeluar');
    Route::put('/admin/suratKeluar/update/{id}', [AdminController::class, 'updateSuratKeluar'])->name('updateSuratKeluar');
    Route::delete('/admin/hapusSuratkeluar/{id}', [AdminController::class, 'hapusSuratKeluar'])->name('hapusSuratKeluar');
    Route::put('/admin/suratMasuk/{id}/update-role', [AdminController::class, 'updateRole'])->name('updateRoleSuratMasuk');
});

Route::middleware([PimpinanMiddleware::class])->name('pimpinan.')->group(function () {
    Route::get('/pimpinan/laporan', [PimpinanController::class, 'laporan'])->name('laporan');
    Route::get('/pimpinan/laporan/cetak-pdf', [PimpinanController::class, 'cetakPDF'])->name('laporan.pdf');
    Route::post('/pimpinan/disposisiSuratMasuk/storeInstruksi', [PimpinanController::class, 'storeInstruksi'])->name('storeInstruksi');
    Route::get('/pimpinan/surat-keluar/download/{id}', [PimpinanController::class, 'downloadSuratKeluar'])->name('surat-keluar.download');
    Route::get('/pimpinan/dashboard', [PimpinanController::class, 'dashboardPimpinan'])->name('dashboard');
    Route::post('/pimpinan/logout', [PimpinanController::class, 'logout'])->name('logout');
    Route::get('/pimpinan/disposisiSuratMasuk', [PimpinanController::class, 'disposisiSuratMasuk'])->name('disposisiSuratMasuk');
    Route::get('/pimpinan/disposisiSuratKeluar', [PimpinanController::class, 'disposisiSuratKeluar'])->name('disposisiSuratKeluar');
    Route::get('/pimpinan/surat-masuk/download/{id}', [PimpinanController::class, 'download'])->name('surat-masuk.download');
    Route::put('/pimpinan/disposisiSuratMasuk/statusSuratMasuk/{id}', [PimpinanController::class, 'updateStatusSuratMasuk'])->name('updateStatusSuratMasuk');
    Route::put('/pimpinan/disposisiSuratKeluar/statusSuratKeluar/{id}', [PimpinanController::class, 'updateStatusSuratKeluar'])->name('updateStatusSuratKeluar');
    Route::put('/pimpinan/disposisiSuratMasuk/statusDisposisi/{id}', [PimpinanController::class, 'updateStatusDisposisi'])->name('updateStatusDisposisi');
    Route::put('/pimpinan/disposisiSuratMasuk/bidang/{id}', [PimpinanController::class, 'updateBidang'])->name('updateBidang');
    Route::get('/pimpinan/profile', [PimpinanController::class, 'profilePimpinan'])->name('profile');
    Route::post('/pimpinan/profile/update', [PimpinanController::class, 'updateProfilePimpinan'])->name('updateProfilePimpinan');
    Route::post('/pimpinan/update-password', [PimpinanController::class, 'updatePasswordPimpinan'])->name('updatePasswordPimpinan');
    Route::put('/pimpinan/disposisiSuratKeluar/{id}/update-instruksi', [PimpinanController::class, 'updateInstruksi'])->name('updateInstruksi');
    Route::post('/pimpinan/disposisiSuratKeluar/storeSuratKeluar', [PimpinanController::class, 'storeSuratKeluar'])->name('storeSuratKeluar');
    Route::put('/pimpinan/disposisiSuratKeluar/update/{id}', [PimpinanController::class, 'updateDisposisiSuratKeluar'])->name('updateDisposisiSuratKeluar');
    Route::delete('/pimpinan/disposisiSuratKeluar/delete/{id}', [PimpinanController::class, 'hapusDisposisiSuratKeluar'])->name('hapusDisposisiSuratKeluar');

});

Route::middleware([PegawaiMiddleware::class])->name('pegawai.')->group(function () {
    Route::get('/pegawai/laporan', [PegawaiController::class, 'laporan'])->name('laporan');
    Route::get('/pegawai/laporan/cetak-pdf', [PegawaiController::class, 'cetakPDF'])->name('laporan.pdf');
    Route::get('/pegawai/dashboard', [PegawaiController::class, 'dashboardPegawai'])->name('dashboard');
    Route::post('/pegawai/logout', [PegawaiController::class, 'logout'])->name('logout');
    Route::get('/pegawai/profile', [PegawaiController::class, 'profilePegawai'])->name('profile');
    Route::post('/pegawai/profile/update', [PegawaiController::class, 'updateProfilePegawai'])->name('updateProfilePegawai');
    Route::post('/pegawai/update-password', [PegawaiController::class, 'updatePasswordPegawai'])->name('updatePasswordPegawai');
    Route::get('/pegawai/disposisiSuratMasuk', [PegawaiController::class, 'disposisiSuratMasuk'])->name('disposisiSuratMasuk');
    Route::get('/pegawai/disposisiSuratKeluar', [PegawaiController::class, 'disposisiSuratKeluar'])->name('disposisiSuratKeluar');
    Route::get('/pegawai/surat-masuk/download/{id}', [PegawaiController::class, 'download'])->name('surat-masuk.download');
    Route::put('/pegawai/disposisiSuratMasuk/statusSuratMasuk/{id}', [PegawaiController::class, 'updateStatusSuratMasuk'])->name('updateStatusSuratMasuk');
    Route::put('/pegawai/disposisiSuratKeluar/statusSuratKeluar/{id}', [PegawaiController::class, 'updateStatusSuratKeluar'])->name('updateStatusSuratKeluar');
    Route::put('/pegawai/disposisiSuratMasuk/statusDisposisi/{id}', [PegawaiController::class, 'updateStatusDisposisi'])->name('updateStatusDisposisi');
    Route::put('/pegawai/disposisiSuratMasuk/bidang/{id}', [PegawaiController::class, 'updateBidang'])->name('updateBidang');
    Route::delete('/pegawai/hapusDisposisiSuratMasuk/{id}', [PegawaiController::class, 'hapusDisposisiSuratMasuk'])->name('hapusDisposisiSuratMasuk');
    Route::delete('/pegawai/hapusDisposisiSuratKeluar/{id}', [PegawaiController::class, 'hapusDisposisiSuratKeluar'])->name('hapusDisposisiSuratKeluar');
    Route::get('/pegawai/surat-keluar/download/{id}', [PegawaiController::class, 'downloadSuratKeluar'])->name('surat-keluar.download');
    Route::put('/pegawai/disposisi/{id}/update-instruksi', [PegawaiController::class, 'updateInstruksi'])->name('updateInstruksi');
});