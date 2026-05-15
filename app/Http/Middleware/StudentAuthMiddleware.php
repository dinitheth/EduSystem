<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class StudentAuthMiddleware {
    public function handle(Request $request, Closure $next) {
        if (!session('student_id')) {
            return redirect()->route('student.login')->with('error', 'Please login to access your portal.');
        }
        return $next($request);
    }
}
