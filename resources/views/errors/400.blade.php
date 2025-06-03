@extends('layouts.app-auth')

@section('content')
    <div class="main-wrapper">
        <div class="error-box">
            <h1>400</h1>
            <h3 class="h2 mb-3"><i class="fas fa-exclamation-triangle"></i> Permintaan Tidak Valid</h3>
            <p class="h4 font-weight-normal">{{ $exception->getMessage() }}</p>
            <a href="{{ route('dashboard.index') }}" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection
