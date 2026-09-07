<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebcamController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfileController;


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::post('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');


    /*
    |--------------------------------------------------------------------------
    | Webcam
    |--------------------------------------------------------------------------
    */

    // Webcam dashboard / gallery
    Route::get('/webcam', [WebcamController::class, 'index'])
        ->name('webcam.index');

    // Capture and save image
    Route::post('/webcam', [WebcamController::class, 'store'])
        ->name('webcam.capture');

    // Download single image
    Route::get('/webcam/download/{filename}', [WebcamController::class, 'download'])
        ->name('webcam.download');

    // Delete single image
    Route::delete('/webcam/{filename}', [WebcamController::class, 'destroy'])
        ->name('webcam.destroy');

    // Delete selected images
    Route::delete('/webcam-bulk-delete', [WebcamController::class, 'bulkDestroy'])
        ->name('webcam.bulkDestroy');

    // Delete all images
    Route::delete('/webcam-delete-all', [WebcamController::class, 'destroyAll'])
        ->name('webcam.destroyAll');


    /*
    |--------------------------------------------------------------------------
    | Image Gallery
    |--------------------------------------------------------------------------
    */

    // Gallery
    Route::get('/gallery', [ImageController::class, 'index'])
        ->name('gallery.index');

    // Trash
    Route::get('/gallery/trash', [ImageController::class, 'trash'])
        ->name('gallery.trash');

    // Restore
    Route::post('/gallery/restore/{id}', [ImageController::class, 'restore'])
        ->name('gallery.restore');

    // Permanently delete
    Route::delete('/gallery/force-delete/{id}', [ImageController::class, 'forceDelete'])
        ->name('gallery.forceDelete');

    // Bulk delete
    Route::post('/gallery/bulk-delete', [ImageController::class, 'bulkDelete'])
        ->name('gallery.bulkDelete');

    // Bulk restore
    Route::post('/gallery/bulk-restore', [ImageController::class, 'bulkRestore'])
        ->name('gallery.bulkRestore');

    // Bulk force delete
    Route::post('/gallery/bulk-force-delete', [ImageController::class, 'bulkForceDelete'])
        ->name('gallery.bulkForceDelete');

    // Update tags
    Route::post('/gallery/tags/{id}', [ImageController::class, 'updateTags'])
        ->name('gallery.tags');

    // Update caption
    Route::post('/gallery/caption/{id}', [ImageController::class, 'updateCaption'])
        ->name('gallery.caption');

    // Rename image
    Route::post('/gallery/rename/{id}', [ImageController::class, 'rename'])
        ->name('gallery.rename');

    // Share image
    Route::post('/gallery/share/{id}', [ImageController::class, 'share'])
        ->name('gallery.share');

    // Download gallery image
    Route::get('/gallery/download/{id}', [ImageController::class, 'download'])
        ->name('gallery.download');

    // Export image
    Route::get('/gallery/export/{id}/{format?}', [ImageController::class, 'export'])
        ->name('gallery.export');

    // Print image
    Route::get('/gallery/print/{id}', [ImageController::class, 'print'])
        ->name('gallery.print');

    // Lightbox
    Route::get('/gallery/lightbox/{id}', [ImageController::class, 'lightbox'])
        ->name('gallery.lightbox');

    // Shared image
    Route::get('/shared/{token}', [ImageController::class, 'shared'])
        ->name('image.shared');
});


/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (auth()->check()) {
        return redirect()->route('webcam.index');
    }

    return redirect()->route('login');
});