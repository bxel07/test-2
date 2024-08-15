<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', function () {
    return view('admin/dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('admin.dashboard');

Route::get('/user/dashboard', function () {
    return view('user/dashboard', );
})->middleware(['auth', 'verified', 'user'])->name('user.dashboard');


Route::middleware(['auth', 'admin'])->group(function () {
    // Route::resource('admin', AdminController::class);
    Route::get('/admin/upload', [UploadController::class, 'showUploadForm'])->name('admin.upload.form');
    Route::post('/admin/upload', [UploadController::class, 'upload'])->name('admin.upload');
    Route::get('/admin/uploads', [UploadController::class, 'index'])->name('admin.uploads.list');

});

Route::middleware(['auth', 'user'])->group(function () {
    Route::resource('user', UserController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
