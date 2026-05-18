<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\StudentLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    public function showLogin() {
        if (session('student_id')) return redirect()->route('student.dashboard');
        return view('portal.student.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $login = StudentLogin::where('email', $request->email)->first();

        if (!$login || !Hash::check($request->password, $login->password)) {
            return back()->withInput()->with('error', 'Invalid email or password.');
        }

        $student = $login->student()->with('subjects')->first();
        session()->forget(['teacher_id','teacher_name','teacher_class']);
        session([
            'student_id'   => $student->id,
            'student_name' => $student->full_name,
            'student_class'=> $student->class,
        ]);

        return redirect()->route('student.dashboard');
    }

    public function logout() {
        session()->forget(['student_id','student_name','student_class']);
        return redirect()->route('student.login')->with('success', 'Logged out successfully.');
    }
}
