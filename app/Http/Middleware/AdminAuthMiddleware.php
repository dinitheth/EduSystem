<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login')->with('error', 'Please login to access the admin dashboard.');
        }

        return $next($request);
    }
}
