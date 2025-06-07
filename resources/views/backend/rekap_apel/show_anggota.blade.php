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

    .subtitle {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: -0.5rem;
        margin-bottom: 1rem;
    }

    .table-controls {
        /* Container for search and filter */
        margin-bottom: 1rem;
    }

    .table-controls .form-control,
    .table-controls .form-select {
        font-size: 0.875rem;
        border-radius: 0.375rem;
        /* Softer radius */
    }

    .search-input {
        /* Custom search input styling */
        max-width: 250px;
        /* Or adjust as needed */
        height: calc(1.5em + .75rem + 2px);
        /* Match Bootstrap's default input height */
        padding: .375rem .75rem;
    }

    #filterByPangkatSelect {
        /* Specific ID for pangkat filter */
        max-width: 200px;
        /* Or adjust as needed */
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
    }


    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.9rem;
        vertical-align: middle;
        text-align: left;
        /* Default left align for headers */
    }

    .table thead th.text-center {
        /* For checkbox and action column header */
        text-align: center;
    }


    .table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
        text-align: left;
        /* Default left align for data */
    }

    .table tbody td.text-center {
        /* For checkbox and action column data */
        text-align: center;
    }


    .table .form-check-input {
        margin-top: 0.1rem;
        margin-left: auto;
        /* For centering in a text-center cell */
        margin-right: auto;
    }

    .btn-ubah {
        background-color: #e9f4ff;
        color: #007bff;
        border: 1px solid #c6e0ff;
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 0.375rem;
    }

    .btn-ubah:hover {
        background-color: #d1e7ff;
        color: #0056b3;
    }

    .bottom-actions {
        padding-top: 1rem;
        border-top: 1px solid #dee2e6;
        margin-top: 1rem;
    }

    .bottom-actions .btn {
        font-weight: 500;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        /* Standard button padding */
    }

    .action-explanation {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.75rem;
    }

    .badge-keterangan {
        /* For displaying selected keterangan */
        background-color: #e9ecef;
        /* Light grey for subtlety */
        color: #495057;
        border: 1px solid #ced4da;
        font-weight: normal;
        padding: 0.3em 0.6em;
    }

    .filter-container {
        /* For date/type filters */
        margin-bottom: 1.5rem;
    }

    .filter-container .form-control,
    .filter-container .form-select,
    .filter-container .btn-group .btn {
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .filter-container .btn-group .btn.active {
        background-color: #007bff;
        color: white;
    }

    .filter-container .btn-group .btn:not(.active) {
        /* Style for non-active Pagi/Sore buttons */
        background-color: #f8f9fa;
        color: #007bff;
        border-color: #dee2e6;
    }


    .header-subdis-name {
        font-size: 1.2rem;
        color: #495057;
        font-weight: 500;
        margin-left: 0.5rem;
    }

    .bulk-keterangan-section {
        margin-bottom: 1.5rem;
        /* Increased margin */
        padding: 1rem 1.5rem;
        /* Increased padding */
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        /* Softer radius */
        border: 1px solid #e9ecef;
    }

    .status-badge {
        font-size: 0.8em;
        padding: 0.4em 0.7em;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3em 0.7em;
        /* Adjust as per image_1 pagination style */
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 0.5em;
        /* Align with pagination */
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Rekap Personel
                        <span class="header-subdis-name"> - {{ $subdis->name }} ({{
                            \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }} - {{ ucfirst($type) }})</span>
                    </h5>
                    <p class="subtitle">Personel Aktif</p>
                </div>
                <a href="{{ route('rekap-apel.index', ['date' => $date, 'type' => $type]) }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        @include('backend.partials.alert')

        {{-- ============================================================== --}}
        {{-- START: TAMBAHKAN BLOK INI --}}
        {{-- ============================================================== --}}
        @if(Auth::user()->role === 'pokmin')
        <div class="alert {{ $pokminCanRekap ? 'alert-success' : 'alert-warning' }} text-center">
            <i class="fas {{ $pokminCanRekap ? 'fa-check-circle' : 'fa-info-circle' }} me-1"></i>
            {{ $rekapTimeMessage }}
        </div>
        @endif
        {{-- ============================================================== --}}
        {{-- END: TAMBAHKAN BLOK INI --}}

        <div class="filter-container card card-body shadow-sm mb-4">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" class="form-control" id="apel-date" value="{{ $date }}">
                    </div>
                </div>
                {{-- <div class="col-md-4 mb-2 mb-md-0">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('rekap-apel.anggota', ['id' => $subdis->id, 'type' => 'pagi', 'date' => $date]) }}"
                            class="btn btn-outline-primary {{ $type == 'pagi' ? 'active' : '' }}">
                            <i class="fas fa-sun me-1"></i> Apel Pagi
                        </a>
                        <a href="{{ route('rekap-apel.anggota', ['id' => $subdis->id, 'type' => 'sore', 'date' => $date]) }}"
                            class="btn btn-outline-primary {{ $type == 'sore' ? 'active' : '' }}">
                            <i class="fas fa-moon me-1"></i> Apel Sore
                        </a>
                    </div>
                </div> --}}
                <div class="col-md-8 text-md-end">
                    <button class="btn btn-primary" type="button" id="refresh-date-page" title="Refresh Data">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 text-primary">Sesi Apel ID: {{ $apelSession->id }}</h6>
                        </div>
                        <div>
                            @php
                            $attendancesCollection =
                            collect($anggotas)->pluck('apelAttendances')->flatten()->filter(fn($att) => $att &&
                            $att->apel_session_id == $apelSession->id);
                            $statusCounts = $attendancesCollection->countBy('status');
                            $draftCount = $statusCounts->get('draft', 0);
                            $submittedCount = $statusCounts->get('submitted', 0);
                            $verifiedCount = $statusCounts->get('verified', 0);
                            $doneCount = $statusCounts->get('done', 0);
                            $totalAnggotaCount = $anggotas->count();
                            $recordedAttendancesCount = $attendancesCollection->count();
                            $allAnggotaAccountedFor = ($recordedAttendancesCount >= $totalAnggotaCount);

                            $overallStatus = 'Belum Ada Data'; // Default
                            if ($totalAnggotaCount == 0) { $overallStatus = 'Tidak Ada Anggota'; }
                            elseif ($allAnggotaAccountedFor && $doneCount == $totalAnggotaCount && $totalAnggotaCount >
                            0) { $overallStatus = 'done'; }
                            elseif ($allAnggotaAccountedFor && $verifiedCount == $totalAnggotaCount &&
                            $totalAnggotaCount > 0 && $submittedCount == 0 && $draftCount == 0) { $overallStatus =
                            'verified'; } // More specific: all verified
                            elseif ($submittedCount > 0 && $draftCount == 0 && $allAnggotaAccountedFor) { $overallStatus
                            = 'submitted'; } // More specific: all submitted/verified/done, no drafts
                            elseif ($submittedCount > 0 ) { $overallStatus = 'submitted'; } // General if any submitted
                            elseif ($draftCount > 0 || !$allAnggotaAccountedFor) { $overallStatus = 'draft'; }
                            elseif ($recordedAttendancesCount == 0 && $totalAnggotaCount > 0) { $overallStatus =
                            'draft';}

                            $allowPokminActions = Auth::user()->role === 'pokmin' && Auth::user()->id ==
                            $subdis->user_id;
                            $allowSuperadminActions = Auth::user()->role === 'superadmin';
                            $allowPiketActions = Auth::user()->role === 'piket';

                            $isSessionDone = $overallStatus === 'done';
                            $canPokminManageDraftsBasedOnTime = $allowPokminActions && $pokminCanRekap;

                            $canManageDrafts = ($canPokminManageDraftsBasedOnTime || $allowSuperadminActions ||
                            $allowPiketActions) && !$isSessionDone;

                            $canPokminOrSuperadminManageDrafts = (($allowPokminActions && $pokminCanRekap &&
                            ($overallStatus === 'draft' ||
                            $overallStatus === 'Belum Ada Data' || $overallStatus === 'submitted')) ||
                            $allowSuperadminActions) && !$isSessionDone;

                            $canPiketVerify = ($allowPiketActions || $allowSuperadminActions) && $submittedCount > 0 &&
                            !$isSessionDone && $overallStatus !== 'verified';

                            $hasAnyDrafts = $draftCount > 0 || ($recordedAttendancesCount < $totalAnggotaCount &&
                                $totalAnggotaCount> 0);

                                @endphp

                                <span class="badge status-badge bg-secondary me-1">{{ $totalAnggotaCount }}
                                    Anggota</span>
                                @if($draftCount > 0)<span class="badge status-badge bg-warning text-dark me-1"
                                    title="Jumlah data masih draft">{{ $draftCount }} Draft</span>@endif
                                @if($submittedCount > 0)<span class="badge status-badge bg-info text-dark me-1"
                                    title="Jumlah data menunggu verifikasi">{{ $submittedCount }} Terkirim</span>@endif
                                @if($verifiedCount > 0)<span class="badge status-badge bg-success me-1"
                                    title="Jumlah data terverifikasi">{{ $verifiedCount }} Terverifikasi</span>@endif
                                @if($doneCount > 0)<span class="badge status-badge bg-dark"
                                    title="Jumlah data selesai">{{ $doneCount }} Selesai</span>@endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-controls d-flex justify-content-between align-items-center mb-3">
                            <div>{{-- Placeholder for left controls if any --}}</div>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control search-input me-2" id="tableSearchInput"
                                    placeholder="Search Nama Personel...">
                                @php
                                $pangkatNamesForDropdown =
                                $anggotas->pluck('biodata.pangkat.name')->filter()->unique()->sort();
                                @endphp
                                <select class="form-select sort-select" id="filterByPangkatSelect">
                                    <option value="">Filter by Pangkat</option>
                                    @foreach($pangkatNamesForDropdown as $pangkatName)
                                    <option value="{{ $pangkatName }}">{{ $pangkatName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0 && !$isSessionDone &&
                        ($overallStatus === 'draft' || $overallStatus === 'Belum Ada Data'))
                        <div class="bulk-keterangan-section">
                            <div class="row align-items-center">
                                <div class="col-md-auto"><label for="bulkKeteranganId"
                                        class="form-label fw-bold mb-0">Keterangan Massal:</label></div>
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm select2-bulk" name="keterangan_id_bulk"
                                        id="bulkKeteranganId">
                                        <option value="">-- Pilih & Terapkan ke Terpilih --</option>
                                        @foreach($keterangans as $ket)
                                        <option value="{{ $ket->id }}">{{ $ket->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md"><small class="form-text text-muted">Pilih anggota di tabel, lalu
                                        pilih keterangan di sini untuk diterapkan otomatis (status akan menjadi
                                        draft).</small></div>
                            </div>
                        </div>
                        @endif

                        @if($canPiketVerify)
                        <div class="mb-3 text-end">
                            <button type="button" class="btn btn-success" id="verifyAllSubmitted">
                                <i class="fas fa-check-double me-1"></i> Verifikasi Semua Terkirim ({{ $submittedCount
                                }})
                            </button>
                        </div>
                        @endif

                        @if($isSessionDone)
                        <div class="alert alert-info text-center"><i class="fas fa-info-circle me-1"></i> Sesi apel ini
                            telah selesai dan tidak dapat diubah lagi.</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0 datatable-anggota">
                                <thead>
                                    <tr>
                                        @if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0 &&
                                        !$isSessionDone)
                                        <th width="5%" class="text-center">
                                            <input class="form-check-input" type="checkbox" id="selectAll"
                                                title="Pilih Semua">
                                        </th>
                                        @endif
                                        <th>Nama</th>
                                        <th>Pangkat</th>
                                        <th>Jabatan</th>
                                        <th>Ubah Status Kehadiran</th>
                                        <th>Status</th>
                                        @if(!$isSessionDone)<th class="text-center">Ubah Status Kehadiran</th>@endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($anggotas as $anggota)
                                    @php
                                    $attendance = $anggota->apelAttendances->first();
                                    $currentKeterangan = $attendance ? $attendance->keterangan : null;
                                    $currentStatus = $attendance ? $attendance->status : 'belum_diisi';

                                    // Logika untuk tombol "Ubah" individual
                                    $canEditKeteranganIndividually = false;
                                    $canEditKeteranganIndividually = false;
                                    if ($isSessionDone) {
                                    $canEditKeteranganIndividually = false;
                                    } elseif ($allowPiketActions || $allowSuperadminActions) {
                                    $canEditKeteranganIndividually = true;
                                    } elseif ($allowPokminActions && $pokminCanRekap && !in_array($currentStatus,
                                    ['verified', 'done','submitted'])) {
                                    // POKMIN hanya bisa edit JIKA waktu rekap aktif DAN status belum final
                                    $canEditKeteranganIndividually = true;
                                    }

                                    // Logika untuk checkbox
                                    $checkboxDisabled = $isSessionDone;
                                    if ($allowPokminActions) {
                                    if (!$pokminCanRekap || in_array($currentStatus, ['submitted', 'verified', 'done']))
                                    {
                                    $checkboxDisabled = true;
                                    }
                                    }

                                    @endphp
                                    <tr>
                                        @if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0 &&
                                        !$isSessionDone)
                                        <td class="text-center">
                                            <input class="form-check-input user-checkbox" type="checkbox"
                                                value="{{ $anggota->id }}" data-current-status="{{ $currentStatus }}" {{
                                                $checkboxDisabled ? 'disabled' : '' }}>
                                        </td>
                                        @endif
                                        <td>{{ $anggota->name }}</td>
                                        <td>{{ $anggota->biodata?->pangkat?->name ?? '-' }}</td>
                                        <td>{{ $anggota->biodata?->jabatan?->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-keterangan">
                                                @if($currentKeterangan)
                                                {{ $currentKeterangan->name }}
                                                @else
                                                Hadir {{-- Default display as per image_1 --}}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($currentStatus === 'verified') <span
                                                class="badge bg-success">Terverifikasi</span>
                                            @elseif($currentStatus === 'submitted') <span
                                                class="badge bg-info text-dark">Terkirim</span>
                                            @elseif($currentStatus === 'draft') <span
                                                class="badge bg-warning text-dark">Draft</span>
                                            @elseif($currentStatus === 'done') <span
                                                class="badge bg-dark">Selesai</span>
                                            @else <span class="badge bg-light text-dark border">Belum Diisi</span>
                                            @endif
                                        </td>
                                        @if(!$isSessionDone)
                                        <td class="text-center">
                                            @if($canEditKeteranganIndividually)
                                            <button class="btn btn-sm btn-ubah btn-keterangan"
                                                data-user-id="{{ $anggota->id }}" data-user-name="{{ $anggota->name }}"
                                                data-session-id="{{ $apelSession->id }}"
                                                data-keterangan-id="{{ $currentKeterangan ? $currentKeterangan->id : '' }}"
                                                title="Ubah Keterangan Individual">
                                                Ubah <i class="fas fa-edit ms-1"></i></button>
                                            @else <button class="btn btn-sm btn-secondary" disabled
                                                title="Status saat ini tidak memperbolehkan perubahan oleh Anda">Ubah</button>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ ($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0 ? 7 : 6) }}"
                                            class="text-center py-4">Tidak ada data anggota personil.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0 && !$isSessionDone)
                        <div class="bottom-actions d-flex justify-content-between align-items-center">
                            <div><span class="table-info-display"></span></div>
                            <div class="d-flex gap-2">
                                <!--<button type="button" class="btn btn-outline-primary" id="submitSelectedDataBottom"
                                    title="Kirim data draft yang dipilih ke Piket">
                                    <i class="fas fa-paper-plane me-1"></i> Kirim Data Terpilih
                                </button> -->
                                @if($allowPokminActions || $allowSuperadminActions) {{-- Only Pokmin/Superadmin can
                                submit all drafts --}}
                                <button type="button" class="btn btn-primary" id="submitAllDraftsButton"
                                    title="Kirim semua data yang masih draft pada sesi ini ke Piket">
                                    <i class="fas fa-tasks me-1"></i> Kirim Semua Draft
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="action-explanation text-center">
                            <p class="mb-0"><strong>Keterangan Massal</strong>: Pilih anggota, lalu pilih keterangan di
                                atas untuk diterapkan (status akan menjadi draft).</p>
                            <p class="mb-0"><strong>Kirim Data Terpilih</strong>: Mengirim data draft yang Anda pilih
                                (centang) ke Piket.</p>
                            <p><strong>Kirim Semua Draft</strong>: Mengirim seluruh data yang masih berstatus draft dan
                                valid pada sesi ini ke Piket.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(!$isSessionDone && ($allowPokminActions || $allowPiketActions || $allowSuperadminActions))
