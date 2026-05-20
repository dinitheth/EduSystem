<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($request->username !== 'Admin' || $request->password !== 'Admin123') {
            return back()->withInput($request->only('username'))->with('error', 'Invalid username or password.');
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->regenerate();
        session()->forget([
            'student_id', 'student_name', 'student_class',
            'teacher_id', 'teacher_name', 'teacher_class',
        ]);
        session([
            'admin_authenticated' => true,
            'admin_name' => 'Admin',
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget(['admin_authenticated', 'admin_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
