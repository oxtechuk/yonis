<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً.');
        }

        $user = Auth::user();

        // Strictly allow only Admin or authorized Staff members
        if ($user->isAdmin() || $user->isStaff()) {
            return $next($request);
        }

        // If the user is a patient or any other role, strictly deny access to admin dashboard
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'غير مصرح لك بالوصول. منطقة خاصة بإدارة العيادة فقط.'
            ], 403);
        }

        return redirect()->route('patient.dashboard')->with('error', 'عذراً، هذه المنطقة مخصصة لإدارة العيادة فقط ولا يمكن للمرضى الدخول إليها.');
    }
}
