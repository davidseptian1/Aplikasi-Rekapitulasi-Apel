@extends('layouts.app-backend')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="content-page-header">
                    <h5>{{ $pages }}</h5>
                </div>
            </div>

            @include('backend.partials.alert')

            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="{{ $user->photo_url }}" alt="User Image" class="rounded-circle" width="150"
                                height="150">
                            <h4 class="mt-3">{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->biodata->jabatan->name ?? '-' }}</p>
                            <div class="profile-info">
                                <ul>
                                    <li>
                                        <span>Role</span>
                                        <span class="text-capitalize">{{ $user->role }}</span>
                                    </li>
                                    <li>
                                        <span>Pangkat</span>
                                        <span>{{ $user->biodata->pangkat->name ?? '-' }}</span>
                                    </li>
                                    <li>
                                        <span>Status</span>
                                        <span class="badge bg-{{ $user->is_active == '1' ? 'success' : 'danger' }}">
                                            {{ $user->is_active == '1' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-solid nav-justified">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#basic-info" data-bs-toggle="tab">Informasi Dasar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#change-password" data-bs-toggle="tab">Ubah Password</a>
                                </li>
                            </ul>

                            <div class="tab-content pt-3">
                                <div class="tab-pane show active" id="basic-info">
                                    <form action="{{ route('profile.update.basic') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-block mb-3">
                                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ old('name', $user->name) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-block mb-3">
                                                    <label>Username <span class="text-danger">*</span></label>
                                                    <input type="text" name="username" class="form-control"
                                                        value="{{ old('username', $user->username) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-block mb-3">
                                                    <label>Email <span class="text-danger">*</span></label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ old('email', $user->email) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-block mb-3">
                                                    <label>No. Telepon</label>
                                                    <input type="text" name="no_telpon" class="form-control"
                                                        value="{{ old('no_telpon', $user->no_telpon) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="input-block mb-3">
                                                    <label>Foto Profil</label>
                                                    <input type="file" name="photos" class="form-control"
                                                        accept="image/*">
                                                    <small class="text-muted">Format: JPEG, PNG, JPG (Max 2MB)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane" id="change-password">
                                    <form action="{{ route('profile.update.password') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="text-black">Password Saat Ini <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" name="current_password"
                                                        class="form-control pass-input-1"
                                                        placeholder="Masukkan password saat ini" required>
                                                    <span class="input-group-text toggle-password"
                                                        data-target="pass-input-1">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="text-black">Password Baru <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" name="password" class="form-control pass-input-2"
                                                        placeholder="Masukkan password baru" required>
                                                    <span class="input-group-text toggle-password"
                                                        data-target="pass-input-2">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="text-black">Konfirmasi Password <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" name="password_confirmation"
                                                        class="form-control pass-input-3"
                                                        placeholder="Konfirmasi password baru" required>
                                                    <span class="input-group-text toggle-password"
                                                        data-target="pass-input-3">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn btn-primary">Ubah Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .profile-info ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .profile-info ul li {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .profile-info ul li:last-child {
            border-bottom: none;
        }

        .profile-info ul li span:first-child {
            font-weight: 500;
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle password visibility for each input independently
            $('.toggle-password').on('click', function() {
                const target = $(this).data('target');
                const $passwordInput = $(`.${target}`);
                const $icon = $(this).find('i');

                if ($passwordInput.attr('type') === 'password') {
                    $passwordInput.attr('type', 'text');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $passwordInput.attr('type', 'password');
                    $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Preview photo before upload
            $('input[name="photos"]').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('.card-body.text-center img').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
