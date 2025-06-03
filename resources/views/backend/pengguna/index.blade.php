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
                                <a class="btn btn-primary" href="">
                                    <i class="fa fa-plus-circle me-2" aria-hidden="true"></i>Tambah {{ $pages }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-table">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-center table-hover datatable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th class="no-sort">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="profile.html" class="avatar avatar-md me-2"><img
                                                            class="avatar-img rounded-circle"
                                                            src="assets/img/profiles/avatar-14.jpg" alt="User Image"></a>
                                                    <a href="profile.html">John Smith <span>john@example.com</span></a>
                                                </h2>
                                            </td>
                                            <td class="d-flex align-items-center">
                                                <a href="" class="btn btn-greys me-2" title="Ubah">
                                                    <i class="fa fa-edit me-1"></i>
                                                </a>
                                                <a href="" class="btn btn-greys me-2" title="Hapus">
                                                    <i class="fa fa-trash me-1"></i>
                                                </a>
                                            </td>
                                        </tr>
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
