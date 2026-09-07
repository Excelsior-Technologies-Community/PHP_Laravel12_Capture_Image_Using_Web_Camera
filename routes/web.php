<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebcamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| These routes handle webcam capture, image download and deletion.
*/

Route::get('webcam', [WebcamController::class, 'index'])
    ->name('webcam.index');

Route::post('webcam', [WebcamController::class, 'store'])
    ->name('webcam.capture');

Route::get('webcam/download/{filename}', [WebcamController::class, 'download'])
    ->name('webcam.download');

Route::delete('webcam/{filename}', [WebcamController::class, 'destroy'])
    ->name('webcam.destroy');