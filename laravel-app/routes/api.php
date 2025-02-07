<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('events', EventController::class);
Route::apiResource('reservations', ReservationController::class);

Route::put('reservations/{reservation}/confirm', [ReservationController::class, 'confirmReservation']);
Route::put('reservations/{reservation}/cancel', [ReservationController::class, 'cancelReservation']);
