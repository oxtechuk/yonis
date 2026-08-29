<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('patient.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request with rate limiting and brute force protection
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('login')) . '|' . $request->ip());

        // Check if user is locked out
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::warning("Too many failed login attempts for: {$request->input('login')} from IP: {$request->ip()}");
            
            throw ValidationException::withMessages([
                'login' => "تم تجاوز الحد الأقصى للمحاولات الخاطئة. يرجى الانتظار {$seconds} ثانية قبل المحاولة مجدداً.",
            ]);
        }

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');

        // Allow logging in via email or phone
        $user = User::where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            RateLimiter::clear($throttleKey);
            
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            Log::info("User logged in successfully: ID {$user->id} (Role: {$user->role}) from IP: {$request->ip()}");

            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('patient.dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        Log::warning("Failed login attempt for: {$loginInput} from IP: {$request->ip()}");

        throw ValidationException::withMessages([
            'login' => 'بيانات الدخول غير صحيحة. يرجى التأكد من البريد/الهاتف وكلمة المرور.',
        ]);
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('patient.dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle registration request with strict input sanitization
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => trim(strip_tags($request->name)),
            'email' => Str::lower(trim($request->email)),
            'phone' => trim($request->phone),
            'password' => Hash::make($request->password),
            'role' => 'patient',
        ]);

        Log::info("New patient registered: ID {$user->id} from IP: {$request->ip()}");

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('patient.dashboard')->with('success', 'تم إنشاء حسابك وملفك الطبي بنجاح!');
    }

    /**
     * Logout user with session revocation
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info("User logged out: ID {$userId} from IP: {$request->ip()}");

        return redirect()->route('home')->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}
