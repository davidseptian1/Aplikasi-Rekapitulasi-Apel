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

    .chart-card {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }

    .chart-card h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #343a40;
        text-align: center;
    }

    .chart-container {
        position: relative;
        height: 300px;
        /* Adjust height as needed */
        width: 100%;
    }

    .pie-chart-container {
        height: 320px;
        /* Slightly more for pie */
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
            <form method="GET" action="{{ route('grafik.kehadiran.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="date_filter_grafik" class="form-label">Pilih Tanggal</label>
                        <input type="date" class="form-control" id="date_filter_grafik" name="date"
                            value="{{ $filterDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="subdis_filter_grafik" class="form-label">Pilih Subdis</label>
                        <select class="form-select" id="subdis_filter_grafik" name="subdis_id">
                            <option value="">-- Semua Subdis --</option>
                            @foreach($subdisList as $subdis)
                            <option value="{{ $subdis->id }}" {{ $selectedSubdisId==$subdis->id ? 'selected' : '' }}>
                                {{ $subdis->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            {{-- Total Keterangan Kehadiran (Bar Chart) --}}
            <div class="col-lg-7 mb-4">
                <div class="chart-card h-100">
                    <h5>Total Keterangan Kehadiran</h5>
                    <div class="chart-container">
                        <canvas id="totalKeteranganChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Persentase Kehadiran (Pie Chart) --}}
            <div class="col-lg-5 mb-4">
                <div class="chart-card h-100">
                    <h5>Persentase Keterangan</h5>
                    <div class="chart-container pie-chart-container">
                        <canvas id="persentaseKeteranganChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Rekap Hadir vs Tidak Hadir (Bar Chart) --}}
            <div class="col-lg-12 mb-4"> {{-- Made this full width for better display --}}
                <div class="chart-card">
                    <h5>Rekap Kehadiran Berdasarkan Hadir dan Tidak Hadir</h5>
                    <div class="chart-container" style="height: 280px;">
                        <canvas id="hadirTidakHadirChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- Ensure Chart.js is included, ideally in app-backend.blade.php or here if not global --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
    // Initialize Select2 for subdis filter if you use it
    if ($('#subdis_filter_grafik').length) {
        $('#subdis_filter_grafik').select2({
            width: '100%',
            placeholder: '-- Semua Subdis --'
        });
    }

    // Chart 1: Total Keterangan Kehadiran (Bar Chart)
    const totalKeteranganCtx = document.getElementById('totalKeteranganChart');
    if (totalKeteranganCtx) {
        new Chart(totalKeteranganCtx, {
            type: 'bar',
            data: {
                labels: @json($chartKeteranganLabels),
                datasets: [{
                    label: 'Jumlah',
                    data: @json($chartKeteranganData),
                    backgroundColor: [ // Example colors, add more if more keterangan types
                        'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
                        'rgba(159, 159, 159, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: { legend: { display: false } }
            }
        });
    }

    // Chart 2: Persentase Keterangan (Pie Chart)
    const persentaseKeteranganCtx = document.getElementById('persentaseKeteranganChart');
    if (persentaseKeteranganCtx && {{ $totalAttendanceRecordsForPie > 0 ? 'true' : 'false' }}) {
        new Chart(persentaseKeteranganCtx, {
            type: 'pie',
            data: {
                labels: @json($chartKeteranganLabels),
                datasets: [{
                    label: 'Persentase',
                    data: @json($chartKeteranganData),
                     backgroundColor: [ /* Same colors as bar chart or new set */
                        'rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth:15, padding:10, font: {size: 10} } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed !== null) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? (context.parsed / total * 100).toFixed(1) + '%' : '0%';
                                    label += context.parsed + ' (' + percentage + ')';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (persentaseKeteranganCtx) {
        persentaseKeteranganCtx.getContext('2d').fillText('Tidak ada data untuk ditampilkan.', 10, 50);
    }


    // Chart 3: Rekap Hadir vs Tidak Hadir (Bar Chart)
    const hadirTidakHadirCtx = document.getElementById('hadirTidakHadirChart');
    if (hadirTidakHadirCtx) {
        new Chart(hadirTidakHadirCtx, {
            type: 'bar',
            data: {
                labels: @json($chartHadirTidakHadirLabels),
                datasets: [{
                    label: 'Jumlah Personel',
                    data: @json($chartHadirTidakHadirData),
                    backgroundColor: ['rgba(75, 192, 192, 0.7)', 'rgba(255, 99, 132, 0.7)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: { legend: { display: false } }
            }
        });
        
    }
});
</script>
@endpush