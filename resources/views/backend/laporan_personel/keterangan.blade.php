@extends('layouts.app-backend')

@push('styles')
<style>
    .filter-bar {
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $pages }}</h5>
                <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="filter-bar">
            <form method="GET" action="{{ route('laporan.personel.keterangan') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="date_filter" class="form-label">Pilih Tanggal</label>
                        <input type="date" class="form-control form-control-sm" id="date_filter" name="date"
                            value="{{ $filterDate }}">
                    </div>
                    <div class="col-md-2">
                        <label for="type_filter" class="form-label">Jenis Apel</label>
                        <select class="form-select form-select-sm" name="type" id="type_filter">
                            <option value="pagi" {{ $selectedType=='pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="sore" {{ $selectedType=='sore' ? 'selected' : '' }}>Sore</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="subdis_filter" class="form-label">Pilih Subdis</label>
                        <select class="form-select form-select-sm select2-filter" id="subdis_filter" name="subdis_id">
                            <option value="">-- Semua Subdis --</option>
                            @foreach($subdisList as $subdis)
                            <option value="{{ $subdis->id }}" {{ $selectedSubdisId==$subdis->id ? 'selected' : '' }}>
                                {{ $subdis->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="keterangan_filter" class="form-label">Keterangan</label>
                        <select class="form-select form-select-sm select2-filter" id="keterangan_filter"
                            name="keterangan_id">
                            <option value="">-- Semua --</option>
                            @foreach($masterKeterangans as $keterangan)
                            <option value="{{ $keterangan->id }}" {{ $selectedKeteranganId==$keterangan->id ? 'selected'
                                : '' }}>
                                {{ $keterangan->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-auto">
                        <a href="{{ route('laporan.personel.keterangan.pdf', request()->query()) }}"
                            class="btn btn-sm btn-success w-100" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0 datatable">
                        <thead>
                            <tr>
                                <th>Nama Personel</th>
                                <th>Keterangan Kehadiran</th>
                                <th>Jenis Apel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allAttendances as $attendance)
                            <tr>
                                <td>{{ $attendance->user_name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $attendance->keterangan_name }}</span>
                                </td>
                                <td>{{ ucfirst($attendance->apel_type) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data ditemukan untuk filter yang dipilih.
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-filter').select2({
            width: '100%',
            allowClear: true,
            placeholder: '-- Pilih --'
        });
    });
</script>
@endpush