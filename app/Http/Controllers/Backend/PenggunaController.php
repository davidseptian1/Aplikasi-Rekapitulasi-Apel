<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Biodata;
use App\Models\Jabatan;
use App\Models\Pangkat;
use App\Models\Subdis; // Tambahkan Subdis model
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk Transaction
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Password; // Ini untuk Reset Password, bukan validasi langsung
use Illuminate\Validation\Rules\Password as PasswordRules; // Gunakan ini untuk validasi password
use Illuminate\Support\Facades\Session; // Tambahkan jika belum ada

class PenggunaController extends Controller
{
    // Daftar role yang valid, sebaiknya diambil dari satu sumber (misal: User model atau config)
    // Konsisten dengan migrasi: ['superadmin', 'pokmin', 'piket', 'pimpinan', 'personil']
    protected function getValidRoles(): array
    {
        // Idealnya: return User::getDefinedRoles();
        return User::getDefinedRoles();
        // return ['superadmin', 'pokmin', 'piket', 'pimpinan', 'personil'];
    }

    public function index(Request $request) // Tambahkan Request $request
    {
        $selectedRole = $request->input('role_filter'); // Ambil role yang difilter dari request

        $query = User::with(['biodata.pangkat', 'biodata.jabatan', 'subdis'])
            ->orderBy('name', 'asc');

        // Terapkan filter jika role dipilih
        if (!empty($selectedRole)) {
            $query->where('role', $selectedRole);
        }

        $users = $query->get();
        $availableRoles = $this->getValidRoles(); // Ambil daftar role untuk dropdown filter

        $data = [
            'title' => 'Pengguna',
            'pages' => 'Pengguna',
            'users' => $users,
            'availableRoles' => $availableRoles, // Kirim daftar role ke view
            'selectedRole' => $selectedRole,     // Kirim role yang sedang aktif difilter
        ];

        return view('backend.master.user.index', $data);
    }

    public function create()
    {
        // Ambil hanya kolom yang dibutuhkan untuk dropdown dan urutkan
        $pangkats = Pangkat::orderBy('name', 'asc')->select('id', 'name')->get();
        $jabatans = Jabatan::orderBy('name', 'asc')->select('id', 'name')->get();
        $subdis_list = Subdis::orderBy('name', 'asc')->select('id', 'name')->get(); // Daftar Subdis

        $data = [
            'title' => 'Tambah Pengguna',
            'pages' => 'Pengguna',
            'pangkats' => $pangkats,
            'jabatans' => $jabatans,
            'subdis_list' => $subdis_list, // Kirim daftar subdis ke view
            'roles' => $this->getValidRoles(), // Gunakan method untuk role
        ];
        return view('backend.master.user.create', $data);
    }

