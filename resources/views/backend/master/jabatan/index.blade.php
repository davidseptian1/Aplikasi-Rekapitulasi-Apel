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
                            <a class="btn btn-primary" href="{{ route('jabatan.create') }}">
                                <i class="fa fa-plus-circle me-2" aria-hidden="true"></i>Tambah {{ $pages }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @include('backend.partials.alert') {{-- Pastikan alert ini menangani 'success' dan 'error' --}}

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Daftar {{ $pages }}</h5>
                        {{-- Tempat untuk filter atau search jika diperlukan di masa mendatang --}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped datatable"> {{-- Class datatable tetap --}}
                                <thead class="thead-light"> {{-- Header tabel dengan latar belakang terang --}}
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th>Nama Jabatan</th>
                                        <th class="text-center no-sort" style="width: 15%;">Aksi</th> {{-- Kolom aksi,
                                        text-center, tidak bisa disortir --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jabatans as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center"> {{-- Flexbox untuk menata tombol
                                                aksi --}}
                                                <a href="{{ route('jabatan.edit', $row->id) }}"
                                                    class="btn btn-sm btn-outline-warning me-2" title="Ubah">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('jabatan.destroy', $row->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan: \'{{ $row->name }}\'?')">
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
                                        <td colspan="3" class="text-center">Data jabatan belum tersedia.</td>
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
{{-- Script untuk DataTables bisa diletakkan di layout utama jika digunakan di banyak halaman --}}
{{-- <script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" // Untuk bahasa Indonesia
            },
            "columnDefs": [
                { "targets": 'no-sort', "orderable": false } // Menonaktifkan sorting untuk kolom dengan class 'no-sort'
            ]
        });
    });
</script> --}}
@endpush