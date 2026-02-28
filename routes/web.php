<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSRFController;
use App\Http\Controllers\SSQLIController;

//Rutas de pruebas de vulnerabilidad SSRF
Route::get('/ssrf', [SSRFController::class, 'show']);
Route::post('/ssrf/fetch', [SSRFController::class, 'fetchVulnerable']);
Route::post('/ssrf/fetch-secure', [SSRFController::class, 'fetchSecure']);
Route::get('/internal/metadata', [SSRFController::class, 'internalMetadata']);

//Rutas para pruebas de SQLInjection 
Route::get('/sqli', [SSQLIController::class, 'show']);
Route::post('/sqli/vulnerable', [SSQLIController::class, 'loginVulnerable']);
Route::post('/sqli/secure', [SSQLIController::class, 'loginSecure']);