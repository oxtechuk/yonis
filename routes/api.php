<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile Client (Flutter & External Consumers)
|--------------------------------------------------------------------------
*/

// 1. Public Info Routes (Rate limited to 60 req/min)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/config', [ApiController::class, 'getApiConfig']);
    Route::get('/doctor/profile', [ApiController::class, 'getDoctorProfile']);
    Route::get('/services', [ApiController::class, 'getServices']);
    Route::get('/slots', [ApiController::class, 'getSlots']);
    Route::get('/available-slots', [ApiController::class, 'getAvailableSlots']);
    Route::get('/slots/available', [ApiController::class, 'getAvailableSlots']);
    Route::get('/reels', [ApiController::class, 'getReels']);
});

// 2. Sensitive Public Auth & Checkout Routes (Strict Rate limited to 15 req/min)
Route::middleware('throttle:15,1')->group(function () {
    Route::post('/login', [ApiController::class, 'login']);
    Route::post('/register', [ApiController::class, 'register']);
    
    // Booking Checkout Flow
    Route::match(['get', 'post'], '/checkout/check-user', [ApiController::class, 'checkUser']);
    Route::post('/checkout/initialize', [ApiController::class, 'initializeCheckout']);
    Route::post('/checkout/confirm', [ApiController::class, 'confirmCheckout']);
});

// 3. Protected Routes (Bearer Token Auth via Sanctum with Rate Limiting)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'role' => $request->user()->role,
            ]
        ]);
    });

    // Patient Booking actions (Protected with IDOR validation in controller)
    Route::get('/patient/bookings', [ApiController::class, 'getPatientBookings']);
    Route::post('/booking/{id}/cancel', [ApiController::class, 'cancelBooking']);
    Route::post('/booking/{id}/reschedule', [ApiController::class, 'rescheduleBooking']);
});
