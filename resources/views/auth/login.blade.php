@extends('layouts.app-auth')

@section('content')
<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <img class="img-fluid logo-dark mb-2 logo-color" src="{{ asset('assets/img/logo2.png') }}" alt="Logo">
            <img class="img-fluid logo-light mb-2" src="{{ asset('assets/img/logo2-white.png') }}" alt="Logo">
            <div class="loginbox">
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>Masuk</h1>
                        <p class="account-subtitle">Akses ke dashboard Anda</p>

                        @include('backend.partials.alert')

                        <form action="{{ route('login.process') }}" method="POST">
                            @csrf

                            <div class="input-block mb-3">
                                <label class="form-control-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    name="username" id="username" value="{{ old('username') }}"
                                    placeholder="Enter your username" autocomplete="off" required autofocus>
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="input-block mb-3">
                                <label class="form-control-label">Password</label>
                                <div class="pass-group">
                                    <input type="password"
                                        class="form-control pass-input @error('password') is-invalid @enderror"
                                        name="password" id="password" placeholder="********" required
                                        autocomplete="current-password">
                                    <span class="fas fa-eye toggle-password"></span>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="input-block mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Remember Me</label>
                                </div>
                            </div>
                            <button class="btn btn-lg btn-primary w-100" type="submit">Login</button>
                        </form>

                        @if (Route::has('password.request'))
                        <div class="text-center dont-have">Lupa password?
                            <a href="{{ route('password.request') }}">Reset password</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function(element) {
            element.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        });
</script>
@endpush