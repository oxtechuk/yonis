<?php

// Polyfill for environments where PHP ext-fileinfo is disabled
if (!class_exists('finfo', false)) {
    if (!defined('FILEINFO_MIME_TYPE')) {
        define('FILEINFO_MIME_TYPE', 16);
    }
    class finfo {
        public function __construct(int $flags = FILEINFO_MIME_TYPE, ?string $magicFile = null) {}
        public function file(?string $filename = null, int $flags = FILEINFO_MIME_TYPE, $context = null): string|false {
            return false;
        }
        public function buffer(?string $string = null, int $flags = FILEINFO_MIME_TYPE, $context = null): string|false {
            return false;
        }
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'api/bookings/stripe/webhook',
            'api/payment/spaceremit/webhook',
            'api/*',
            'checkout/*',
            'booking/*/confirm-payment',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
