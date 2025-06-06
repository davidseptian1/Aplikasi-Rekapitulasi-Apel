<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // 1. Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->filled('remember');

        // 2. Ambil data user
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'Username tidak terdaftar.',
            ])->onlyInput('username');
        }

        // 3. Cek apakah ada sesi lain yang tercatat
        if ($user->last_session_id) {
            // Ambil data sesi dari database
            $lastSession = DB::table('sessions')->find($user->last_session_id);

            // Cek apakah sesi tersebut masih ada DAN masih aktif (belum kedaluwarsa)
            // 'session.lifetime' diambil dari config Anda (dalam menit), dikali 60 jadi detik
            if ($lastSession && (time() - $lastSession->last_activity) < (config('session.lifetime') * 60)) {
                return back()->withErrors([
                    'username' => 'Akun ini sedang digunakan di perangkat lain. Harap logout terlebih dahulu atau tunggu sesi berakhir.',
                ])->onlyInput('username');
            }
            // Jika sesi sudah tidak ada atau sudah kedaluwarsa, kita bisa lanjutkan login
        }

        // 4. Proses Autentikasi
        if (Auth::attempt($credentials, $remember)) {
            // Cek status aktif user
            if (Auth::user()->is_active != '1') {
                Auth::logout(); // Logout paksa jika tidak aktif
                return back()->withErrors([
                    'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
                ])->onlyInput('username');
            }

            // 5. Regenerate session ID untuk keamanan
            $request->session()->regenerate();

            // 6. Simpan session ID BARU ke database SETELAH regenerate
            $user->last_session_id = Session::getId();
            $user->save();

            return redirect()->intended(route('dashboard.index'));
        }

        // 7. Autentikasi gagal (password salah)
        return back()->withErrors([
            'username' => 'Kombinasi username dan password salah.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Kosongkan last_session_id saat logout
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
