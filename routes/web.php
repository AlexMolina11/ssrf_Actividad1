<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSRFController;

Route::get('/ssrf', [SSRFController::class, 'show']);
Route::post('/ssrf/fetch', [SSRFController::class, 'fetchVulnerable']);
Route::post('/ssrf/fetch-secure', [SSRFController::class, 'fetchSecure']);
Route::get('/internal/metadata', [SSRFController::class, 'internalMetadata']);