<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile Client (Flutter)
|--------------------------------------------------------------------------
*/

// 1. Public Routes
Route::get('/config', [ApiController::class, 'getApiConfig']);
Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);
Route::get('/doctor/profile', [ApiController::class, 'getDoctorProfile']);
Route::get('/services', [ApiController::class, 'getServices']);
Route::get('/slots', [ApiController::class, 'getSlots']);

// Reels / Video Testimonials
Route::get('/reels', [ApiController::class, 'getReels']);

// Booking Checkout Flow (Guest registers & pays via Stripe, account created upon confirmation)
Route::post('/checkout/initialize', [ApiController::class, 'initializeCheckout']);
Route::post('/checkout/confirm', [ApiController::class, 'confirmCheckout']);

// 2. Protected Routes (Token Auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Patient Booking actions
    Route::get('/patient/bookings', [ApiController::class, 'getPatientBookings']);
    Route::post('/booking/{id}/cancel', [ApiController::class, 'cancelBooking']);
    Route::post('/booking/{id}/reschedule', [ApiController::class, 'rescheduleBooking']);
});

