@extends('layouts.app-backend')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah {{ $pages }}</h5>
            </div>
            <div class="card-body">
                @include('backend.partials.alert')

                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('jabatan.store') }}" method="POST">
                            @csrf
                            <div class="form-group-item">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label for="name" class="form-label">Nama Jabatan <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" placeholder="Masukkan Nama Jabatan" required
                                                autofocus>
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="add-customer-btns text-end">
                                <a href="{{ route('jabatan.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection