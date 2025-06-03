@extends('layouts.app-backend')

@push('styles')
<style>
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

    .table thead th:nth-child(2),
    .table thead th:nth-child(3) {
        text-align: left;
    }

    /* Subdis, Personel */
    .table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
    }

    .table tbody td:nth-child(1),
    .table tbody td:nth-child(4),
    .table tbody td:nth-child(5) {
        text-align: center;
    }

    /* No, Pangkat, Keterangan */
    .table tbody td:nth-child(2),
    .table tbody td:nth-child(3) {
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

    .btn-cetak-pdf-global {
        font-weight: 500;
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-cetak-pdf-global:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .piket-info-card {
        border-left: 4px solid #17a2b8;
        margin-bottom: 1.5rem;
    }

    .piket-info-card .card-title {
        color: #17a2b8;
    }

    .piket-info-card ul {
        list-style-type: none;
        padding-left: 0;
    }

    .piket-info-card ul li strong {
        min-width: 80px;
        display: inline-block;
    }

    .share-buttons-container {
        margin-top: 10px;
    }

    /* For WA and Email buttons */
    .share-buttons-container .btn {
        margin-right: 10px;
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $pages }}</h5>
                <a href="{{ route('rekap-apel.index', ['date' => $filterDate, 'type' => $filterType]) }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Rekap Apel
                </a>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="filter-bar card card-body">
            <form method="GET" action="{{ route('rekap-apel.laporan-global') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="date_filter" class="form-label">Tanggal</label>
                        <input type="date" class="form-control form-control-sm" id="date_filter" name="date"
                            value="{{ $filterDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="type_filter" class="form-label">Jenis Apel</label>
                        <select class="form-select form-select-sm" id="type_filter" name="type">
                            <option value="pagi" {{ $filterType=='pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="sore" {{ $filterType=='sore' ? 'selected' : '' }}>Sore</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="subdis_filter" class="form-label">Subdis</label>
                        <select class="form-select form-select-sm select2-filter" id="subdis_filter" name="subdis_id">
                            <option value="">-- Semua Subdis --</option>
                            @foreach($subdisListFilter as $subdis_item)
                            <option value="{{ $subdis_item->id }}" {{ $filterSubdisId==$subdis_item->id ? 'selected' :
                                '' }}>
                                {{ $subdis_item->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i>
                            Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            Laporan Rekap Apel {{ ucfirst($filterType) }}
                            Tanggal: {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('l, d F Y') }}
                            @if($filterSubdisId && $subdisData->first())
                            - Subdis: {{ $subdisData->first()->name }}
                            @else
                            - Semua Subdis
                            @endif
                        </h6>
                        <div class="share-buttons-container mt-0 text-md-end">
                            <a href="{{ route('rekap-apel.laporan-global.pdf', ['date' => $filterDate, 'type' => $filterType, 'subdis_id' => $filterSubdisId]) }}"
                                class="btn btn-sm btn-cetak-pdf-global" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($pesanUntukBagikan) }}"
                                target="_blank" class="btn btn-sm btn-success">
                                <i class="fab fa-whatsapp me-1"></i> Kirim WA
                            </a>
                            <a href="mailto:?subject={{ urlencode($subjectEmailBagikan) }}&body={{ urlencode($pesanUntukBagikan) }}"
                                class="btn btn-sm btn-warning text-dark">
                                <i class="fas fa-envelope me-1"></i> Kirim Email
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($subdisData->isEmpty())
                        <p class="text-center text-muted">Tidak ada data rekap apel untuk filter yang dipilih.</p>
                        @else
                        @foreach($subdisData as $sub)
                        @if($filterSubdisId || $sub->personil_count > 0) {{-- Show subdis if filtered or if it has
                        members --}}
                        <h5 class="mt-3 mb-2" style="font-size: 1.1rem; font-weight: 500;">Subdis: {{ $sub->name }}</h5>
                        @php $apelSession = $sub->apelSessions->first(); @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-3 datatable-laporan-global">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Personel</th>
                                        <th>Pangkat</th>
                                        <th>Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($apelSession && $apelSession->attendances->count() > 0)
                                    @foreach($sub->users as $personel) {{-- Iterate through all personnel of this subdis
                                    --}}
                                    @php
                                    $attendanceRecord = $apelSession->attendances->where('user_id',
                                    $personel->id)->first();
                                    $keteranganName = $attendanceRecord && $attendanceRecord->keterangan ?
                                    $attendanceRecord->keterangan->name : 'Belum Diisi';
                                    if (!$attendanceRecord && $apelSession) $keteranganName = 'Belum Diisi';
                                    if (!$apelSession && $sub->personil_count > 0) $keteranganName = 'Sesi Belum Ada';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $personel->name }}</td>
                                        <td>{{ $personel->biodata?->pangkat?->name ?? '-' }}</td>
                                        <td>{{ $keteranganName }}</td>
                                    </tr>
                                    @endforeach
                                    @elseif($sub->personil_count > 0)
                                    <tr>
                                        <td colspan="4" class="text-center"><i>Belum ada data rekap untuk sesi ini di
                                                subdis {{ $sub->name }}.</i></td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="4" class="text-center"><i>Tidak ada personil di subdis ini.</i>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @endif
                        @endforeach

                        {{-- Global Summary Card --}}
                        <div class="keterangan-summary-card mt-4">
                            <h6>Ringkasan Total Keterangan (@if($filterSubdisId && $subdisData->first()) {{
                                $subdisData->first()->name }} @else Semua Subdis @endif)</h6>
                            <div class="keterangan-summary-grid">
                                @php $grandTotalHadirFromSummary = 0; @endphp
                                @foreach($grandTotals as $name => $count)
                                @if(strtolower($name) === 'hadir') @php $grandTotalHadirFromSummary = $count; @endphp
                                @endif
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
                                    <span class="keterangan-count">{{ $totalDirekapKeseluruhan }}</span>
                                </div>
                                <div class="keterangan-summary-item">
                                    <span class="keterangan-name">Kurang (Belum Direkap):</span>
                                    <span class="keterangan-count">{{ $totalKurangKeseluruhan }}</span>
                                </div>
                                <div class="keterangan-summary-item">
                                    <span class="keterangan-name">Total Personel Terdata:</span>
                                    <span class="keterangan-count">{{ $totalPersonilKeseluruhan }}</span>
                                </div>
                                <div class="keterangan-summary-item"
                                    style="grid-column: 1 / -1; justify-content: left; font-weight: bold; padding-top: 0.75rem;">
                                    <span class="keterangan-name">Hadir Aktual:</span>
                                    <span class="keterangan-count">{{ $grandTotals['Hadir'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Piket Info --}}
                        @if($piketHariIni)
                        <div class="card piket-info-card mt-4">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-users-cog me-1"></i> Petugas Piket Tanggal {{
                                    \Carbon\Carbon::parse($filterDate)->translatedFormat('d M Y') }}</h6>
                                <ul>
                                    <li><strong>Pa Jaga:</strong> {{ $piketHariIni->pajaga?->name ?? 'N/A' }}</li>
                                    <li><strong>Ba Jaga I:</strong> {{ $piketHariIni->bajagaFirst?->name ?? 'N/A' }}
                                    </li>
                                    <li><strong>Jaga Tariat:</strong> {{ $piketHariIni->bajagaSecond?->name ?? 'N/A' }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-light text-center mt-4">Data Piket untuk tanggal {{
                            \Carbon\Carbon::parse($filterDate)->translatedFormat('d M Y') }} tidak ditemukan.</div>
                        @endif

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
    $(document).ready(function() {
    // Initialize Select2 for filters
    if ($('.select2-filter').length) {
        $('.select2-filter').select2({
            width: '100%',
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });
    }

    // Initialize DataTables for each personnel list if needed (can be many tables)
    // For simplicity, global DataTables init is removed as each subdis has its own table
    // If you want DataTables for EACH subdis table:
    $('.datatable-laporan-global').each(function() {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                "paging": true, // Enable pagination
                "lengthChange": false,
                "searching": false, // Global search is better if one big table
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "dom": '<"row"<"col-sm-12"tr>><"row"<"col-sm-12 d-flex justify-content-end"p>>',
                "columnDefs": [
                    { "orderable": false, "targets": [0,3] } // No, Keterangan
                ]
            });
        }
    });
});
</script>
@endpush