@extends('layouts.app-backend')

@push('styles')
<style>
    /* General Dashboard Styles (can be moved to a global dashboard.css) */
    .page-wrapper .content {
        padding: 20px;
    }

    /* Adjust overall padding */
    .dashboard-welcome-card {
        /* Using a more neutral but modern welcome card */
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        color: #343a40;
        /* Darker text for better contrast */
        padding: 25px;
        border-radius: 12px;
        /* Consistent rounded corners */
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.075);
        /* Softer shadow */
    }

    .dashboard-welcome-card h4 {
        font-size: 1.75rem;
        /* Slightly reduced from image but still prominent */
        font-weight: 600;
        margin-bottom: 8px;
        color: #007bff;
        /* Primary color for heading */
    }

    .dashboard-welcome-card p {
        font-size: 1rem;
        margin-bottom: 12px;
        color: #495057;
    }

    .dashboard-welcome-card .time-info {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .dashboard-welcome-card .welcome-icon img {
        max-width: 100px;
        /* Adjusted for a cleaner look */
        opacity: 0.85;
    }

    .dashboard-section-title {
        font-size: 1.25rem;
        /* Standardized section title */
        font-weight: 600;
        color: #2c3e50;
        /* Dark blue/grey */
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 2px solid #007bff;
        display: inline-block;
    }

    .dashboard-card {
        /* General card styling for content blocks */
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }

    .dashboard-card h5.card-title-custom {
        /* For card titles within dashboard cards */
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 15px;
        color: #343a40;
    }

    .apel-card {
        /* For Apel Pagi/Sore cards */
        text-align: center;
        padding: 20px 15px;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .apel-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.08);
    }

    .apel-card img {
        max-width: 150px;
        margin-bottom: 12px;
    }

    .apel-card h6 {
        font-weight: 500;
        font-size: 1rem;
        margin-bottom: 15px;
        color: #343a40;
    }

    .apel-card .btn {
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 0.4rem 1.2rem;
        letter-spacing: 0.5px;
    }


    /* Piket Hari Ini Cards (Common for Pokmin, Superadmin, Piket-after-register) */
    .piket-personel-card {
        text-align: center;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    }

    .piket-personel-card img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 8px;
        border: 2px solid #007bff;
    }

    .piket-personel-card .piket-role {
        font-size: 0.75rem;
        color: #6c757d;
        display: block;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .piket-personel-card .piket-name {
        font-weight: 500;
        font-size: 0.9rem;
        color: #343a40;
    }

    .piket-personel-card .piket-detail {
        font-size: 0.8rem;
        color: #555;
    }


    /* Keterangan Simpan Rekap Apel Table (Pokmin, Superadmin) */
    .rekap-status-table thead th {
        background-color: #e9ecef;
        font-size: 0.85rem;
        text-align: center;
    }

    .rekap-status-table tbody td {
        font-size: 0.85rem;
        text-align: center;
    }

    .rekap-status-table tbody td:nth-child(1),
    /* Jenis Apel */
    .rekap-status-table tbody td:nth-child(4) {
        /* Keterangan */
        text-align: left;
    }


    /* Piket Dashboard Specifics */
    .mulai-sesi-piket-card {
        text-align: center;
        padding: 30px 20px;
    }

    .mulai-sesi-piket-card .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
    }

    .piket-info-text {
        background-color: #e9f7fe;
        /* Light blue info */
        color: #0c5460;
        border: 1px solid #b8daff;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    /* Pimpinan Dashboard Specifics */
    .stat-card {
        /* Re-styling for pimpinan/superadmin summary */
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        text-align: center;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }

    .stat-card i.fas {
        font-size: 1.8rem;
        margin-bottom: 10px;
        display: block;
    }

    /* Icon on top */
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        display: block;
        color: #343a40;
    }

    .stat-card .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .chart-container {
        height: 320px;
    }

    /* Badge Styles (from rekap_apel index, ensure these are consistent or defined globally) */
    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.75em;
        border-radius: 0.375rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .badge-selesai {
        background-color: #28a745;
        color: white;
    }

    .badge-terverifikasi {
        background-color: #007bff;
        color: white;
    }

    .badge-terkirim {
        background-color: #17a2b8;
        color: white;
    }

    .badge-sementara {
        background-color: #fd7e14;
        color: white;
    }

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

    /* For table action buttons */
    .table .btn-sm.btn-wa {
        background-color: #25D366;
        color: white;
        border-color: #25D366;
    }

    .table .btn-sm.btn-wa:hover {
        background-color: #1DAE50;
        border-color: #1DAE50;
    }

    .table .btn-sm.btn-warning {
        color: #fff;
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .table .btn-sm.btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }

    /* Select2 styling for modals */
    .select2-container--bootstrap-5 .select2-selection--single {
        height: calc(1.5em + .75rem + 2px);
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + .75rem);
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem);
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        {{-- Common Welcome Card --}}
        <div class="dashboard-welcome-card">
            <div>
                <h4>Selamat Datang, {{ Auth::user()->role === 'superadmin' ? 'Admin' : ucfirst(Auth::user()->role) }}!
                </h4>
                @if(Auth::user()->role === 'pokmin')
                <p>Silahkan Laksanakan Apel hari ini untuk Subdis Anda.</p>
                @elseif(Auth::user()->role === 'piket')
                <p>Silahkan Laksanakan dan Verifikasi Rekap Apel Hari Ini.</p>
                @elseif(Auth::user()->role === 'pimpinan')
                <p>Selamat datang di Dasbor Pimpinan. Pantau kinerja dan kehadiran.</p>
                @elseif(Auth::user()->role === 'superadmin')
                <p>Anda memiliki akses penuh ke semua fitur sistem.</p>
                @endif
                <div class="time-info" id="currentTimeDashboard">{{ $currentTimeFormatted }}</div>
            </div>
            <div class="welcome-icon">
                {{-- Choose an appropriate generic icon or make it dynamic --}}
                <img src="{{ asset('assets/img/illustrations/dashboard-welcome.svg') }}" alt="Welcome"
                    style="max-width: 130px;">
            </div>
        </div>

        @include('backend.partials.alert') {{-- For session messages --}}

        {{-- Role Specific Content --}}
        @if(Auth::user()->role === 'pokmin')
        @include('backend.dashboard_partials._pokmin_dashboard')
        @elseif(Auth::user()->role === 'piket')
        @include('backend.dashboard_partials._piket_dashboard')
        @elseif(Auth::user()->role === 'pimpinan')
        @include('backend.dashboard_partials._pimpinan_dashboard')
        @elseif(Auth::user()->role === 'superadmin')
        @include('backend.dashboard_partials._superadmin_dashboard')
        @else
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <p>Dashboard untuk peran Anda belum dikonfigurasi atau Anda tidak memiliki tampilan dashboard khusus.
                </p>
                <p>Silakan gunakan menu navigasi untuk mengakses fitur yang tersedia.</p>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- MODALS (Commonly placed at the end of the main view or layout) --}}

