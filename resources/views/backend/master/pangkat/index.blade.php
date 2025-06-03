@extends('layouts.app-backend')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header">
                <h5>{{ $pages }}</h5>
                <div class="list-btn">
                    <ul class="filter-list">
                        <li>
                            <a class="btn btn-primary" href="{{ route('pangkat.create') }}">
                                <i class="fa fa-plus-circle me-2" aria-hidden="true"></i>Tambah {{ $pages }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @include('backend.partials.alert') {{-- Pastikan alert ini menampilkan session 'success' dan 'error' --}}

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Daftar {{ $pages }}</h5>
                        {{-- Anda bisa menambahkan filter atau search di sini jika perlu --}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped datatable"> {{-- Tetap menggunakan class datatable --}}
                                <thead class="thead-light"> {{-- Memberi sedikit kontras pada header --}}
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th>Nama Pangkat</th>
                                        <th style="width: 15%;">Nilai Pangkat</th> {{-- Kolom baru --}}
                                        <th class="text-center no-sort" style="width: 15%;">Aksi</th> {{-- no-sort agar
                                        tidak disortir DataTables, text-center untuk alignment --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pangkats as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->nilai_pangkat }}</td> {{-- Data baru --}}
                                        <td class="text-center"> {{-- Aksi dibuat text-center --}}
                                            <div class="d-flex justify-content-center"> {{-- Menggunakan flex untuk
                                                menata tombol --}}
                                                <a href="{{ route('pangkat.edit', $row->id) }}"
                                                    class="btn btn-sm btn-outline-warning me-2" title="Ubah"> {{--
                                                    btn-sm dan btn-outline-* untuk tampilan modern --}}
                                                    <i class="fa fa-edit"></i> {{-- Icon saja atau dengan teks --}}
                                                </a>
                                                <form action="{{ route('pangkat.destroy', $row->id) }}" method="POST"
                                                    class="d-inline" {{-- d-inline agar form tidak mengambil baris baru
                                                    --}}
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini: {{ $row->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data pangkat belum tersedia.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi DataTables (jika belum ada di script.js global)
    // $(document).ready(function() {
    //     $('.datatable').DataTable({
    //         "language": {
    //             "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" // Contoh lokalisasi
    //         },
    //         "columnDefs": [
    //             { "targets": 'no-sort', "orderable": false } // Menonaktifkan sorting pada kolom dengan class no-sort
    //         ]
    //     });
    // });
</script>
@endpush