<div class="modal fade" id="keteranganModal" tabindex="-1" aria-labelledby="keteranganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keteranganModalLabel">Ubah Keterangan untuk <span id="modalUserName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="keteranganForm"> @csrf
                    <input type="hidden" name="apel_session_id" id="modalApelSessionId">
                    <input type="hidden" name="user_id" id="modalUserId">
                    <div class="form-group">
                        <label for="modalKeteranganId" class="form-label">Keterangan</label>
                        <select class="form-select select2-modal" name="keterangan_id" id="modalKeteranganId" required
                            style="width: 100%;">
                            <option value="">Pilih Keterangan</option>
                            @foreach($keterangans as $ket)
                            <option value="{{ $ket->id }}">{{ $ket->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-3" id="alasanContainer" style="display:none;">
                        <label for="alasanText" class="form-label">Alasan (Opsional)</label>
                        <textarea name="alasan" id="alasanText" class="form-control" rows="3"
                            placeholder="Masukkan alasan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveKeterangan">Simpan Keterangan</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const selectKeterangan = document.getElementById('modalKeteranganId');
    const alasanContainer = document.getElementById('alasanContainer');

    // Ganti dengan ID yang sesuai keterangan "Izin"
    const izinId = "{{ $keterangans->firstWhere('name', 'Izin')->id ?? '' }}";

    console.log("ID Keterangan Izin:", izinId);

    function toggleAlasan() {
        if (selectKeterangan.value === izinId) {
            alasanContainer.style.display = 'block';
        } else {
            alasanContainer.style.display = 'none';
            document.getElementById('alasanText').value = '';
        }
    }

    selectKeterangan.addEventListener('change', toggleAlasan);

    const modal = document.getElementById('keteranganModal');
    modal.addEventListener('shown.bs.modal', function () {
        toggleAlasan();
    });
});
</script>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    var anggotaTable;
    // DataTable Initialization
    if (!$.fn.DataTable.isDataTable('.datatable-anggota')) {
        anggotaTable = $('.datatable-anggota').DataTable({
            "paging": true, "lengthChange": false, "searching": true, // Custom search handled separately
            "ordering": true, "info": false, // Using custom info display
            "autoWidth": false, "responsive": true, "pageLength": 8, // As per image
            "language": { "paginate": { "previous": "&lt;", "next": "&gt;" } },
            "columnDefs": [ { "orderable": false, "targets": [
                @if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0) 0, @endif // Checkbox
                (@if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0) 4 @else 3 @endif), // Keterangan col index
                (@if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0) 5 @else 4 @endif), // Status col index
                @if(!$isSessionDone) (@if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0) 6 @else 5 @endif) @endif // Aksi col index
            ] } ],
            "drawCallback": function( settings ) {
                var api = this.api(); var pageInfo = api.page.info();
                var infoText = 'Menampilkan ' + (pageInfo.start + 1) + ' sampai ' + pageInfo.end + ' dari ' + pageInfo.recordsTotal + ' entri';
                if (pageInfo.recordsTotal === 0) { infoText = "Tidak ada data"; }
                $('.table-info-display').html(infoText);
            },
            "dom": 'rt<"d-flex justify-content-between align-items-center mt-3"<"table-info-display col-md-6"><"col-md-6 d-flex justify-content-end"p>>'
        });
         if(anggotaTable) anggotaTable.draw(); // Initial draw for info
    }

    // Custom Search Input
    $('#tableSearchInput').on('keyup', function(){
        if (anggotaTable) { anggotaTable.search($(this).val()).draw(); }
    });

    // Filter by Pangkat Select
    $('#filterByPangkatSelect').on('change', function(){
        if (anggotaTable) {
            let pangkatColumnIndex = @if($canPokminOrSuperadminManageDrafts && $totalAnggotaCount > 0) 2 @else 1 @endif; // 0:chk, 1:Nama, 2:Pangkat OR 0:Nama, 1:Pangkat
            anggotaTable.column(pangkatColumnIndex).search(this.value ? '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$' : '', true, false).draw();
        }
    });

    // Initialize Select2
    if ($('#keteranganModal').length > 0 && $('.select2-modal').length > 0) { $('.select2-modal').select2({ dropdownParent: $('#keteranganModal'), }); }
    if ($('.select2-bulk').length > 0) { $('.select2-bulk').select2({}); }

    // Date and Type Filter navigation
    function navigateWithFilters() {
        const selectedDate = $('#apel-date').val();
        let activeType = '{{ $type }}';
        $('.filter-container .btn-group .btn.active').each(function() {
             if($(this).text().trim().toLowerCase().includes('pagi')) activeType = 'pagi';
             if($(this).text().trim().toLowerCase().includes('sore')) activeType = 'sore';
        });
        if ($(this).hasClass('btn-outline-primary') && !$(this).is('#apel-date') && !$(this).is('#refresh-date-page')) {
            activeType = $(this).text().trim().toLowerCase().includes('pagi') ? 'pagi' : 'sore';
        }
        window.location.href = `{{ route('rekap-apel.anggota', ['id' => $subdis->id]) }}?date=${selectedDate}&type=${activeType}`;
    }
    $('#apel-date').on('change', navigateWithFilters);
    $('.filter-container .btn-group .btn').on('click', function(e) {
        e.preventDefault(); $(this).addClass('active').siblings().removeClass('active');
        navigateWithFilters.call(this);
    });
    $('#refresh-date-page').on('click', function() {
        const today = new Date().toISOString().split('T')[0]; $('#apel-date').val(today);
        navigateWithFilters.call(this);
    });

    // Select All Checkbox
    $('#selectAll').on('change', function() { $('.user-checkbox:not(:disabled)').prop('checked', this.checked); });

    // Loading state helper functions
    function showLoading(button, text = 'Memproses...') { button.data('original-html', button.html()); button.prop('disabled', true).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${text}`); }
    function hideLoading(button) { if(button.data('original-html')) { button.prop('disabled', false).html(button.data('original-html')); } else { button.prop('disabled', false).html(text || 'Submit'); /* Fallback text */ } }

    // AUTOMATIC BULK KETERANGAN UPDATE
    $('#bulkKeteranganId').on('change', function() {
        const selectedKeteranganId = $(this).val();
        const selectedKeteranganText = $('#bulkKeteranganId option:selected').text();
        if (!selectedKeteranganId) return;

        const checkedBoxes = $('.user-checkbox:checked:not(:disabled)');
        if (checkedBoxes.length === 0) {
            alert('Pilih anggota terlebih dahulu untuk menerapkan keterangan massal.');
            $(this).val(''); if ($(this).hasClass('select2-bulk')) $(this).trigger('change.select2'); return;
        }
        if (!confirm(`Terapkan keterangan "${selectedKeteranganText}" ke ${checkedBoxes.length} anggota terpilih? Status akan diubah menjadi draft.`)) {
            $(this).val(''); if ($(this).hasClass('select2-bulk')) $(this).trigger('change.select2'); return;
        }
        const user_ids = checkedBoxes.map((_,el) => $(el).val()).get();
        const dummyButton = $('<button style="display:none;"></button>').appendTo('body'); showLoading(dummyButton);

        $.ajax({
            url: '{{ route("rekap-apel.update-keterangan-bulk") }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}', apel_session_id: '{{ $apelSession->id }}',
                    user_ids: user_ids, keterangan_id: selectedKeteranganId, action: 'draft' /* Controller expects action */ },
            success: function(r) { if(r.success) { alert(r.message); location.reload(); } else { alert(r.message || 'Gagal.'); }},
            error: function(xhr) { alert(xhr.responseJSON?.message || 'Error.'); },
            complete: function() {
                hideLoading(dummyButton); dummyButton.remove();
                $('#bulkKeteranganId').val(''); if ($('#bulkKeteranganId').hasClass('select2-bulk')) $('#bulkKeteranganId').trigger('change.select2');
            }
        });
    });

    // SUBMIT SELECTED DRAFTS ("Kirim Data Terpilih")
    $('#submitSelectedDataBottom').on('click', function() {
        const button = $(this);
        const checkedBoxes = $('.user-checkbox:checked:not(:disabled)').filter(function() {
            const status = $(this).data('current-status'); return status === 'draft' || status === 'belum_diisi';
        });
        if (checkedBoxes.length === 0) { alert('Pilih anggota berstatus draft/belum_diisi untuk dikirim.'); return; }
        if (!confirm(`Kirim data untuk ${checkedBoxes.length} anggota terpilih? Pastikan keterangan sudah benar (default "Hadir" jika belum diisi).`)) return;
        showLoading(button, 'Mengirim Terpilih...');
        const user_ids = checkedBoxes.map((_,el) => $(el).val()).get();

        let usersToPreUpdateKeterangan = [];
        const hadirKeteranganId = '{{ $keterangans->firstWhere("name", "Hadir")?->id ?? "" }}';
        checkedBoxes.each(function() {
            const currentStatus = $(this).data('current-status');
            const currentKeteranganBadgeText = $(this).closest('tr').find('.badge-keterangan').text().trim();
            // Apply 'Hadir' if status is 'belum_diisi' OR if current keterangan displayed is effectively 'Hadir' or 'Belum Diisi' (to ensure it's explicitly set)
            if ((currentStatus === 'belum_diisi' || currentKeteranganBadgeText === 'Hadir' || currentKeteranganBadgeText === 'Belum Diisi') && hadirKeteranganId) {
                usersToPreUpdateKeterangan.push($(this).val());
            }
        });

        let preUpdatePromise = Promise.resolve({success: true});
        if (usersToPreUpdateKeterangan.length > 0 && hadirKeteranganId) {
             preUpdatePromise = $.ajax({
                url: '{{ route("rekap-apel.update-keterangan-bulk") }}', type: 'POST',
                data: { _token: '{{ csrf_token() }}', apel_session_id: '{{ $apelSession->id }}',
                        user_ids: usersToPreUpdateKeterangan, keterangan_id: hadirKeteranganId, action: 'draft' }
            });
        }

        preUpdatePromise.then(function(updateResponse) {
            if (!updateResponse || (!updateResponse.success && usersToPreUpdateKeterangan.length > 0)) {
                alert(updateResponse?.message || 'Gagal mengatur default keterangan "Hadir" sebelum mengirim.');
                return Promise.reject('Pre-update failed for submit selected');
            }
            return $.ajax({ url: '{{ route("rekap-apel.submit-session", $apelSession->id) }}', type: 'POST', data: { _token: '{{ csrf_token() }}', user_ids: user_ids } });
        }).then(function(r) { if (r && r.success) { alert(r.message); location.reload(); } else if(r) { alert(r.message || `Gagal.`); }}
        ).catch(function(err) { if (err.responseText && err.status !== 0 && err !== 'Pre-update for submit selected failed' ) { alert(JSON.parse(err.responseText).message || 'Error.'); } else if (err !== 'Pre-update for submit selected failed') { console.error(err); alert('Terjadi kesalahan jaringan atau server.');} }
        ).always(function() { hideLoading(button); });
    });

    // SUBMIT ALL DRAFTS ("Kirim Semua Draft ke Piket")
    $('#submitAllDraftsButton').on('click', function() {
        if (!confirm('Kirim semua data draft yang valid (sudah ada keterangan) pada sesi ini ke Piket?')) return;
        const button = $(this); showLoading(button, 'Mengirim Semua...');
        $.ajax({
            url: '{{ route("rekap-apel.submit-session", $apelSession->id) }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}' }, // No user_ids, controller handles "all"
            success: function(r) { if(r.success) { alert(r.message); location.reload(); } else { alert(r.message || 'Gagal.'); }},
            error: function(xhr) { alert(xhr.responseJSON?.message || 'Error.'); },
            complete: function() { hideLoading(button); }
        });
    });

    // Verify All Submitted Button
    $('#verifyAllSubmitted').on('click', function() {
        if (!confirm('Verifikasi semua data terkirim untuk sesi ini?')) return;
        const button = $(this); showLoading(button, 'Memverifikasi...');
        $.ajax({
            url: '{{ route("rekap-apel.verify-session", $apelSession->id) }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(r) { if(r.success) { alert(r.message); location.reload(); } else { alert(r.message || 'Gagal.'); }},
            error: function(xhr) { alert(xhr.responseJSON?.message || 'Error.'); },
            complete: function() { hideLoading(button); }
        });
    });

    // Individual Keterangan Modal & Save
    $(document).on('click', '.btn-keterangan', function() {
        $('#modalUserId').val($(this).data('user-id')); $('#modalUserName').text($(this).data('user-name'));
        $('#modalApelSessionId').val($(this).data('session-id'));
        let currentKetId = $(this).data('keterangan-id');
        if (!currentKetId && $(this).closest('tr').find('.badge-keterangan').text().trim() === 'Hadir') {
            @php $hadirIdModal = $keterangans->firstWhere(fn($k) => strtolower($k->name) === 'hadir')?->id; @endphp
            currentKetId = '{{ $hadirIdModal }}' || '';
        }
        $('#modalKeteranganId').val(currentKetId).trigger('change'); $('#keteranganModal').modal('show');
    });

    $('#saveKeterangan').on('click', function() {
        const button = $(this); const form = $('#keteranganForm'); const userId = $('#modalUserId').val();
        if (!$('#modalKeteranganId').val()) { alert('Pilih keterangan.'); return; }
        showLoading(button, 'Menyimpan...');
        const url = '{{ route("rekap-apel.update-keterangan", ":id") }}'.replace(':id', userId);
        $.ajax({
            url: url, type: 'POST', data: form.serialize(),
            success: function(r) { if (r.success) { $('#keteranganModal').modal('hide'); alert(r.message); location.reload(); } else { alert(r.message || 'Gagal.'); }},
            error: function(xhr) { alert(xhr.responseJSON?.message || 'Error.'); },
            complete: function() { hideLoading(button); }
        });
    });

    $('#keteranganModal').on('hidden.bs.modal', function () {
        $('#keteranganForm')[0].reset();
        if ($('#modalKeteranganId').hasClass('select2-hidden-accessible')) { $('#modalKeteranganId').val(null).trigger('change'); }
        else { $('#modalKeteranganId').val(''); }
    });
});
</script>
@endpush