{{-- Modal for Piket Registration (used by Piket & Superadmin Dashboard if Piket not set for today) --}}
@if((Auth::user()->role === 'piket' || Auth::user()->role === 'superadmin') && !$hasPiketToday)
<div class="modal fade" id="addPiketModal" tabindex="-1" aria-labelledby="addPiketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPiketModalLabel">Sesi Pendaftaran Piket Hari Ini ({{
                    $todayCarbon->translatedFormat('d M Y') }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('piket.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="piket_date" value="{{ $today }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pajaga_by_modal" class="form-label"><i class="fas fa-user-shield me-1"></i> Pa
                                Jaga</label>
                            <select name="pajaga_by" id="pajaga_by_modal" class="form-select select2-piket" required>
                                <option value="">- Pilih Pa Jaga -</option>
                                @foreach ($usersForPiketSelection as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bajaga_first_by_modal" class="form-label"><i class="fas fa-user me-1"></i> Ba
                                Jaga</label>
                            <select name="bajaga_first_by" id="bajaga_first_by_modal" class="form-select select2-piket"
                                required>
                                <option value="">- Pilih Ba Jaga -</option>
                                @foreach ($usersForPiketSelection as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bajaga_second_by_modal" class="form-label"><i class="fas fa-user me-1"></i> Jaga
                                Tariat</label>
                            <select name="bajaga_second_by" id="bajaga_second_by_modal"
                                class="form-select select2-piket" required>
                                <option value="">- Pilih Jaga Tariat -</option>
                                @foreach ($usersForPiketSelection as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Piket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal for Editing Piket (used by Superadmin Dashboard) --}}
@if(in_array(Auth::user()->role, ['superadmin', 'piket']) && $piketToday)
<div class="modal fade" id="editPiketModal" tabindex="-1" aria-labelledby="editPiketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPiketModalLabel">Edit Data Piket Hari Ini ({{ $piketToday->piket_date ?
                    \Carbon\Carbon::parse($piketToday->piket_date)->translatedFormat('d M Y') : '' }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPiketForm" action="{{ route('piket.update', $piketToday->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_pajaga_by" class="form-label"><i class="fas fa-user-shield me-1"></i> Pa
                                Jaga</label>
                            <select name="pajaga_by" id="edit_pajaga_by" class="form-select select2-piket-edit"
                                required>
                                @foreach ($usersForPiketSelection as $user) {{-- Assuming $usersForPiketSelection is
                                passed for superadmin too --}}
                                <option value="{{ $user->id }}" {{ $piketToday->pajaga_by == $user->id ? 'selected' : ''
                                    }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_bajaga_first_by" class="form-label"><i class="fas fa-user me-1"></i> Ba
                                Jaga</label>
                            <select name="bajaga_first_by" id="edit_bajaga_first_by"
                                class="form-select select2-piket-edit" required>
                                @foreach ($usersForPiketSelection as $user)
                                <option value="{{ $user->id }}" {{ $piketToday->bajaga_first_by == $user->id ?
                                    'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_bajaga_second_by" class="form-label"><i class="fas fa-user me-1"></i> Jaga
                                Tariat</label>
                            <select name="bajaga_second_by" id="edit_bajaga_second_by"
                                class="form-select select2-piket-edit" required>
                                @foreach ($usersForPiketSelection as $user)
                                <option value="{{ $user->id }}" {{ $piketToday->bajaga_second_by == $user->id ?
                                    'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
{{-- Include Chart.js only if pimpinan or superadmin (if they also see charts) --}}
@if(in_array(Auth::user()->role, ['pimpinan', 'superadmin']))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endif

<script>
    $(document).ready(function() {
        // Live Clock
        function updateWaktuDashboard() {
            moment.locale('id');
            $('#currentTimeDashboard').text(moment().format('dddd, DD MMMM YYYY HH:mm:ss') + ' WIB');
        }
        if ($('#currentTimeDashboard').length) {
            updateWaktuDashboard();
            setInterval(updateWaktuDashboard, 1000);
        }

        // Initialize Select2 for Piket Modals if they exist
        if ($('#addPiketModal').length && $('.select2-piket').length) {
            $('.select2-piket').select2({
                dropdownParent: $('#addPiketModal'),
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
         if ($('#editPiketModal').length && $('.select2-piket-edit').length) {
            $('.select2-piket-edit').select2({
                dropdownParent: $('#editPiketModal'),
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        // ==========================================================
        // TAMBAHKAN BLOK SCRIPT DI BAWAH INI
        // ==========================================================
        @if(Auth::user()->role === 'pimpinan' && isset($kehadiranChartData))
            const ctxKehadiran = document.getElementById('kehadiranChart');
            if (ctxKehadiran) {
                const labelsKehadiran = @json($kehadiranChartData['labels']);
                const dataKehadiran = @json($kehadiranChartData['data']);
                new Chart(ctxKehadiran, {
                    type: 'pie', // Menggunakan tipe Pie Chart
                    data: {
                        labels: labelsKehadiran,
                        datasets: [{
                            label: 'Total',
                            data: dataKehadiran,
                            backgroundColor: [ // Sediakan warna yang cukup
                                'rgba(40, 167, 69, 0.7)',  // success (Hadir)
                                'rgba(255, 193, 7, 0.7)',   // warning (Izin)
                                'rgba(220, 53, 69, 0.7)',   // danger (Sakit/Alpha)
                                'rgba(0, 123, 255, 0.7)',   // primary
                                'rgba(23, 162, 184, 0.7)',  // info
                                'rgba(108, 117, 125, 0.7)', // secondary
                                'rgba(253, 126, 20, 0.7)',  // orange
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top', // Pindahkan legenda ke atas
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + ' Personel';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        @endif

        // Pimpinan Dashboard: Chart
        @if(Auth::user()->role === 'pimpinan')
             // Chart BARU: Tren Kehadiran Harian (Line Chart)
            const trenHarianCtx = document.getElementById('trenHarianChart');
            if (trenHarianCtx) {
                new Chart(trenHarianCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartTrenHarianLabels),
                        datasets: [{
                            label: 'Jumlah Personel Hadir',
                            data: @json($chartTrenHarianData),
                            fill: true, // Beri warna di bawah garis
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0 // Pastikan tidak ada desimal di sumbu Y
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false // Sembunyikan legenda karena sudah jelas dari judul
                            }
                        }
                    }
                });
            }
        @endif

        // Superadmin Dashboard - Edit Piket Button (if you add a specific button for it)
        // This assumes the modal is triggered by a button with class 'edit-piket-button-superadmin'
        // The current setup for superadmin reuses the #editPiketModal directly if $piketToday exists.
        // If you have a specific button on the superadmin partial:
        // $('.edit-piket-button-superadmin').on('click', function() {
        //     $('#editPiketModal').modal('show');
        // });
    });
</script>
@endpush