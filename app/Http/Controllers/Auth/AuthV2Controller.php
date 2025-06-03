<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthV2Controller extends Controller
{
    /**
     * Display login form
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        $data = [
            'title' => 'Autentikasi Login',
        ];

        return view('auth.loginv2', $data);
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

        $remember = $request->filled('remember');
        $currentSessionId = Session::getId();

        // Ambil user terlebih dahulu
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'Username tidak terdaftar.',
            ])->onlyInput('username');
        }

        // Cek apakah user sedang login di sesi lain
        if ($user->last_session_id && $user->last_session_id !== $currentSessionId) {
            return back()->withErrors([
                'username' => 'Akun ini sedang login di perangkat lain.',
            ])->onlyInput('username');
        }

        // Proses autentikasi
        if (Auth::attempt($credentials, $remember)) {

            // Cek status aktif
            if ($user->is_active != '1') {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
                ])->onlyInput('username');
            }

            // Simpan session ID saat ini
            $user->last_session_id = $currentSessionId;
            $user->save();

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard.index'));
        }

        // Autentikasi gagal
        throw ValidationException::withMessages([
            'username' => __('auth.failed'),
        ])->redirectTo(route('login'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->last_session_id = null;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }
}
