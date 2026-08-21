<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StripeWebhookController;

// 1. Public / Landing Page Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); // fallback for ease of access

// 3. Dynamic Slots & Booking APIs
Route::get('/api/slots', [BookingController::class, 'getSlots'])->name('api.slots');
Route::post('/api/booking/checkout', [BookingController::class, 'store'])->name('api.booking.checkout');

// 4. Stripe Webhook (excluding CSRF via bootstrap/app.php)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// 5. Patient Dashboard Routes (Protected by Auth)
Route::middleware('auth')->group(function () {
    Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
    Route::post('/patient/booking/{id}/cancel', [PatientDashboardController::class, 'cancel'])->name('patient.booking.cancel');
});

// 6. Admin / Doctor Dashboard Routes (Protected by Auth & Admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Redirect /admin to /admin/dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Bookings
    Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{id}/status', [AdminDashboardController::class, 'updateBookingStatus'])->name('bookings.status');
    Route::post('/bookings/{id}/reschedule', [AdminDashboardController::class, 'rescheduleBooking'])->name('bookings.reschedule');
    
    // Patients
    Route::get('/patients', [AdminDashboardController::class, 'patients'])->name('patients');
    Route::get('/patients/{id}', [AdminDashboardController::class, 'patientDetails'])->name('patients.details');
    
    // Payments
    Route::get('/payments', [AdminDashboardController::class, 'payments'])->name('payments');
    
    // Services
    Route::get('/services', [AdminDashboardController::class, 'services'])->name('services');
    Route::post('/services', [AdminDashboardController::class, 'storeService'])->name('services.store');
    Route::post('/services/{id}/update', [AdminDashboardController::class, 'updateService'])->name('services.update');
    
    // Availability & Blocks
    Route::get('/availability', [AdminDashboardController::class, 'availability'])->name('availability');
    Route::post('/availability', [AdminDashboardController::class, 'updateAvailability'])->name('availability.update');
    Route::post('/availability/block', [AdminDashboardController::class, 'storeBlockedTime'])->name('availability.block.store');
    Route::post('/availability/block/{id}/delete', [AdminDashboardController::class, 'deleteBlockedTime'])->name('availability.block.delete');
    
    // Doctor Portfolio
    Route::get('/portfolio', [AdminDashboardController::class, 'portfolio'])->name('portfolio');
    Route::post('/portfolio', [AdminDashboardController::class, 'updatePortfolio'])->name('portfolio.update');
    Route::post('/portfolio/image/delete', [AdminDashboardController::class, 'deleteGalleryImage'])->name('portfolio.image.delete');

    // Calendar
    Route::get('/calendar', [AdminDashboardController::class, 'calendar'])->name('calendar');
    Route::get('/api/calendar-events', [AdminDashboardController::class, 'getCalendarEvents'])->name('api.calendar-events');

    // Settings
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');

    // API Control Panel
    Route::get('/api-control', [AdminDashboardController::class, 'apiControl'])->name('api-control');
    Route::post('/api-control', [AdminDashboardController::class, 'updateApiControl'])->name('api-control.update');
    Route::post('/api-control/token/{id}/delete', [AdminDashboardController::class, 'revokeToken'])->name('api-control.token.revoke');

    // Add Patient manually
    Route::post('/patients/store', [AdminDashboardController::class, 'storePatient'])->name('patients.store');

    // Add Booking manually
    Route::post('/bookings/store', [AdminDashboardController::class, 'storeBooking'])->name('bookings.store');
});

// ═══ SEO Routes ══════════════════════════════════════════════
Route::get('/sitemap.xml', function () {
    $services = App\Models\Service::where('is_active', true)->get();
    $content = view('seo.sitemap', compact('services'))->render();
    return response($content, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/robots.txt', function () {
    $content = view('seo.robots')->render();
    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots');

