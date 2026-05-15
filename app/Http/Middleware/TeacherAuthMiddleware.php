<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class TeacherAuthMiddleware {
    public function handle(Request $request, Closure $next) {
        if (!session('teacher_id')) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access your portal.');
        }
        return $next($request);
    }
}
