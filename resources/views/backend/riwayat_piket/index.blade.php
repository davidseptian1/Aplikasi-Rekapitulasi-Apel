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

    .filter-section {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .user-info img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 8px;
        object-fit: cover;
    }

    .user-info .user-name {
        font-weight: 500;
    }

    .user-info .user-pangkat {
        font-size: 0.8rem;
        color: #6c757d;
        display: block;
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
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="filter-section card card-body shadow-sm">
            <form method="GET" action="{{ route('riwayat.piket.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="date_filter" class="form-label">Pilih Tanggal:</label>
                        <input type="date" class="form-control form-control-sm" id="date_filter" name="date"
                            value="{{ $filterDate }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">Daftar Sesi Piket untuk Tanggal: {{
                            \Carbon\Carbon::parse($filterDate)->translatedFormat('l, d F Y') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0 datatable-riwayat-piket">
                                <thead>
                                    <tr>
                                        <th>Tanggal Piket</th>
                                        <th>Pa Jaga</th>
                                        <th>Ba Jaga I</th>
                                        <th>Ba Jaga II (Tariat)</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Waktu Dibuat</th>
                                        {{-- Add Aksi column if needed --}}
                                        {{-- <th class="text-center">Aksi</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pikets as $piket)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($piket->piket_date)->translatedFormat('d M Y') }}
                                        </td>
                                        <td>
                                            @if($piket->pajaga)
                                            <div class="user-info d-flex align-items-center">
                                                <img src="{{ $piket->pajaga->photo_url }}"
                                                    alt="{{ $piket->pajaga->name }}">
                                                <div>
                                                    <span class="user-name">{{ $piket->pajaga->name }}</span>
                                                    <span class="user-pangkat">{{
                                                        $piket->pajaga->biodata?->pangkat?->name ?? '' }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <span class="text-muted">- Tidak Ditugaskan -</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($piket->bajagaFirst)
                                            <div class="user-info d-flex align-items-center">
                                                <img src="{{ $piket->bajagaFirst->photo_url }}"
                                                    alt="{{ $piket->bajagaFirst->name }}">
                                                <div>
                                                    <span class="user-name">{{ $piket->bajagaFirst->name }}</span>
                                                    <span class="user-pangkat">{{
                                                        $piket->bajagaFirst->biodata?->pangkat?->name ?? '' }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <span class="text-muted">- Tidak Ditugaskan -</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($piket->bajagaSecond)
                                            <div class="user-info d-flex align-items-center">
                                                <img src="{{ $piket->bajagaSecond->photo_url }}"
                                                    alt="{{ $piket->bajagaSecond->name }}">
                                                <div>
                                                    <span class="user-name">{{ $piket->bajagaSecond->name }}</span>
                                                    <span class="user-pangkat">{{
                                                        $piket->bajagaSecond->biodata?->pangkat?->name ?? '' }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <span class="text-muted">- Tidak Ditugaskan -</span>
                                            @endif
                                        </td>
                                        <td>{{ $piket->creator?->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($piket->created_at)->translatedFormat('d M Y, H:i')
                                            }}</td>
                                        {{-- <td class="text-center">
                                            <button class="btn btn-sm btn-outline-info" title="Detail"><i
                                                    class="fas fa-eye"></i></button>
                                        </td> --}}
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Tidak ada data piket ditemukan untuk tanggal {{
                                            \Carbon\Carbon::parse($filterDate)->translatedFormat('d F Y') }}.
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
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    // Initialize DataTables
    if (!$.fn.DataTable.isDataTable('.datatable-riwayat-piket')) {
        $('.datatable-riwayat-piket').DataTable({
            ordering: false, // Move ordering to the top-left
            bFilter: false, // Enable search filter
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

    // The form submission will handle the date filter reload.
    // If you want the URL to update without full form submission on date change:
    // $('#date_filter').on('change', function() {
    //     const selectedDate = $(this).val();
    //     if (selectedDate) {
    //         window.location.href = `{{ route('riwayat.piket.index') }}?date=${selectedDate}`;
    //     }
    // });
});
</script>
@endpush