<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Zap\ZapController;
use App\Http\Controllers\Api\VivaReal\VivaRealController;

Route::get('/zap-properties', ZapController::class);
Route::get('/viva-real-properties', VivaRealController::class);