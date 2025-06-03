<?php

namespace App\Http\Controllers\Backend\Master;

use App\Models\Jabatan; // Pastikan model di-import
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session; // Pastikan Session di-import

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua jabatan. Untuk tabel dengan DataTables client-side, ini umumnya oke.
        // Pertimbangkan pengurutan default untuk tampilan yang lebih konsisten.
        $jabatans = Jabatan::orderBy('name', 'asc')->get();

        $data = [
            'title' => 'Jabatan',
            'pages' => 'Jabatan', // Variabel ini digunakan di view Anda
            'jabatans' => $jabatans,
        ];

        return view('backend.master.jabatan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Jabatan',
            'pages' => 'Jabatan', // Konsisten dengan index
        ];

        return view('backend.master.jabatan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi sudah baik.
        // Untuk konsistensi dan skalabilitas, pertimbangkan Form Request (misal: StoreJabatanRequest)
        $request->validate([
            'name' => 'required|string|max:155|unique:jabatans,name',
        ]);

        // $request->all() aman digunakan di sini karena model Jabatan memiliki $fillable yang sudah ditentukan.
        Jabatan::create($request->all());

        Session::flash('success', 'Jabatan berhasil ditambahkan!');
        return redirect()->route('jabatan.index');
    }

    /**
     * Display the specified resource.
     * Method ini biasanya tidak terlalu sering digunakan untuk master data sederhana
     * yang detailnya sudah terlihat di tabel atau form edit.
     */
    public function show(string $id)
    {
        // Jika tidak digunakan, bisa dihapus atau dibiarkan kosong dengan abort.
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jabatan = Jabatan::findOrFail($id); // findOrFail sudah baik untuk menangani kasus ID tidak ditemukan.

        $data = [
            'title' => 'Edit Jabatan',
            'pages' => 'Jabatan', // Konsisten
            'jabatan' => $jabatan,
        ];

        return view('backend.master.jabatan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        // Validasi unique dengan mengabaikan ID saat ini sudah benar.
        // Pertimbangkan Form Request (misal: UpdateJabatanRequest)
        $request->validate([
            'name' => 'required|string|max:155|unique:jabatans,name,' . $jabatan->id,
        ]);

        $jabatan->update($request->all());

        Session::flash('success', 'Jabatan berhasil diperbarui!');
        return redirect()->route('jabatan.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        // PENTING: Tambahkan pengecekan relasi sebelum menghapus.
        // Jika jabatan ini digunakan di tabel 'biodatas', penghapusan bisa gagal
        // jika ada foreign key constraint dengan onDelete('restrict').
        // Atau, jika Anda ingin memberi pesan yang lebih ramah:
        if ($jabatan->biodatas()->exists()) { // Asumsi ada relasi 'biodatas()' di model Jabatan
            Session::flash('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh data personil.');
            return redirect()->route('jabatan.index');
        }

        $jabatan->delete();

        Session::flash('success', 'Jabatan berhasil dihapus!');
        return redirect()->route('jabatan.index');
    }
}
