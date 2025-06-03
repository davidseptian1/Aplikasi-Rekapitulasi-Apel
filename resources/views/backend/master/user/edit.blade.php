@extends('layouts.app-backend')

@push('styles')
<style>
    .profile-upload-wrapper {
        margin-bottom: 1.5rem;
    }

    .profile-preview-container {
        width: 150px;
        height: 150px;
        margin: 0 auto 1rem;
        border: 3px solid #eee;
        border-radius: 50%;
        overflow: hidden;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-preview-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .section-header {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
    }

    .section-title {
        color: var(--bs-primary, #0d6efd);
    }

    .form-section:not(:last-child) {
        margin-bottom: 2.5rem !important;
    }

    .form-actions {
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }

    .btn-rounded {
        border-radius: 50px;
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit {{ $pages }}</h5>
            </div>
            <div class="card-body">
                <div class="page-header mb-4">
                    <div class="content-page-header">
                        <div class="text-muted">Perbarui informasi pengguna: <strong>{{ $user->name }}</strong>. Semua
                            field dengan tanda <span class="text-danger">*</span> wajib diisi.</div>
                    </div>
                </div>

                @include('backend.partials.alert')

                <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <div class="section-header d-flex align-items-center justify-content-between mb-4">
                            <h5 class="section-title mb-0">Informasi Akun & Personal</h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-12">
                                <div class="profile-upload-wrapper text-center mb-4">
                                    <div class="profile-preview-container">
                                        <img id="previewPhoto" class="profile-preview-img" src="{{ $user->photo_url }}"
                                            alt="Profile Preview">
                                    </div>
                                    <div class="upload-actions">
                                        <input type="file" name="photos" id="photos" class="d-none"
                                            accept="image/jpeg,image/png,image/jpg">
                                        <label for="photos" class="btn btn-outline-primary btn-sm btn-rounded">
                                            <i class="fas fa-camera me-1"></i>Ubah Foto
                                        </label>
                                        @if ($user->photos)
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-rounded mt-2"
                                            id="removePhotoButton">
                                            <i class="fas fa-trash-alt me-1"></i>Hapus Foto
                                        </button>
                                        <input type="hidden" name="remove_photo" id="remove_photo_input" value="0">
                                        @endif
                                        <small class="d-block text-muted mt-1">Max 2MB (JPG, PNG)</small>
                                        @error('photos') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="nrp" class="form-label">NRP</label>
                                    <input type="text" class="form-control @error('nrp') is-invalid @enderror" id="nrp"
                                        name="nrp" value="{{ old('nrp', $user->nrp) }}" placeholder="Masukkan NRP">
                                    @error('nrp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="pangkat_id" class="form-label">Pangkat <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('pangkat_id') is-invalid @enderror"
                                        id="pangkat_id" name="pangkat_id" required>
                                        <option value="" disabled>- Pilih Pangkat -</option>
                                        @foreach ($pangkats as $pangkat)
                                        <option value="{{ $pangkat->id }}" {{ old('pangkat_id', optional($user->
                                            biodata)->pangkat_id) == $pangkat->id ? 'selected' : '' }}>
                                            {{ $pangkat->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('pangkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="jabatan_id" class="form-label">Jabatan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('jabatan_id') is-invalid @enderror"
                                        id="jabatan_id" name="jabatan_id" required>
                                        <option value="" disabled>- Pilih Jabatan -</option>
                                        @foreach ($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id }}" {{ old('jabatan_id', optional($user->
                                            biodata)->jabatan_id) == $jabatan->id ? 'selected' : '' }}>
                                            {{ $jabatan->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('jabatan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-12">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama Lengkap <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="no_telpon" class="form-label">No. Telepon</label>
                                        <input type="text" class="form-control @error('no_telpon') is-invalid @enderror"
                                            id="no_telpon" name="no_telpon"
                                            value="{{ old('no_telpon', $user->no_telpon) }}">
                                        @error('no_telpon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text"
                                                class="form-control @error('username') is-invalid @enderror"
                                                id="username" name="username"
                                                value="{{ old('username', $user->username) }}" required
                                                autocomplete="username">
                                        </div>
                                        @error('username') <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subdis_id" class="form-label">Subdirektorat</label>
                                        <select class="form-select @error('subdis_id') is-invalid @enderror"
                                            id="subdis_id" name="subdis_id">
                                            <option value="">- Pilih Subdis (Jika Ada) -</option>
                                            @foreach ($subdis_list as $subdis_item)
                                            <option value="{{ $subdis_item->id }}" {{ old('subdis_id', $user->subdis_id)
                                                == $subdis_item->id ? 'selected' : '' }}>
                                                {{ $subdis_item->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('subdis_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">Role <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('role') is-invalid @enderror" id="role"
                                            name="role" required>
                                            @foreach ($roles as $roleValue)
                                            <option value="{{ $roleValue }}" {{ old('role', $user->role) == $roleValue ?
                                                'selected' : '' }}>
                                                {{ ucfirst($roleValue) }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password"
                                                placeholder="Kosongkan jika tidak ingin diubah"
                                                autocomplete="new-password">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"><i
                                                    class="fas fa-eye"></i></button>
                                        </div>
                                        <small class="form-text text-muted">Minimal 8 karakter. Kosongkan jika tidak
                                            ingin mengubah password.</small>
                                        <div class="password-strength mt-1">
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="text-muted">Kekuatan: <span
                                                    class="strength-text">-</span></small>
                                        </div>
                                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password
                                            Baru</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation"
                                                placeholder="Kosongkan jika tidak ingin diubah"
                                                autocomplete="new-password">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"><i
                                                    class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3 mt-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                id="is_active" {{ old('is_active', $user->is_active) == '1' ? 'checked'
                                            : '' }}>
                                            <label class="form-check-label" for="is_active">Akun Aktif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions text-end">
                        <a href="{{ route('user.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk preview foto, toggle password, password strength, dan validasi form
        // (Gunakan script yang sama seperti pada create.blade.php)
        // Tambahkan logika untuk tombol "Hapus Foto" jika diperlukan

        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        const password = form.querySelector('#password');
                        const confirmPassword = form.querySelector('#password_confirmation');

                        // Hanya validasi konfirmasi password jika password baru diisi
                        if (password && password.value.length > 0 && confirmPassword && password.value !== confirmPassword.value) {
                            confirmPassword.setCustomValidity('Password tidak cocok.');
                            event.preventDefault();
                            event.stopPropagation();
                        } else if (confirmPassword) {
                            confirmPassword.setCustomValidity('');
                        }
                        form.classList.add('was-validated')
                    }, false)
                })

            // Photo Preview & Remove
            const photoInput = document.getElementById('photos');
            const previewPhoto = document.getElementById('previewPhoto');
            const defaultPhoto = "{{ asset('assets/img/default-user.jpg') }}";
            const removePhotoButton = document.getElementById('removePhotoButton');
            const removePhotoInput = document.getElementById('remove_photo_input');

            if (photoInput && previewPhoto) {
                photoInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) { // Max 2MB
                            alert('Ukuran file maksimal 2MB');
                            this.value = '';
                            // Jangan reset preview jika sudah ada foto sebelumnya, kecuali jika input 'remove_photo' diaktifkan
                            // previewPhoto.src = defaultPhoto;
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewPhoto.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                        if(removePhotoInput) removePhotoInput.value = '0'; // Jika user memilih foto baru, batalkan remove
                    }
                });
            }

            if (removePhotoButton && previewPhoto && removePhotoInput) {
                removePhotoButton.addEventListener('click', function() {
                    if (confirm('Apakah Anda yakin ingin menghapus foto profil saat ini?')) {
                        previewPhoto.src = defaultPhoto;
                        photoInput.value = ''; // Kosongkan input file
                        removePhotoInput.value = '1'; // Set flag untuk menghapus foto di server
                    }
                });
            }


            // Password Toggle
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const inputGroup = this.closest('.input-group');
                    const input = inputGroup.querySelector('input[type="password"], input[type="text"]');
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

             // Password Strength (identik dengan create)
            const passwordField = document.getElementById('password');
            if (passwordField) {
                passwordField.addEventListener('input', function() {
                    const password = this.value;
                    // Cari elemen .password-strength yang satu level dengan input-group atau mb-3 dari password
                    const parentContainer = this.closest('.mb-3');
                    const strengthWrapper = parentContainer.querySelector('.password-strength');

                    if (!strengthWrapper) return;

                    const progressBar = strengthWrapper.querySelector('.progress-bar');
                    const strengthText = strengthWrapper.querySelector('.strength-text');

                    if (password.length === 0) {
                        progressBar.style.width = '0%';
                        progressBar.className = 'progress-bar';
                        strengthText.textContent = '-'; // Teks default saat kosong
                        return;
                    }
                    const strength = checkPasswordStrength(password);
                    progressBar.style.width = strength.percentage + '%';
                    progressBar.className = 'progress-bar ' + strength.class;
                    strengthText.textContent = strength.text;
                });
            }
            function checkPasswordStrength(password) { // Fungsi yang sama
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.length >= 10) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                const percentage = Math.min((strength / 6) * 100, 100);
                let strengthClass = 'bg-danger';
                let text = 'Sangat Lemah';

                if (strength <= 2) {
                    strengthClass = 'bg-danger'; text = 'Lemah';
                } else if (strength <= 3) {
                    strengthClass = 'bg-warning'; text = 'Sedang';
                } else if (strength <= 4) {
                    strengthClass = 'bg-info'; text = 'Cukup Kuat';
                } else {
                    strengthClass = 'bg-success'; text = 'Kuat';
                }
                return { percentage, class: strengthClass, text };
            }

        })();
</script>
@endpush