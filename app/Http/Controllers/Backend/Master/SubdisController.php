<?php

namespace App\Http\Controllers\Backend\Master;

use App\Models\User;
use App\Models\Subdis; // Pastikan model Subdis di-import
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session; // Pastikan Session di-import

class SubdisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager loading relasi 'user' untuk menghindari N+1 problem saat menampilkan nama user di view.
        // Mengurutkan berdasarkan nama subdis.
        $subdis = Subdis::with('user')->orderBy('name', 'asc')->get();

        $data = [
            'title' => 'Subdirektorat',
            'pages' => 'Subdirektorat',
            'subdis' => $subdis,
        ];

        return view('backend.master.subdis.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengambil semua user. Jika jumlah user sangat banyak, pertimbangkan untuk memfilter
        // user yang relevan saja (misal, berdasarkan role tertentu yang bisa jadi penanggung jawab)
        // atau gunakan select2 dengan ajax loading jika daftarnya panjang.
        // Untuk sekarang, User::all() diasumsikan masih manageable.
        // Filter hanya user yang aktif dan mungkin berdasarkan role tertentu (misal, 'pokmin' atau 'pimpinan')
        $users = User::where('is_active', '1') // Ambil hanya user yang aktif
            ->whereIn('role', ['pokmin']) // Contoh filter berdasarkan role
            ->orderBy('name', 'asc')
            ->select('id', 'name') // Hanya ambil kolom yang dibutuhkan untuk dropdown
            ->get();

        $data = [
            'title' => 'Tambah Subdirektorat',
            'pages' => 'Subdirektorat',
            'users' => $users,
        ];

        return view('backend.master.subdis.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Pertimbangkan Form Request untuk validasi yang lebih kompleks.
        $request->validate([
            'name' => 'required|string|max:155|unique:subdis,name',
            'user_id' => 'nullable|exists:users,id', // user_id boleh kosong (nullable)
        ]);

        Subdis::create($request->all());

        Session::flash('success', 'Subdirektorat berhasil ditambahkan!');
        return redirect()->route('subdis.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return abort(404); // Biasanya tidak digunakan untuk master data seperti ini
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Eager load relasi 'user' jika akan ditampilkan di form edit (meskipun di sini user dipilih ulang)
        $subdi = Subdis::findOrFail($id);
        // Logika pengambilan user sama seperti di create()
        $users = User::where('is_active', '1')
            ->whereIn('role', ['pokmin'])
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        $data = [
            'title' => 'Edit Subdirektorat',
            'pages' => 'Subdirektorat',
            'subdi' => $subdi, // Variabel di view Anda adalah 'subdi', bukan 'subdis'
            'users' => $users,
        ];

        return view('backend.master.subdis.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subdi = Subdis::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:155|unique:subdis,name,' . $subdi->id,
            'user_id' => 'nullable|exists:users,id',
        ]);

        $subdi->update($request->all());

        Session::flash('success', 'Subdirektorat berhasil diperbarui!');
        return redirect()->route('subdis.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subdi = Subdis::findOrFail($id);

        // PENTING: Tambahkan pengecekan relasi sebelum menghapus.
        // Jika subdis ini masih memiliki user personil (relasi User ke Subdis)
        // atau sesi apel (relasi ApelSession ke Subdis), penghapusan bisa gagal
        // atau menyebabkan data yatim piatu jika tidak ada onDelete('cascade') yang sesuai.
        if ($subdi->users()->exists() || $subdi->apelSessions()->exists()) {
            // Asumsi ada relasi users() dan apelSessions() di model Subdis
            Session::flash('error', 'Subdirektorat tidak dapat dihapus karena masih memiliki data personil atau sesi apel terkait.');
            return redirect()->route('subdis.index');
        }
        // Juga, jika user_id (penanggung jawab) di subdis ini menjadi foreign key di tabel lain
        // yang onDelete('restrict'), itu juga perlu diperhatikan.

        $subdi->delete();

        Session::flash('success', 'Subdirektorat berhasil dihapus!');
        return redirect()->route('subdis.index');
    }
}