    public function store(Request $request)
    {
        // Pertimbangkan menggunakan Form Request (misal: StoreUserRequest)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'no_telpon' => 'nullable|string|max:30|regex:/^[0-9\-\+\(\)\s]*$/', // Validasi nomor telepon
            'nrp' => 'nullable|string|max:50|unique:users,nrp', // Validasi untuk nrp
            'username' => 'required|string|max:55|unique:users,username|alpha_dash', // Username alpha_dash
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()], // Aturan password lebih kuat
            'role' => 'required|in:' . implode(',', $this->getValidRoles()),
            'photos' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'pangkat_id' => 'required|exists:pangkats,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'subdis_id' => 'nullable|exists:subdis,id', // Validasi untuk subdis_id
            'is_active' => 'nullable|boolean', // Terima boolean atau null
        ]);

        DB::beginTransaction();
        try {
            $photoName = null;
            if ($request->hasFile('photos')) {
                $photo = $request->file('photos');
                // Menggunakan hashName() untuk nama file yang lebih aman dan unik
                $photoName = $photo->hashName(); // contoh: timestamp_randomstring.jpg
                $photo->storeAs('uploads/photos', $photoName, 'public');
            }

            $user = User::create([
                'name' => $validatedData['name'],
                'no_telpon' => $validatedData['no_telpon'] ?? null,
                'nrp' => $validatedData['nrp'] ?? null,
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => $validatedData['role'],
                'photos' => $photoName,
                'is_active' => $request->boolean('is_active') ? '1' : '0', // Konversi boolean ke '1' atau '0'
                'subdis_id' => $validatedData['subdis_id'] ?? null, // Simpan subdis_id
            ]);

            Biodata::create([
                'user_id' => $user->id,
                'pangkat_id' => $validatedData['pangkat_id'],
                'jabatan_id' => $validatedData['jabatan_id'],
            ]);

            DB::commit();
            Session::flash('success', 'Pengguna berhasil ditambahkan.');
            return redirect()->route('user.index');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error $e->getMessage()
            report($e); // Laporkan exception
            Session::flash('error', 'Gagal menambahkan pengguna. Silakan coba lagi.');
            return back()->withInput();
        }
    }

    public function edit(string $id)
    {
        // Eager load biodata dan subdis
        $user = User::with(['biodata', 'subdis'])->findOrFail($id);
        $pangkats = Pangkat::orderBy('name', 'asc')->select('id', 'name')->get();
        $jabatans = Jabatan::orderBy('name', 'asc')->select('id', 'name')->get();
        $subdis_list = Subdis::orderBy('name', 'asc')->select('id', 'name')->get();

        $data = [
            'title' => 'Edit Pengguna',
            'pages' => 'Pengguna',
            'user' => $user,
            'pangkats' => $pangkats,
            'jabatans' => $jabatans,
            'subdis_list' => $subdis_list,
            'roles' => $this->getValidRoles(),
        ];
        return view('backend.master.user.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Pertimbangkan menggunakan Form Request (misal: UpdateUserRequest)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'no_telpon' => 'nullable|string|max:30|regex:/^[0-9\-\+\(\)\s]*$/',
            'nrp' => 'nullable|string|max:50|unique:users,nrp,' . $id,
            'username' => 'required|string|max:55|unique:users,username,' . $id . '|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => ['nullable', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()], // Password opsional
            'role' => 'required|in:' . implode(',', $this->getValidRoles()),
            'photos' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pangkat_id' => 'required|exists:pangkats,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'subdis_id' => 'nullable|exists:subdis,id',
            'is_active' => 'nullable|boolean',
            'remove_photo' => 'nullable|boolean', // Untuk menghapus foto
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'name' => $validatedData['name'],
                'no_telpon' => $validatedData['no_telpon'] ?? null,
                'nrp' => $validatedData['nrp'] ?? null,
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'is_active' => $request->boolean('is_active') ? '1' : '0',
                'subdis_id' => $validatedData['subdis_id'] ?? null,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validatedData['password']);
            }

            $photoName = $user->photos;
            if ($request->boolean('remove_photo') && $photoName) {
                Storage::disk('public')->delete('uploads/photos/' . $photoName);
                $photoName = null;
            }

            if ($request->hasFile('photos')) {
                if ($photoName) { // Hapus foto lama jika ada dan tidak di-remove oleh checkbox
                    Storage::disk('public')->delete('uploads/photos/' . $photoName);
                }
                $photo = $request->file('photos');
                $photoName = $photo->hashName();
                $photo->storeAs('uploads/photos', $photoName, 'public');
            }
            $userData['photos'] = $photoName;

            $user->update($userData);

            $user->biodata()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'pangkat_id' => $validatedData['pangkat_id'],
                    'jabatan_id' => $validatedData['jabatan_id'],
                ]
            );

            DB::commit();
            Session::flash('success', 'Pengguna berhasil diperbarui.');
            return redirect()->route('user.index');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Session::flash('error', 'Gagal memperbarui pengguna. Silakan coba lagi.');
            return back()->withInput();
        }
    }

    public function destroy(string $id)
    {
        $user = User::with('biodata')->findOrFail($id); // Load biodata untuk menghapusnya juga jika perlu

        // PENTING: Cek relasi lain sebelum menghapus user (misal: apakah user ini penanggung jawab subdis,
        // apakah ada di tabel piket, apel_attendances, dll. tergantung onDelete constraint Anda)
        // Contoh:
        if ($user->createdApelSessions()->exists() || $user->piketSebagaiPajaga()->exists()) {
            Session::flash('error', 'Pengguna tidak dapat dihapus karena masih terkait dengan data lain.');
            return redirect()->route('user.index');
        }

        DB::beginTransaction();
        try {
            if ($user->photos) {
                Storage::disk('public')->delete('uploads/photos/' . $user->photos);
            }

            // Hapus biodata terkait (jika onDelete('cascade') tidak diset di migrasi biodatas.user_id)
            // Jika sudah onDelete cascade, ini tidak perlu.
            if ($user->biodata) {
                $user->biodata->delete();
            }
            // Atau $user->biodata()->delete();

            $user->delete(); // Ini akan memicu onDelete('cascade') atau onDelete('set null') pada tabel lain yang mereferensikan users.id

            DB::commit();
            Session::flash('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Session::flash('error', 'Gagal menghapus pengguna. Kemungkinan masih terkait dengan data lain.');
        }
        return redirect()->route('user.index');
    }
}
