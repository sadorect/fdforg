<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->canAccessAdminPanel()) {
            return $next($request);
        }

        // Redirect to login if not admin
        return redirect()->route('admin.login')->with('error', 'Access denied. Admin privileges required.');
    }
}
