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
        /* Larger title for "Detail Rekap" */
        font-weight: 600;
    }

    .summary-card {
        border-radius: 0.75rem;
        /* More rounded corners for cards */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        /* Softer shadow */
        border: none;
        transition: all 0.3s ease;
        background-color: #fff;
        /* Ensure card background */
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .summary-card-icon-wrapper {
        background-color: rgba(0, 123, 255, 0.1);
        /* Default icon bg */
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-card-icon {
        font-size: 1.5rem;
        color: #007bff;
        /* Default icon color */
    }

    .summary-card-value {
        font-size: 1.75rem;
        /* Larger value text */
        font-weight: 700;
        color: #343a40;
        /* Darker text for value */
    }

    .summary-card-label {
        font-size: 0.875rem;
        color: #6c757d;
        /* Muted text for label */
        margin-bottom: 0.25rem;
    }

    .date-filter-section {
        background-color: #fff;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }

    .date-filter-section .form-label {
        margin-bottom: 0;
        font-weight: 500;
    }

    .date-filter-section .form-control,
    .date-filter-section .btn-group .btn {
        border-radius: 0.375rem;
        /* Softer radius for inputs/buttons */
        font-size: 0.875rem;
    }

    .date-filter-section .btn-group .btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .date-filter-section .btn-group .btn:not(.active) {
        background-color: #e9ecef;
        color: #495057;
        border-color: #ced4da;
    }


    .table thead th {
        background-color: #f8f9fa;
        /* Standard light header */
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.9rem;
        vertical-align: middle;
        text-align: center;
        /* Center align table headers */
    }

    .table thead th:first-child {
        text-align: left;
    }


    .table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
        text-align: center;
        /* Center align table data */
    }

    .table tbody td:first-child {
        text-align: left;
        /* Left align first column (Nama Subdis) */
    }


    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.75em;
        border-radius: 0.375rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    /* Badge Colors matching image_2.png and defined states */
    .badge-selesai {
        background-color: #28a745;
        color: white;
    }

    /* Green */
    .badge-terverifikasi {
        background-color: #007bff;
        color: white;
    }

    /* Primary Blue */
    .badge-terkirim {
        background-color: #17a2b8;
        color: white;
    }

    /* Cyan/Teal */
    .badge-sementara {
        background-color: #fd7e14;
        color: white;
    }

    /* Orange */
    .badge-tidak-ada-anggota {
        background-color: #e9ecef;
        color: #495057;
        border: 1px solid #ced4da;
    }

    .badge-belum-ada-data {
        background-color: #6c757d;
        color: white;
    }

    .badge-perlu-dicek {
        background-color: #dc3545;
        color: white;
    }

    /* Red */

    .btn-selesaikan {
        font-weight: 500;
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }

    .table .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .table .btn-outline-info {
        color: #17a2b8;
        border-color: #17a2b8;
    }

    .table .btn-outline-info:hover {
        background-color: #17a2b8;
        color: #fff;
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

        @php
        // These summary values are now calculated in the controller and passed via $subdis items
        // We'll re-calculate here for simplicity if not passed directly as summary vars
        // Or ideally, pass these summary counts directly from the controller.
        $cardTotalSementara = 0;
        $cardTotalSubdisTerkirimLanjut = 0;
        $cardTotalSubdisCount = $subdis->count();

        foreach ($subdis as $s_item) { // Use a different variable name to avoid conflict with $subdis from controller
        $session = $s_item->apelSessions->first();
        if ($session && isset($session->draft_attendances_count)) { // Check if count property exists
        $cardTotalSementara += $session->draft_attendances_count;
        }
        if (in_array($s_item->display_status_text, ['Terkirim', 'Terverifikasi', 'Selesai'])) {
        $cardTotalSubdisTerkirimLanjut++;
        }
        }
        @endphp

        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="summary-card-icon-wrapper me-3" style="background-color: rgba(253, 126, 20, 0.1);">
                            <i class="fas fa-hourglass-half summary-card-icon" style="color: #fd7e14;"></i>
                        </div>
                        <div>
                            <div class="summary-card-label">Total Rekap Sementara</div>
                            <div class="summary-card-value">{{ $cardTotalSementara }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="summary-card-icon-wrapper me-3" style="background-color: rgba(23, 162, 184, 0.1);">
                            <i class="fas fa-paper-plane summary-card-icon" style="color: #17a2b8;"></i>
                        </div>
                        <div>
                            <div class="summary-card-label">Total Rekap Terkirim/Lanjut</div>
                            <div class="summary-card-value">{{ $cardTotalSubdisTerkirimLanjut }}/{{
                                $cardTotalSubdisCount }} <small
                                    style="font-size:0.9rem; font-weight:normal;">Subdis</small></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="summary-card-icon-wrapper me-3" style="background-color: rgba(40, 167, 69, 0.1);">
                            <i class="fas fa-building summary-card-icon" style="color: #28a745;"></i>
                        </div>
                        <div>
                            <div class="summary-card-label">Total Subdis</div>
                            <div class="summary-card-value">{{ $cardTotalSubdisCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="date-filter-section d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                <label for="apel-date" class="form-label">Pilih Tanggal:</label>
                <input type="date" class="form-control" id="apel-date" value="{{ $date }}" style="max-width: 180px;">
                <div class="btn-group" role="group">
                    <a href="{{ route('rekap-apel.index', ['date' => $date, 'type' => 'pagi']) }}"
                        class="btn btn-outline-primary {{ $type == 'pagi' ? 'active' : '' }}">Pagi</a>
                    <a href="{{ route('rekap-apel.index', ['date' => $date, 'type' => 'sore']) }}"
                        class="btn btn-outline-primary {{ $type == 'sore' ? 'active' : '' }}">Sore</a>
                </div>
            </div>
            @if (Auth::user()->role === 'superadmin' || Auth::user()->role === 'piket')
            @if ($enableShareButtonGlobal) {{-- New flag from controller --}}
            <button id="bagikanRekap" class="btn btn-info btn-selesaikan" data-date="{{ $date }}"
                data-type="{{ $type }}" title="Bagikan rekap apel yang sudah selesai.">
                <i class="fas fa-share-alt me-1"></i> Bagikan
            </button>
            @elseif ($allDataCanBeMarkedAsDone) {{-- Use the renamed flag for clarity --}}
            <button id="markAsDone" class="btn btn-success btn-selesaikan" data-date="{{ $date }}"
                data-type="{{ $type }}" title="Selesaikan semua rekap apel terverifikasi untuk tanggal dan tipe ini.">
                <i class="fas fa-check-circle me-1"></i> Selesaikan
            </button>
            @else
            {{-- Button is disabled or not shown if neither condition is met --}}
            <button class="btn btn-secondary btn-selesaikan" disabled
                title="Semua subdis (yang memiliki anggota) harus memiliki sesi yang Terverifikasi atau sudah Selesai.">
                <i class="fas fa-check-circle me-1"></i> Selesaikan
            </button>
            @endif
            @endif
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>Nama Subdis</th>
                                        <th>Waktu Apel Dibuat</th>
                                        <th>Waktu Kegiatan</th>
                                        <th>Tanggal Sesi</th>
                                        <th>Status Sesi</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($subdis as $row)
                                    <tr>
                                        <td>
                                            <a href="{{ route('rekap-apel.anggota', ['id' => $row->id, 'date' => $date, 'type' => $type]) }}"
                                                class="fw-bold">
                                                {{ $row->name }}
                                            </a>
                                        </td>
                                        <td>{{ Str::ucfirst($type) }}</td> {{-- Waktu Kegiatan - Placeholder --}}
                                        <td>{{ $row->display_waktu_apel }}</td>
                                        <td>{{ $row->display_tanggal_apel }}</td>
                                        <td><span class="badge {{ $row->display_badge_class }}">{{
                                                $row->display_status_text }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('rekap-apel.subdis', ['id' => $row->id, 'date' => $date, 'type' => $type]) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="Lihat Detail Statistik Subdis">
                                                <i class="fas fa-chart-bar me-1"></i>Lihat Laporan
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Tidak ada data subdis yang ditemukan.
                                            @if(Auth::user()->role === 'pokmin' && $subdis->isEmpty() &&
                                            \App\Models\Subdis::count() > 0)
                                            <br><small>Anda belum ditugaskan sebagai penanggung jawab subdis, atau
                                                subdis Anda tidak memiliki jadwal apel untuk tanggal ini.</small>
                                            @elseif(\App\Models\Subdis::count() == 0)
                                            <br><small>Belum ada data subdis dalam sistem.</small>
                                            @endif
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
        if (!$.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable({
                "bFilter": true,
                "bInfo": true,
                "bLengthChange": true,
                "pagingType": "simple_numbers", // Matches image_2.png pagination
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari Subdis...",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_-_END_ dari _TOTAL_",
                    "infoEmpty": "Data tidak tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total entri)",
                    "zeroRecords": "Tidak ada data yang cocok",
                    "paginate": { "first": "<i class='fas fa-angle-double-left'></i>", "last": "<i class='fas fa-angle-double-right'></i>", "next": "<i class='fas fa-angle-right'></i>", "previous": "<i class='fas fa-angle-left'></i>" }
                },
                "columnDefs": [
                    { "orderable": false, "targets": [1, 2, 4, 5] } // Adjust non-sortable: Waktu Apel, Waktu Kegiatan, Status, Aksi
                ],
                 "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        }

        function navigateWithFilters() {
            const selectedDate = $('#apel-date').val();
            let activeType = '{{ $type }}'; // Default to current type
            $('.date-filter-section .btn-group .btn.active').each(function() {
                 if($(this).text().trim().toLowerCase() === 'pagi') activeType = 'pagi';
                 if($(this).text().trim().toLowerCase() === 'sore') activeType = 'sore';
            });
             // If the trigger was a type button, update activeType
            if ($(this).hasClass('btn-outline-primary') && !$(this).is('#apel-date')) {
                activeType = $(this).text().trim().toLowerCase() === 'pagi' ? 'pagi' : 'sore';
            }
            window.location.href = `{{ route('rekap-apel.index') }}?date=${selectedDate}&type=${activeType}`;
        }

        $('#apel-date').on('change', navigateWithFilters);
        $('.date-filter-section .btn-group .btn').on('click', function(e) {
            e.preventDefault();
            $(this).addClass('active').siblings().removeClass('active');
            navigateWithFilters.call(this); // Use call to set 'this' context correctly
        });


        $('#markAsDone').on('click', function() {
            if (!confirm('Apakah Anda yakin ingin menyelesaikan semua rekap apel yang telah terverifikasi untuk tanggal dan tipe ini? Data yang sudah diselesaikan tidak dapat diubah lagi.')) {
                return;
            }
            const button = $(this);
            const date = button.data('date');
            const type = button.data('type');
            const originalHtml = button.html();
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');

            $.ajax({
                url: '{{ route("rekap-apel.mark-as-done") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', date: date, type: type },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message || 'Gagal menyelesaikan rekap.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan.');
                },
                complete: function() {
                    button.prop('disabled', false).html(originalHtml);
                }
            });
        });

        $('#bagikanRekap').on('click', function() {
            const date = $(this).data('date');
            const type = $(this).data('type');
            // Format date as Y-m-d
            let formattedDate = date;
            if (date) {
                const d = new Date(date);
                const year = d.getFullYear();
                const month = ('0' + (d.getMonth() + 1)).slice(-2);
                const day = ('0' + d.getDate()).slice(-2);
                formattedDate = `${year}-${month}-${day}`;
            }
            // Validate type
            const validTypes = ['pagi', 'sore'];
            if (!validTypes.includes(type)) {
                alert('Tipe apel tidak valid.');
                return;
            }
            const url = `{{ route('rekap-apel.laporan-global') }}?date=${formattedDate}&type=${type}`;
            window.location.href = url; // Or open in new tab: window.open(url, '_blank');
        });
    });
</script>
@endpush