<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Middleware\CheckPermission;

// Bilingual Language Switcher Route
Route::get('/lang/switch/{lang}', function ($lang) {
    if (in_array($lang, ['ar', 'en'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return redirect()->back();
})->name('lang.switch');

// SEO Routes
Route::get('/robots.txt', [ApiController::class, 'robotsTxt']);
Route::get('/sitemap.xml', [ApiController::class, 'sitemapXml']);

// Landing Page & Public Booking Wizard
Route::get('/', [HomeController::class, 'index'])->name('home');

// API Public Endpoints
Route::get('/api/services', [ApiController::class, 'getServices']);
Route::get('/api/availabilities', [ApiController::class, 'getAvailabilities']);
Route::get('/api/slots', [ApiController::class, 'getSlots']);
Route::get('/api/reels', [ApiController::class, 'getReels']);
Route::get('/api/testimonials', [ApiController::class, 'getTestimonials']);
Route::get('/api/reviews', [ApiController::class, 'getTestimonials']);
Route::post('/api/testimonials', [ApiController::class, 'storeTestimonial']);
Route::post('/api/reviews', [ApiController::class, 'storeTestimonial']);

// Guest & Patient Booking Flow
Route::post('/api/bookings/checkout', [BookingController::class, 'createCheckoutSession']);
Route::post('/api/bookings/stripe/webhook', [BookingController::class, 'stripeWebhook']);
Route::get('/booking/success', [BookingController::class, 'bookingSuccess'])->name('booking.success');
// Patient payment confirmation (public — works for guests too)
Route::post('/booking/{bookingRef}/confirm-payment', [BookingController::class, 'confirmPayment'])->name('booking.confirm-payment');
Route::get('/booking/{bookingRef}/view-dashboard', [BookingController::class, 'goToPatientDashboard'])->name('booking.view-dashboard');

// Checkout routes alias (supports requests sent without /api prefix)
Route::prefix('checkout')->group(function () {
    Route::match(['get', 'post'], '/check-user', [ApiController::class, 'checkUser']);
    Route::post('/initialize', [ApiController::class, 'initializeCheckout']);
    Route::post('/confirm', [ApiController::class, 'confirmCheckout']);
});

// Authentication Routes (Web Session Protected with Rate Limiting)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Patient Portal Routes
Route::middleware(['auth'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'patientDashboard'])->name('dashboard');
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancelBooking'])->name('bookings.cancel');
});

// Admin & Staff Control Panel Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Bookings (manage_bookings)
    Route::middleware([CheckPermission::class . ':manage_bookings'])->group(function () {
        Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
        Route::post('/bookings', [AdminDashboardController::class, 'storeBooking'])->name('bookings.store');
        Route::post('/bookings/{id}/status', [AdminDashboardController::class, 'updateBookingStatus'])->name('bookings.status');
        Route::post('/bookings/{id}/reschedule', [AdminDashboardController::class, 'rescheduleBooking'])->name('bookings.reschedule');
        Route::get('/calendar', [AdminDashboardController::class, 'calendar'])->name('calendar');
        Route::get('/api/calendar-events', [AdminDashboardController::class, 'getCalendarEvents'])->name('api.calendar-events');
    });

    // Patients (manage_patients)
    Route::middleware([CheckPermission::class . ':manage_patients'])->group(function () {
        Route::get('/patients', [AdminDashboardController::class, 'patients'])->name('patients');
        Route::post('/patients', [AdminDashboardController::class, 'storePatient'])->name('patients.store');
        Route::get('/patients/{id}', [AdminDashboardController::class, 'patientDetails'])->name('patients.details');
        Route::get('/patient/profile/{id}', [AdminDashboardController::class, 'patientDetails'])->name('patient.profile');
    });

    // Payments & Reports (manage_payments)
    Route::middleware([CheckPermission::class . ':manage_payments'])->group(function () {
        Route::get('/payments', [AdminDashboardController::class, 'payments'])->name('payments');
        Route::get('/payments/export', [AdminDashboardController::class, 'exportPaymentsReport'])->name('payments.export');
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [AdminDashboardController::class, 'exportReportsCsv'])->name('reports.export');
        Route::get('/bookings/export', [AdminDashboardController::class, 'exportBookingsReport'])->name('bookings.export');
    });

    // Services (manage_services)
    Route::middleware([CheckPermission::class . ':manage_services'])->group(function () {
        Route::get('/services', [AdminDashboardController::class, 'services'])->name('services');
        Route::post('/services', [AdminDashboardController::class, 'storeService'])->name('services.store');
        Route::post('/services/{id}/update', [AdminDashboardController::class, 'updateService'])->name('services.update');
        Route::post('/services/{id}/delete', [AdminDashboardController::class, 'deleteService'])->name('services.delete');
        Route::delete('/services/{id}', [AdminDashboardController::class, 'deleteService'])->name('services.destroy');
    });

    // Availability & Work Hours (manage_availability)
    Route::middleware([CheckPermission::class . ':manage_availability'])->group(function () {
        Route::get('/availability', [AdminDashboardController::class, 'availability'])->name('availability');
        Route::post('/availability', [AdminDashboardController::class, 'updateAvailability'])->name('availability.update');
        Route::post('/availability/block', [AdminDashboardController::class, 'storeBlockedTime'])->name('availability.block.store');
        Route::post('/availability/block/{id}/delete', [AdminDashboardController::class, 'deleteBlockedTime'])->name('availability.block.delete');
    });

    // Doctor Portfolio & Content Editor (manage_portfolio)
    Route::middleware([CheckPermission::class . ':manage_portfolio'])->group(function () {
        Route::get('/portfolio', [AdminDashboardController::class, 'portfolio'])->name('portfolio');
        Route::post('/portfolio', [AdminDashboardController::class, 'updatePortfolio'])->name('portfolio.update');
        Route::post('/portfolio/image/delete', [AdminDashboardController::class, 'deleteGalleryImage'])->name('portfolio.image.delete');
        Route::post('/portfolio/reel', [AdminDashboardController::class, 'storeReel'])->name('portfolio.reel.store');
        Route::match(['post', 'put'], '/portfolio/reel/{id}', [AdminDashboardController::class, 'updateReel'])->name('portfolio.reel.update');
        Route::delete('/portfolio/reel/{id}', [AdminDashboardController::class, 'deleteReel'])->name('portfolio.reel.delete');
        Route::post('/portfolio/testimonial', [AdminDashboardController::class, 'storeTestimonial'])->name('portfolio.testimonial.store');
        Route::match(['post', 'put'], '/portfolio/testimonial/{id}', [AdminDashboardController::class, 'updateTestimonial'])->name('portfolio.testimonial.update');
        Route::delete('/portfolio/testimonial/{id}', [AdminDashboardController::class, 'deleteTestimonial'])->name('portfolio.testimonial.delete');
    });

    // Platform Settings (manage_settings)
    Route::middleware([CheckPermission::class . ':manage_settings'])->group(function () {
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/test-email', [AdminDashboardController::class, 'sendTestEmail'])->name('settings.test-email');
        Route::get('/api-control', [AdminDashboardController::class, 'apiControl'])->name('api-control');
        Route::post('/api-control', [AdminDashboardController::class, 'updateApiControl'])->name('api-control.update');
        Route::post('/api-control/token/{id}/delete', [AdminDashboardController::class, 'revokeToken'])->name('api-control.token.revoke');
    });

    // Staff Management & RBAC (manage_staff)
    Route::middleware([CheckPermission::class . ':manage_staff'])->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });
});
