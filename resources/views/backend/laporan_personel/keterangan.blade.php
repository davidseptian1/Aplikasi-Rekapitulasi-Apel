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

    .keterangan-card {
        background-color: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.03);
    }

    .keterangan-card-header {
        background-color: #f8f9fa;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        font-size: 1.05rem;
        color: #343a40;
    }

    .keterangan-card-body {
        padding: 1.25rem;
    }

    .keterangan-card-body ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .keterangan-card-body ul li {
        padding: 0.4rem 0;
        font-size: 0.9rem;
        border-bottom: 1px dotted #eee;
    }

    .keterangan-card-body ul li:last-child {
        border-bottom: none;
    }

    .no-data-message {
        text-align: center;
        padding: 20px;
        color: #6c757d;
    }

    .btn-cetak-pdf-keterangan {
        font-weight: 500;
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-cetak-pdf-keterangan:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
.keterangan-card-body {
        padding: 1rem;
    }

    .keterangan-card-body ul {
        max-height: 16rem;   /* Tinggi maksimum 8 baris */
        min-height: 16rem;   /* Tinggi minimum agar card tetap tinggi */
        overflow-y: auto;    /* Scroll jika lebih dari 8 */
        padding-left: 1rem;
        margin: 0;
        list-style-type: disc;
    }

    .keterangan-card-body ul li {
        line-height: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .no-data-message {
        min-height: 16rem; /* Agar card kosong tetap tinggi */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        margin: 0;
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
                        <label for="date_filter_lap_ket" class="form-label">Pilih Tanggal</label>
                        <input type="date" class="form-control form-control-sm" id="date_filter_lap_ket" name="date"
                            value="{{ $filterDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="subdis_filter_lap_ket" class="form-label">Pilih Subdis</label>
                        <select class="form-select form-select-sm select2-filter" id="subdis_filter_lap_ket"
                            name="subdis_id">
                            <option value="">-- Semua Subdis --</option>
                            @foreach($subdisList as $subdis)
                            <option value="{{ $subdis->id }}" {{ $selectedSubdisId==$subdis->id ? 'selected' : '' }}>
                                {{ $subdis->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="keterangan_filter_lap_ket" class="form-label">Pilih Keterangan</label>
                        <select class="form-select form-select-sm select2-filter" id="keterangan_filter_lap_ket"
                            name="keterangan_id">
                            <option value="">-- Semua Keterangan --</option>
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
                        <a href="{{ route('laporan.personel.keterangan.pdf', ['date' => $filterDate, 'subdis_id' => $selectedSubdisId, 'keterangan_id' => $selectedKeteranganId]) }}"
                            class="btn btn-sm btn-cetak-pdf-keterangan w-100" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
    @if($groupedAttendances->isEmpty())
    <div class="col-12">
        <div class="alert alert-info text-center">
            Tidak ada data keterangan ditemukan untuk filter yang dipilih.
        </div>
    </div>
    @else
    @foreach($groupedAttendances as $keteranganName => $attendances)
    <div class="col-md-6 col-lg-4">
        <div class="card keterangan-card">
            <div class="keterangan-card-header">
                {{ $keteranganName }} ({{ $attendances->count() }})
            </div>
            <div class="keterangan-card-body">
                @if($attendances->count() > 0)
                <ul>
                    @foreach($attendances as $att)
                    <li>
                        {{ $att->user_name }}
                        @if($att->user_nrp)
                        <small class="text-muted">(NRP: {{ $att->user_nrp }})</small>
                        @endif
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="no-data-message">Tidak ada personel dengan keterangan ini.</p>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>

        @if(!$groupedAttendances->isEmpty() && $totalRecords > 10)
        <div class="mt-3 text-center">
            <small class="text-muted">Menampilkan {{ $totalRecords }} entri. Pertimbangkan filter untuk hasil lebih
                spesifik.</small>
        </div>
        @endif


    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    // Initialize Select2 for filters
    if ($('.select2-filter').length) {
        $('.select2-filter').select2({
            width: '100%',
            allowClear: true, // Allows clearing the selection for optional filters
            placeholder: $(this).data('placeholder') || '-- Pilih --'
        });
    }
});
</script>
@endpush