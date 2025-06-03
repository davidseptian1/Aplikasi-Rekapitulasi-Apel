@extends('layouts.app-auth')

@section('content')
    <div class="main-wrapper">
        <div class="error-box">
            <h1>403</h1>
            <h3 class="h2 mb-3"><i class="fas fa-exclamation-circle"></i> Akses Ditolak</h3>
            <p class="h4 font-weight-normal">Anda tidak memiliki izin untuk mengakses halaman ini</p>
            <a href="{{ route('dashboard.index') }}" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection
