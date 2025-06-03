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
                            <a class="btn btn-primary" href="{{ route('subdis.create') }}">
                                <i class="fa fa-plus-circle me-2" aria-hidden="true"></i>Tambah {{ $pages }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Daftar {{ $pages }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th>Nama Subdirektorat</th>
                                        <th>Penanggung Jawab</th>
                                        <th class="text-center no-sort" style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($subdis as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            @if ($row->user)
                                            <span class="badge bg-primary">{{ $row->user->name }}</span>
                                            @else
                                            <span class="badge bg-secondary">- Tidak Ada -</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('subdis.edit', $row->id) }}"
                                                    class="btn btn-sm btn-outline-warning me-2" title="Ubah">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('subdis.destroy', $row->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus Subdirektorat: \'{{ $row->name }}\'?')">
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
                                        <td colspan="4" class="text-center">Data subdirektorat belum tersedia.</td>
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
{{-- Script untuk DataTables --}}
{{-- <script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "language": { /* Opsi lokalisasi */ },
            "columnDefs": [ { "targets": 'no-sort', "orderable": false } ]
        });
        // Jika menggunakan select2 untuk dropdown di form, inisialisasi di sini atau di form view.
        // $('.select').select2({ theme: 'bootstrap-5' }); // Contoh inisialisasi Select2
    });
</script> --}}
@endpush