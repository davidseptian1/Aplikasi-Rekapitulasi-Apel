<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Display login form
     */
    public function index()
    {
        // Jika user sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        $data = [
            'title' => 'Authentication Login',
            'pages' => 'Authentication Login',
        ];

        return view('auth.login', $data);
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah username terdaftar
        $user = User::where('username', $credentials['username'])->first();
        if (!$user) {
            return back()->withErrors([
                'username' => 'Username tidak terdaftar',
            ])->onlyInput('username');
        }

        // Cek apakah password valid
        if (!Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            return back()->withErrors([
                'password' => 'Password salah',
            ])->onlyInput('username');
        }

        // Cek apakah akun aktif
        if ($user->is_active == '1') {
            $request->session()->regenerate();

            // Redirect ke halaman dashboard.index
            return redirect()->route('dashboard.index');
        }

        // Logout jika akun tidak aktif
        Auth::logout();
        return back()->withErrors([
            'username' => 'Akun tidak aktif',
        ])->onlyInput('username');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
