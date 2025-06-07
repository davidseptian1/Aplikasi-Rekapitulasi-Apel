@extends('layouts.app-backend')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">{{ $title }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengaturan Jam Apel</li>
                    </ul>
                </div>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Atur Jadwal Rekap Apel</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('jam-apel.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Apel Pagi --}}
                            <h5 class="mb-3">Apel Pagi</h5>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="pagi_start_time" class="form-label">Waktu Mulai</label>
                                    <input type="time"
                                        class="form-control @error('pagi_start_time') is-invalid @enderror"
                                        id="pagi_start_time" name="pagi_start_time"
                                        value="{{ old('pagi_start_time', $jamPagi ? \Carbon\Carbon::parse($jamPagi->start_time)->format('H:i') : '') }}">
                                    @error('pagi_start_time') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="pagi_end_time" class="form-label">Waktu Berakhir</label>
                                    <input type="time" class="form-control @error('pagi_end_time') is-invalid @enderror"
                                        id="pagi_end_time" name="pagi_end_time"
                                        value="{{ old('pagi_end_time', $jamPagi ? \Carbon\Carbon::parse($jamPagi->end_time)->format('H:i') : '') }}">
                                    @error('pagi_end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr>

                            {{-- Apel Sore --}}
                            <h5 class="mb-3">Apel Sore</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sore_start_time" class="form-label">Waktu Mulai</label>
                                    <input type="time"
                                        class="form-control @error('sore_start_time') is-invalid @enderror"
                                        id="sore_start_time" name="sore_start_time"
                                        value="{{ old('sore_start_time', $jamSore ? \Carbon\Carbon::parse($jamSore->start_time)->format('H:i') : '') }}">
                                    @error('sore_start_time') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="sore_end_time" class="form-label">Waktu Berakhir</label>
                                    <input type="time" class="form-control @error('sore_end_time') is-invalid @enderror"
                                        id="sore_end_time" name="sore_end_time"
                                        value="{{ old('sore_end_time', $jamSore ? \Carbon\Carbon::parse($jamSore->end_time)->format('H:i') : '') }}">
                                    @error('sore_end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection