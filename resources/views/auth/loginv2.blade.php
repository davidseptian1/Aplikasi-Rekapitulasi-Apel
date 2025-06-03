{{-- auth/loginv2.blade.php --}}
@extends('layouts.app-authv2')

@push('styles')
<style>
    /* Custom CSS untuk halaman login */
    body,
    html {
        height: 100%;
    }

    .page.page-center {
        display: flex;
        align-items: center;
        /* Vertically center */
        justify-content: center;
        /* Horizontally center */
        min-height: 100vh;
        background-color: #f8f9fa;
        /* Warna latar belakang netral */
        padding-top: 1rem;
        /* Beri sedikit padding atas & bawah untuk viewport */
        padding-bottom: 1rem;
    }

    .login-container {
        /* Sebelumnya: max-width: 900px; */
        width: 90%;
        /* MEMPERLEBAR: Ambil 90% dari lebar parent (container-fluid) */
        max-width: 1600px;
        /* MEMPERLEBAR: Batas lebar maksimum pada layar sangat besar */
        /* mx-auto sudah ada di class HTML, akan otomatis center */
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
        /* Penting agar border-radius bekerja pada child */
        /* display: flex; -- Tidak perlu jika child-nya (row) sudah mengatur dirinya sendiri */
    }

    .login-form-column {
        padding: 2.5rem;
        /* d-flex flex-column justify-content-center ditambahkan di HTML untuk centering vertikal konten form */
    }

    .login-image-column {
        background-color: #e9ecef;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        position: relative;
    }

    .company-logo-text {
        position: absolute;
        top: 20px;
        font-weight: bold;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .main-illustration {
        max-width: 80%;
        height: auto;
        margin-top: 20px;
    }

    .company-name-text {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        text-align: left;
    }

    .login-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--tblr-primary, #206bc4);
        margin-bottom: 0.5rem;
        text-align: left;
    }

    .login-subtitle {
        font-size: 0.95rem;
        color: #495057;
        margin-bottom: 2rem;
        text-align: left;
    }

    .form-footer .btn {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .password-toggle-icon {
        cursor: pointer;
    }

    @media (max-width: 991.98px) {

        /* lg breakpoint */
        .login-container {
            width: 95%;
            /* Di layar kecil, bisa sedikit lebih lebar persentasenya */
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .login-image-column {
            min-height: 250px;
            padding: 1.5rem;
            order: -1;
            /* Pindahkan gambar ke atas pada mobile */
        }

        .main-illustration {
            max-width: 60%;
        }

        .login-form-column {
            padding: 2rem 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="page page-center">
    {{-- Container-fluid agar .login-container (dengan width: 90%) bisa mengambil persentase dari lebar viewport --}}
    <div class="container-fluid p-0 d-flex align-items-center justify-content-center" style="flex-grow: 1;">
        {{-- Penambahan class 'mx-auto' pada .login-container akan membuatnya terpusat horizontal --}}
        <div class="row g-0 align-items-stretch login-container mx-auto">
            {{-- Penambahan class d-flex flex-column justify-content-center untuk centering vertikal konten di kolom
            form --}}
            <div class="col-lg-6 login-form-column d-flex flex-column justify-content-center">
                <div> {{-- Wrapper tambahan untuk konten form --}}
                    <div class="company-name-text">XYZ</div>
                    <h2 class="login-title">Rekap Apel Kehadiran Personel</h2>
                    <p class="login-subtitle">
                        Selamat datang di Halaman Login, silahkan Login!
                    </p>

                    @if (session('status'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form id="loginForm" action="{{ route('login.process') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                name="username" id="username" value="{{ old('username') }}"
                                placeholder="Masukkan username Anda" autocomplete="username" required autofocus>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" id="password" placeholder="Masukkan password Anda" required
                                    autocomplete="current-password">
                                <span class="input-group-text password-toggle-icon">
                                    <svg id="togglePasswordIcon" xmlns="http://www.w3.org/2000/svg" class="icon"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path
                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </span>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{
                                    old('remember') ? 'checked' : '' }}>
                                <span class="form-check-label">Remember Me</span>
                            </label>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100" id="loginButton">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                    aria-hidden="true" id="loginSpinner"></span>
                                Login
                            </button>
                        </div>
                    </form>

                    @if (Route::has('password.request'))
                    <div class="text-center text-secondary mt-3">
                        Lupa password? <a href="{{ route('password.request') }}" tabindex="-1">Reset password</a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-flex login-image-column">
                <div class="company-logo-text">XYZ</div>
                <img src="{{ asset('assets/auth/dist/img/gambarlogin.png') }}" alt="Ilustrasi Login"
                    class="main-illustration">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // JavaScript untuk toggle password dan spinner (SAMA SEPERTI SEBELUMNYA, TIDAK ADA PERUBAHAN)
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const togglePasswordIconContainer = document.querySelector('.password-toggle-icon');
    const togglePasswordSvg = document.getElementById('togglePasswordIcon');

    const eyeIconSvg = `
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />`;
    const eyeOffIconSvg = `
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" />
        <path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.319 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.821 -.18c3.6 0 6.6 2 9 6c-.633 1.05 -1.309 1.976 -2.036 2.77" />
        <path d="M3 3l18 18" />`;

    if (togglePasswordIconContainer && passwordInput && togglePasswordSvg) {
        togglePasswordIconContainer.addEventListener('click', function (e) {
            e.preventDefault();
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'password') {
                togglePasswordSvg.innerHTML = eyeIconSvg;
            } else {
                togglePasswordSvg.innerHTML = eyeOffIconSvg;
            }
        });
    }

    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const loginSpinner = document.getElementById('loginSpinner');

    if (loginForm && loginButton && loginSpinner) {
        loginForm.addEventListener('submit', function() {
            loginButton.setAttribute('disabled', 'disabled');
            loginSpinner.classList.remove('d-none');
        });
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endpush