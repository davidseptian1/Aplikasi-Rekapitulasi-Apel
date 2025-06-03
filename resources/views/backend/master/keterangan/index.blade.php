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
                            <a class="btn btn-primary" href="{{ route('keterangan.create') }}">
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
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped datatable"> {{-- Class datatable tetap --}}
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th>Nama Keterangan</th>
                                        <th class="text-center no-sort" style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($keterangans as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('keterangan.edit', $row->id) }}"
                                                    class="btn btn-sm btn-outline-warning me-2" title="Ubah">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('keterangan.destroy', $row->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus keterangan: \'{{ $row->name }}\'?')">
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
                                        <td colspan="3" class="text-center">Data keterangan belum tersedia.</td>
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
                // Opsi untuk lokalisasi ke Bahasa Indonesia jika diperlukan
                // "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "columnDefs": [
                { "targets": 'no-sort', "orderable": false }
            ]
        });
    });
</script> --}}
@endpush