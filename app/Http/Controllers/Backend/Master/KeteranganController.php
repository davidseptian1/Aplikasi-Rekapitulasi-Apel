<?php

namespace App\Http\Controllers\Backend\Master;

use App\Models\Keterangan; // Pastikan model di-import
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session; // Pastikan Session di-import

class KeteranganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua keterangan. Untuk tabel dengan DataTables client-side, ini umumnya oke.
        // Pertimbangkan pengurutan default untuk tampilan yang lebih konsisten.
        $keterangans = Keterangan::orderBy('name', 'asc')->get();

        $data = [
            'title' => 'Keterangan',
            'pages' => 'Keterangan', // Variabel ini digunakan di view Anda
            'keterangans' => $keterangans,
        ];

        return view('backend.master.keterangan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Keterangan',
            'pages' => 'Keterangan', // Konsisten dengan index
        ];

        return view('backend.master.keterangan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi sudah baik.
        // Untuk konsistensi dan skalabilitas, pertimbangkan Form Request (misal: StoreKeteranganRequest)
        $request->validate([
            'name' => 'required|string|max:155|unique:keterangans,name',
        ]);

        // $request->all() aman digunakan di sini karena model Keterangan memiliki $fillable yang sudah ditentukan.
        Keterangan::create($request->all());

        Session::flash('success', 'Keterangan berhasil ditambahkan!');
        return redirect()->route('keterangan.index');
    }

    /**
     * Display the specified resource.
     * Method ini biasanya tidak terlalu sering digunakan untuk master data sederhana.
     */
    public function show(string $id)
    {
        return abort(404); // Jika tidak digunakan
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $keterangan = Keterangan::findOrFail($id); // findOrFail sudah baik.

        $data = [
            'title' => 'Edit Keterangan',
            'pages' => 'Keterangan', // Konsisten
            'keterangan' => $keterangan,
        ];

        return view('backend.master.keterangan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $keterangan = Keterangan::findOrFail($id);

        // Validasi unique dengan mengabaikan ID saat ini sudah benar.
        // Pertimbangkan Form Request (misal: UpdateKeteranganRequest)
        $request->validate([
            'name' => 'required|string|max:155|unique:keterangans,name,' . $keterangan->id,
        ]);

        $keterangan->update($request->all());

        Session::flash('success', 'Keterangan berhasil diperbarui!');
        return redirect()->route('keterangan.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $keterangan = Keterangan::findOrFail($id);

        // PENTING: Tambahkan pengecekan relasi sebelum menghapus.
        // Jika keterangan ini digunakan di tabel 'apel_attendances', penghapusan bisa gagal
        // jika ada foreign key constraint dengan onDelete('restrict').
        // (Saran sebelumnya onDelete('restrict') untuk keterangan_id di apel_attendances)
        if ($keterangan->apelAttendances()->exists()) { // Asumsi ada relasi 'apelAttendances()' di model Keterangan
            Session::flash('error', 'Keterangan tidak dapat dihapus karena masih digunakan dalam rekap apel.');
            return redirect()->route('keterangan.index');
        }

        $keterangan->delete();

        Session::flash('success', 'Keterangan berhasil dihapus!');
        return redirect()->route('keterangan.index');
    }
}
