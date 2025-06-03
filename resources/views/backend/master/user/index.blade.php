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
                            <a class="btn btn-primary" href="{{ route('user.create') }}">
                                <i class="fa fa-plus-circle me-2" aria-hidden="true"></i>Tambah {{ $pages }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="card">
            <div class="card-body pb-0">
                <form method="GET" action="{{ route('user.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="input-block mb-3">
                                <label for="role_filter" class="form-label">Filter berdasarkan Role:</label>
                                <select name="role_filter" id="role_filter" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Role</option>
                                    @foreach ($availableRoles as $role)
                                    <option value="{{ $role }}" {{ $selectedRole==$role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            {{-- Tombol Reset Filter bisa ditambahkan jika filter sudah dipilih --}}
                            @if (!empty($selectedRole))
                            <div class="input-block mb-3">
                                <a href="{{ route('user.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset
                                    Filter</a>
                            </div>
                            @endif
                        </div>
                        {{-- Bisa tambahkan filter lain di sini --}}
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-3"> {{-- Tambah margin atas setelah filter --}}
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Daftar {{ $pages }} @if($selectedRole) (Role: {{ ucfirst($selectedRole)
                            }}) @endif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 3%;">#</th>
                                        <th style="width: 5%;">Foto</th>
                                        <th>Nama</th>
                                        <th>NRP</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Pangkat</th>
                                        <th>Jabatan</th>
                                        <th>Subdis</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center no-sort" style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                    <tr>
                                        <td class="align-middle">{{ $loop->iteration }}</td>
                                        <td class="align-middle text-center">
                                            <img src="{{ $user->photo_url }}" alt="Foto"
                                                class="avatar avatar-sm rounded-circle me-2">
                                        </td>
                                        <td class="align-middle">
                                            {{ $user->name }}<br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </td>
                                        <td class="align-middle">{{ $user->nrp ?? '-' }}</td>
                                        <td class="align-middle">{{ $user->username }}</td>
                                        <td class="align-middle">
                                            <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                                        </td>
                                        <td class="align-middle">{{ $user->biodata->pangkat->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $user->biodata->jabatan->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $user->subdis->name ?? '-' }}</td>
                                        <td class="align-middle text-center">
                                            @if ($user->is_active == '1')
                                            <span class="badge bg-success">Aktif</span>
                                            @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('user.edit', $user->id) }}"
                                                    class="btn btn-sm btn-outline-warning me-2" title="Ubah">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna \'{{ $user->name }}\'? Tindakan ini tidak dapat diurungkan.')">
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
                                        <td colspan="11" class="text-center">
                                            @if (!empty($selectedRole))
                                            Tidak ada pengguna dengan role "{{ ucfirst($selectedRole) }}". <a
                                                href="{{ route('user.index') }}">Tampilkan semua</a>.
                                            @else
                                            Data pengguna belum tersedia.
                                            @endif
                                        </td>
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