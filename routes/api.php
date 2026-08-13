<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Public Routes for Flutter Mobile Clients
Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);
Route::get('/doctor/profile', [ApiController::class, 'getDoctorProfile']);
Route::get('/services', [ApiController::class, 'getServices']);
Route::get('/slots', [ApiController::class, 'getSlots']);

// 2. Protected Routes (Token Auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Booking actions
    Route::post('/booking/create', [ApiController::class, 'createBooking']);
    Route::get('/patient/bookings', [ApiController::class, 'getPatientBookings']);
    Route::post('/booking/{id}/cancel', [ApiController::class, 'cancelBooking']);
});
