<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TeacherLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherAuthController extends Controller
{
    public function showLogin() {
        if (session('teacher_id')) return redirect()->route('teacher.dashboard');
        return view('portal.teacher.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $login = TeacherLogin::where('email', $request->email)->first();

        if (!$login || !Hash::check($request->password, $login->password)) {
            return back()->withInput()->with('error', 'Invalid email or password.');
        }

        $teacher = $login->teacher()->with('subjects')->first();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->regenerate();
        session()->forget(['student_id','student_name','student_class']);
        session([
            'teacher_id'    => $teacher->id,
            'teacher_name'  => $teacher->full_name,
            'teacher_class' => $teacher->class,
        ]);

        return redirect()->route('teacher.dashboard');
    }

    public function logout(Request $request) {
        session()->forget(['teacher_id','teacher_name','teacher_class']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('teacher.login')->with('success', 'Logged out successfully.');
    }
}
