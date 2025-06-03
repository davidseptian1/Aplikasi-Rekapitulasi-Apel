@extends('layouts.app-backend')

@push('styles')
<style>
    /* Styles can be mostly shared with laporan_global.blade.php, consider a common CSS file */
    .page-header {
        border-bottom: 0;
        padding-bottom: 0;
        margin-bottom: 1.5rem;
    }

    .content-page-header h5 {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .filter-bar {
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table thead th {
        background-color: #f0f2f5;
        font-weight: 600;
        border: 1px solid #dee2e6;
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
        text-align: center;
    }

    .table thead th:nth-child(2) {
        text-align: left;
    }

    /* Nama Personel */
    .table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
    }

    .table tbody td:nth-child(1),
    .table tbody td:nth-child(3),
    .table tbody td:nth-child(4) {
        text-align: center;
    }

    /* No, Pangkat, Keterangan */
    .table tbody td:nth-child(2) {
        text-align: left;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .keterangan-summary-card {
        background-color: #f8f9fa;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-top: 1.5rem;
    }

    .keterangan-summary-card h6 {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #343a40;
        font-size: 1.1rem;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 0.5rem;
    }

    .keterangan-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.5rem 1.5rem;
        font-size: 0.9rem;
    }

    .keterangan-summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.25rem 0;
    }

    .keterangan-summary-item .keterangan-name {
        color: #495057;
    }

    .keterangan-summary-item .keterangan-count {
        font-weight: 500;
        color: #007bff;
    }

    .keterangan-totals-separator {
        grid-column: 1 / -1;
        border-top: 1px dashed #ccc;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }

    .btn-cetak-pdf-subdis {
        font-weight: 500;
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-cetak-pdf-subdis:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .piket-info-card {
        border-left: 4px solid #17a2b8;
        margin-bottom: 1.5rem;
    }

    .piket-info-card .card-title {
        color: #17a2b8;
        font-size: 1rem;
        font-weight: 600;
    }

    .piket-info-card ul {
        list-style-type: none;
        padding-left: 0;
        font-size: 0.875rem;
    }

    .piket-info-card ul li strong {
        min-width: 80px;
        display: inline-block;
    }

    .date-filter-display {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
        text-align: center;
        font-style: italic;
    }

    .filter-container-show-subdis {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .filter-container-show-subdis .form-control,
    .filter-container-show-subdis .btn-group .btn {
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header d-flex justify-content-between align-items-center">
                {{-- Title is now passed as $title from controller --}}
                <h5 class="mb-0">{{ $title }}</h5>
                <a href="{{ route('rekap-apel.index', ['date' => $filterDate, 'type' => $filterType]) }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Rekap Apel
                </a>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="filter-container-show-subdis card card-body">
            <form method="GET" action="{{ route('rekap-apel.subdis', ['id' => $subdis->id]) }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="date_filter_show_subdis" class="form-label">Tanggal</label>
                        <input type="date" class="form-control form-control-sm" id="date_filter_show_subdis" name="date"
                            value="{{ $filterDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="type_filter_show_subdis" class="form-label">Jenis Apel</label>
                        <select class="form-select form-select-sm" id="type_filter_show_subdis" name="type">
                            <option value="pagi" {{ $filterType=='pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="sore" {{ $filterType=='sore' ? 'selected' : '' }}>Sore</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i>
                            Tampilkan</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('rekap-apel.subdis.pdf', ['id' => $subdis->id, 'date' => $filterDate, 'type' => $filterType]) }}"
                            class="btn btn-sm btn-cetak-pdf-subdis w-100" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <p class="date-filter-display">Menampilkan data untuk Apel {{ ucfirst($filterType) }} pada tanggal {{
            \Carbon\Carbon::parse($filterDate)->translatedFormat('l, d F Y') }}</p>


        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            Daftar Personel Subdis: {{ $subdis->name }}
                            <small class="text-muted fw-normal">(Total: {{ $subdis->personil_count }})</small>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0 datatable-show-subdis">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Personel</th>
                                        <th>Pangkat</th>
                                        <th>Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($subdis->users->count() > 0)
                                    @foreach($subdis->users as $personel)
                                    @php
                                    $keteranganName = 'Belum Ada Sesi';
                                    if ($apelSessionInstance) {
                                    $attendanceRecord = $apelSessionInstance->attendances->where('user_id',
                                    $personel->id)->first();
                                    if ($attendanceRecord && $attendanceRecord->keterangan) {
                                    $keteranganName = $attendanceRecord->keterangan->name;
                                    } elseif($attendanceRecord) {
                                    $keteranganName = '-';
                                    } else {
                                    $keteranganName = 'Belum Diisi';
                                    }
                                    }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $personel->name }}</td>
                                        <td>{{ $personel->biodata?->pangkat?->name ?? '-' }}</td>
                                        <td>{{ $keteranganName }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4" class="text-center"><i>Tidak ada personil di subdis ini.</i>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="keterangan-summary-card">
                    <h6>Keterangan Total Subdis: {{ $subdis->name }}</h6>
                    <div class="keterangan-summary-grid">
                        @php $hadirCountFromSummary = 0; @endphp
                        @foreach($keteranganTotalsSubdis as $name => $count)
                        @if(strtolower($name) === 'hadir') @php $hadirCountFromSummary = $count; @endphp @endif
                        <div class="keterangan-summary-item">
                            <span class="keterangan-name">{{ $name }}:</span>
                            <span class="keterangan-count">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="keterangan-totals-separator"></div>
                    <div class="keterangan-summary-grid mt-3">
                        <div class="keterangan-summary-item fw-bold">
                            <span class="keterangan-name">Jumlah (Direkap):</span>
                            <span class="keterangan-count">{{ $totalDirekapSubdis }}</span>
                        </div>
                        <div class="keterangan-summary-item">
                            <span class="keterangan-name">Kurang (Belum Direkap):</span>
                            <span class="keterangan-count">{{ $totalKurangSubdis }}</span>
                        </div>
                        <div class="keterangan-summary-item">
                            <span class="keterangan-name">Total Personel Terdata:</span>
                            <span class="keterangan-count">{{ $totalPersonilSubdis }}</span>
                        </div>
                        <div class="keterangan-summary-item"
                            style="grid-column: 1 / -1; justify-content: left; font-weight: bold; padding-top: 0.75rem;">
                            <span class="keterangan-name">Hadir Aktual:</span>
                            <span class="keterangan-count">{{ $keteranganTotalsSubdis['Hadir'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                @if($piketHariIni)
                <div class="card piket-info-card mt-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-users-cog me-1"></i> Petugas Piket Tanggal {{
                            \Carbon\Carbon::parse($filterDate)->translatedFormat('d M Y') }}</h6>
                        <ul>
                            <li><strong>Pa Jaga:</strong> {{ $piketHariIni->pajaga?->name ?? 'N/A' }}</li>
                            <li><strong>Ba Jaga I:</strong> {{ $piketHariIni->bajagaFirst?->name ?? 'N/A' }}</li>
                            <li><strong>Jaga Tariat:</strong> {{ $piketHariIni->bajagaSecond?->name ?? 'N/A' }}</li>
                        </ul>
                    </div>
                </div>
                @else
                <div class="alert alert-light text-center mt-4">Data Piket untuk tanggal {{
                    \Carbon\Carbon::parse($filterDate)->translatedFormat('d M Y') }} tidak ditemukan.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    if ($('.select2-filter').length) {
        $('.select2-filter').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $(this).data('placeholder'), // Useful if you set data-placeholder attribute
            allowClear: true
        });
    }

    if (!$.fn.DataTable.isDataTable('.datatable-show-subdis')) {
        $('.datatable-show-subdis').DataTable({
            ordering: false, // Move ordering to the top-left
            bFilter: true, // Enable search filter
            autoWidth: false,
            sDom: "fBtlpi",
            columnDefs: [
                {
                    targets: "no-sort",
                    orderable: false,
                },
            ],
            language: {
                search: "Search: ", // Add label for search input
                sLengthMenu: "_MENU_",
                paginate: {
                    next: 'Next <i class=" fa fa-angle-double-right ms-2"></i>',
                    previous:
                        '<i class="fa fa-angle-double-left me-2"></i> Previous',
                },
                info: "Showing _START_ to _END_ of _TOTAL_ entries", // Add info at the bottom-left
            },
            initComplete: (settings, json) => {
                $(".dataTables_filter").appendTo("#tableSearch");
                $(".dataTables_filter").appendTo(".search-input");
            },
        });
    }
});
</script>
@endpush