<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth/login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Arahkan pengguna sesuai role (akses tetap dibatasi oleh middleware CheckRole di route)
            return match ($user->role) {
                'administrator' => redirect()->route('admin.dashboard'),
                'operator' => redirect()->route('operator.dashboard'),
                'finance' => redirect()->route('finance.dashboard'),
                'manager' => redirect()->route('user.manager'),
                default => abort(403, 'Unauthorized role'),
            };
        }

        return redirect()->back()->with('error', 'Email dan password tidak cocok');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
