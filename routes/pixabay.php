<?php

use Illuminate\Support\Facades\Route;
use hexa_package_pixabay\Http\Controllers\PixabayController;

/*
|--------------------------------------------------------------------------
| Pixabay Package Routes
|--------------------------------------------------------------------------
| All routes behind core's auth + middleware stack.
| The service provider handles registration.
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'locked', 'system_lock', 'two_factor', 'role'])->group(function () {

    // ── Raw dev page ──
    Route::get('/raw-pixabay', [PixabayController::class, 'raw'])->name('pixabay.index');

    // ── AJAX endpoints ──
    Route::post('/pixabay/search', [PixabayController::class, 'search'])->name('pixabay.search');

});
