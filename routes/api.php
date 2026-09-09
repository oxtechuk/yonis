<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile Client (Flutter / React Native / Web Clients)
|--------------------------------------------------------------------------
*/

// 1. Public Info Routes (Rate limited to 60 req/min)
Route::middleware('throttle:60,1')->group(function () {
    // Master Home API (Single-request complete feed)
    Route::get('/home', [ApiController::class, 'getHome']);

    // Config & System Status
    Route::get('/config', [ApiController::class, 'getApiConfig']);
    Route::get('/payment-methods', [ApiController::class, 'getPaymentMethods']);

    // Doctor Profile
    Route::get('/doctor/profile', [ApiController::class, 'getDoctorProfile']);

    // Services (Unified with ?type=clinic or ?type=online filter)
    Route::get('/services', [ApiController::class, 'getServices']);
    Route::get('/services/clinic', [ApiController::class, 'getClinicServices']); // Alias
    Route::get('/services/online', [ApiController::class, 'getOnlineServices']); // Alias

    // Appointment Slots
    Route::get('/slots', [ApiController::class, 'getSlots']);
    Route::get('/available-slots', [ApiController::class, 'getSlots']); // Alias
    Route::get('/slots/available', [ApiController::class, 'getSlots']); // Alias

    // Media & Social Proof
    Route::get('/reels', [ApiController::class, 'getReels']);
    Route::get('/reviews', [ApiController::class, 'getTestimonials']);
    Route::get('/testimonials', [ApiController::class, 'getTestimonials']); // Alias
    Route::post('/reviews', [ApiController::class, 'storeTestimonial']);
    Route::post('/testimonials', [ApiController::class, 'storeTestimonial']); // Alias
});

// 2. Sensitive Public Auth & Checkout Routes (Strict Rate limited to 15 req/min)
Route::middleware('throttle:15,1')->group(function () {
    Route::post('/login', [ApiController::class, 'login']);
    Route::post('/register', [ApiController::class, 'register']);
    
    // Booking Checkout Flow
    Route::match(['get', 'post'], '/checkout/check-user', [ApiController::class, 'checkUser']);
    Route::post('/checkout/initialize', [ApiController::class, 'initializeCheckout']);
    Route::post('/checkout/confirm', [ApiController::class, 'confirmCheckout']);

    // Direct Booking Modal API Endpoints
    Route::post('/booking/request', [BookingController::class, 'store']);
    Route::match(['get', 'post'], '/booking/check-user', [ApiController::class, 'checkUser']);
    Route::get('/booking/available-slots', [ApiController::class, 'getSlots']);

    // Local & Manual Payment Confirmation Endpoints (ZainCash, SuperKi, Cash, etc.)
    Route::post('/checkout/confirm-local', [ApiController::class, 'confirmLocalPayment']);
    Route::post('/payment/confirm-local', [ApiController::class, 'confirmLocalPayment']);
    Route::post('/payment/confirm', [ApiController::class, 'confirmLocalPayment']);
    Route::post('/booking/confirm-payment', [ApiController::class, 'confirmLocalPayment']);
    Route::post('/booking/{bookingRef}/confirm-payment', [ApiController::class, 'confirmLocalPayment']);

    // SpaceRemit Webhook notification endpoint (IPN callback)
    Route::post('/payment/spaceremit/webhook', [BookingController::class, 'spaceremitWebhook']);
});

// 3. Protected Routes (Bearer Token Auth via Sanctum with Rate Limiting)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => [
                'id'    => $request->user()->id,
                'name'  => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'role'  => $request->user()->role,
            ]
        ]);
    });

    // Patient Booking actions (Protected with IDOR validation in controller)
    Route::get('/patient/bookings', [ApiController::class, 'getPatientBookings']);
    Route::post('/booking/{id}/cancel', [ApiController::class, 'cancelBooking']);
    Route::post('/booking/{id}/reschedule', [ApiController::class, 'rescheduleBooking']);
});
