<?php

namespace App\Http\Middleware;

use App\Models\User; // Import User model jika ingin mengambil role dari sana
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Mengganti auth() helper dengan Facade
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // Ambil daftar role yang valid dari User model atau config untuk konsistensi
    // protected $validRoles = ['superadmin', 'pokmin', 'piket', 'anggota']; // Versi lama Anda

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) { // Menggunakan Auth Facade
            // Jika request mengharapkan JSON (misalnya API), kembalikan response JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Ambil daftar role yang benar-benar terdefinisi dalam sistem Anda
        // Sebaiknya ini konsisten dengan enum di migrasi 'users' dan User model.
        // Contoh mengambil dari User Model (jika Anda membuat method static getDefinedRoles())
        $systemDefinedRoles = User::getDefinedRoles();
        // Untuk saat ini, kita akan sesuaikan dengan migrasi Anda:
        // $systemDefinedRoles = ['superadmin', 'pokmin', 'piket', 'pimpinan', 'personil'];


        // Jika tidak ada role spesifik yang dibutuhkan oleh route, lanjutkan
        if (empty($roles)) {
            return $next($request);
        }

        // Validasi apakah role yang diminta oleh route terdaftar dalam sistem
        foreach ($roles as $role) {
            if (!in_array($role, $systemDefinedRoles)) {
                // Role yang diminta oleh middleware tidak valid/tidak dikenal sistem
                // Ini biasanya kesalahan konfigurasi di sisi developer (route middleware)
                // Sebaiknya log error ini
                report("Invalid role '{$role}' requested in route middleware. Check route definitions.");
                abort(500, "Kesalahan konfigurasi: Role '{$role}' tidak terdaftar dalam sistem.");
            }
        }

        // Cek apakah user memiliki salah satu dari roles yang diizinkan untuk route ini
        $userRole = Auth::user()->role; // Ambil role user yang sedang login
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki akses
        // Jika request mengharapkan JSON, kembalikan response JSON
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden. You do not have the required role.'], 403);
        }
        abort(403, 'AKSES DITOLAK. Anda tidak memiliki hak akses untuk halaman ini.'); // Pesan lebih jelas
    }
}
