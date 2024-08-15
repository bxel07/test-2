<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UploadController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/admin/dashboard', function () {
    return view('admin/dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('admin.dashboard');

Route::get('/user/dashboard', function () {
    return view('user/dashboard', );
})->middleware(['auth', 'verified', 'user'])->name('user.dashboard');


Route::middleware(['auth', 'admin'])->group(function () {
    // user management crud
    Route::resource('admin/user-management', UserManagementController::class);


    // page dashboard admin
    Route::get('/admin/upload', [UploadController::class, 'showUploadForm'])->name('admin.upload.form');
    Route::post('/admin/upload', [UploadController::class, 'upload'])->name('admin.upload');
    Route::get('/admin/uploads', [UploadController::class, 'index'])->name('admin.uploads.list');

    // export
    Route::get('documents/{id}/export/pdf', [UploadController::class, 'exportPdf'])->name('documents.export.pdf');
    Route::get('documents/{id}/export/excel', [UploadController::class, 'exportExcel'])->name('documents.export.excel');
    Route::get('/documents/{id}', [UploadController::class, 'show'])->name('documents.show');
    Route::get('/batch-download', [UploadController::class, 'batchDownload'])->name('batch.download');



});

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/user/document', [DashboardController::class,'index'])->name('user.index');
    Route::get('/user/document/{id}', [DashboardController::class,'show'])->name('user.show');
    Route::get('/user/export-pdf/{id}', [DashboardController::class,'exportPDF'])->name('user.pdf');
    Route::get('/user/export-excel/{id}', [DashboardController::class,'exportExcel'])->name('user.excel');
    Route::get('/user/export-batch', [DashboardController::class,'batchDownload'])->name('user.batch');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
