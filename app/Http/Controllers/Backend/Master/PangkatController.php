<?php

namespace App\Http\Controllers\Backend\Master;

use App\Models\Pangkat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session; // Pastikan ini di-import

class PangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Untuk performa yang lebih baik jika data sangat banyak dan menggunakan server-side DataTables,
        // pertimbangkan untuk tidak mengambil semua kolom: Pangkat::select('id', 'name', 'nilai_pangkat')->get();
        // Namun, untuk client-side DataTables dengan jumlah pangkat yang moderat, Pangkat::all() masih oke.
        $pangkats = Pangkat::orderBy('nilai_pangkat', 'asc')->orderBy('name', 'asc')->get(); // Urutkan berdasarkan nilai dan nama

        $data = [
            'title' => 'Pangkat',
            'pages' => 'Pangkat', // Variabel ini digunakan di view, pastikan konsisten
            'pangkats' => $pangkats,
        ];

        return view('backend.master.pangkat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Pangkat',
            'pages' => 'Pangkat', // Konsisten dengan index
        ];

        return view('backend.master.pangkat.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Pertimbangkan menggunakan Form Request untuk validasi yang lebih kompleks
        // Contoh: php artisan make:request StorePangkatRequest
        $request->validate([
            'name' => 'required|string|max:155|unique:pangkats,name',
            'nilai_pangkat' => 'required|integer|min:0', // Validasi untuk nilai_pangkat
        ]);

        Pangkat::create([
            'name' => $request->name,
            'nilai_pangkat' => $request->nilai_pangkat,
        ]);

        Session::flash('success', 'Pangkat berhasil ditambahkan!');
        return redirect()->route('pangkat.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Biasanya tidak digunakan untuk master data sederhana,
        // tapi jika diperlukan, implementasikan di sini.
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pangkat = Pangkat::findOrFail($id);

        $data = [
            'title' => 'Edit Pangkat',
            'pages' => 'Pangkat', // Konsisten
            'pangkat' => $pangkat,
        ];

        return view('backend.master.pangkat.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pangkat = Pangkat::findOrFail($id);

        // Pertimbangkan menggunakan Form Request di sini juga
        $request->validate([
            // Pastikan validasi unique mengabaikan ID saat ini
            'name' => 'required|string|max:155|unique:pangkats,name,' . $pangkat->id,
            'nilai_pangkat' => 'required|integer|min:0', // Validasi untuk nilai_pangkat
        ]);

        $pangkat->update([
            'name' => $request->name,
            'nilai_pangkat' => $request->nilai_pangkat,
        ]);

        Session::flash('success', 'Pangkat berhasil diperbarui!');
        return redirect()->route('pangkat.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pangkat = Pangkat::findOrFail($id);
        // Tambahkan pengecekan relasi jika pangkat tidak boleh dihapus jika masih digunakan,
        // misalnya di tabel biodatas. Saat ini onDelete('restrict') di migration biodatas akan mencegah ini.
        if ($pangkat->biodatas()->exists()) {
            Session::flash('error', 'Pangkat tidak dapat dihapus karena masih digunakan.');
            return redirect()->route('pangkat.index');
        }
        $pangkat->delete();

        Session::flash('success', 'Pangkat berhasil dihapus!');
        return redirect()->route('pangkat.index');
    }
}
