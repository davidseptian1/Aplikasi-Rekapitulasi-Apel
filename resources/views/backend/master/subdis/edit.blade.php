@extends('layouts.app-backend')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit {{ $pages }}</h5>
            </div>
            <div class="card-body">
                @include('backend.partials.alert')

                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('subdis.update', $subdi->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group-item">
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label for="name" class="form-label">Nama Subdirektorat <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $subdi->name) }}"
                                                placeholder="Masukkan Nama Subdirektorat" required autofocus>
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label for="user_id" class="form-label">Penanggung Jawab</label>
                                            <select name="user_id" id="user_id"
                                                class="form-select select @error('user_id') is-invalid @enderror">
                                                <option value="">- Pilih Penanggung Jawab -</option>
                                                @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id', $subdi->user_id) ==
                                                    $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="add-customer-btns text-end">
                                <a href="{{ route('subdis.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
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

{{-- Script Select2 (jika digunakan) --}}
@push('scripts')
<script>
    $(document).ready(function() {
        $('.select').select2();
    });
</script>
@endpush