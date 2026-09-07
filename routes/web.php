<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebcamController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfileController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('webcam', [WebcamController::class, 'index'])->name('webcam.index');
    Route::post('webcam', [WebcamController::class, 'store'])->name('webcam.capture');
    Route::get('webcam/download/{filename}', [WebcamController::class, 'download'])->name('webcam.download');
    Route::delete('webcam/{filename}', [WebcamController::class, 'destroy'])->name('webcam.destroy');

    Route::get('gallery', [ImageController::class, 'index'])->name('gallery.index');
    Route::get('gallery/trash', [ImageController::class, 'trash'])->name('gallery.trash');
    Route::post('gallery/restore/{id}', [ImageController::class, 'restore'])->name('gallery.restore');
    Route::delete('gallery/force-delete/{id}', [ImageController::class, 'forceDelete'])->name('gallery.forceDelete');
    Route::post('gallery/bulk-delete', [ImageController::class, 'bulkDelete'])->name('gallery.bulkDelete');
    Route::post('gallery/bulk-restore', [ImageController::class, 'bulkRestore'])->name('gallery.bulkRestore');
    Route::post('gallery/bulk-force-delete', [ImageController::class, 'bulkForceDelete'])->name('gallery.bulkForceDelete');
    Route::post('gallery/tags/{id}', [ImageController::class, 'updateTags'])->name('gallery.tags');
    Route::post('gallery/caption/{id}', [ImageController::class, 'updateCaption'])->name('gallery.caption');
    Route::post('gallery/rename/{id}', [ImageController::class, 'rename'])->name('gallery.rename');
    Route::post('gallery/share/{id}', [ImageController::class, 'share'])->name('gallery.share');
    Route::get('gallery/download/{id}', [ImageController::class, 'download'])->name('gallery.download');
    Route::get('gallery/export/{id}/{format?}', [ImageController::class, 'export'])->name('gallery.export');
    Route::get('gallery/print/{id}', [ImageController::class, 'print'])->name('gallery.print');
    Route::get('gallery/lightbox/{id}', [ImageController::class, 'lightbox'])->name('gallery.lightbox');
    Route::get('shared/{token}', [ImageController::class, 'shared'])->name('image.shared');
});

Route::get('/', function () {
    return auth()->check() ? redirect()->route('webcam.index') : redirect()->route('login');
});
