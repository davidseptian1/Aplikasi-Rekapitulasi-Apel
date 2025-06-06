{{-- Menambahkan form filter di bagian paling atas --}}
<div class="card dashboard-card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('dashboard.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-5">
                <label for="end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>
                    Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Kartu Statistik --}}
<div class="row">
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="stat-card h-100">
            <i class="fas fa-users text-primary"></i>
            <span class="stat-value">{{ $total_personel_pimpinan ?? 0 }}</span>
            <span class="stat-label">Total Personel Aktif</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="stat-card h-100">
            <i class="fas fa-building text-success"></i>
            <span class="stat-value">{{ $total_subdis_pimpinan ?? 0 }}</span>
            <span class="stat-label">Total Subdis</span>
        </div>
    </div>
    <div class="col-md-12 col-xl-4 mb-4">
        <div class="stat-card h-100 d-flex flex-column justify-content-center align-items-center"
            style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
            <i class="fas fa-calendar-check text-info"></i>
            <span class="stat-label mt-2">Menampilkan Data Laporan</span>
            <strong class="text-primary">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} &mdash; {{
                \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
        </div>
    </div>
</div>

<div class="row mt-2">
    {{-- GRAFIK BARU: Kehadiran (Pie Chart) --}}
    <div class="col-xl-5 mb-4">
        <div class="card shadow-sm dashboard-card h-100">
            <div class="card-header bg-light border-bottom-0">
                <h5 class="card-title-custom mb-0"><i class="fas fa-chart-pie me-2"></i>Grafik Kehadiran Personel</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                @if(!empty($kehadiranChartData['data']) && $kehadiranChartData['data']->sum() > 0)
                <div class="chart-container" style="height:300px; width:100%;">
                    <canvas id="kehadiranChart"></canvas>
                </div>
                @else
                <p class="text-center text-muted my-5">Tidak ada data kehadiran pada rentang tanggal yang dipilih.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- GRAFIK LAMA: Personel per Subdis (Bar Chart) --}}
    <div class="col-xl-7 mb-4">
        <div class="card shadow-sm dashboard-card h-100">
            <div class="card-header bg-light border-bottom-0">
                <h5 class="card-title-custom mb-0"><i class="fas fa-chart-bar me-2"></i>Total Personel Tiap Subdis</h5>
            </div>
            <div class="card-body">
                @if(isset($personel_per_subdis_chart) && $personel_per_subdis_chart->count() > 0)
                <div class="chart-container" style="height:300px;">
                    <canvas id="personelSubdisChart"></canvas>
                </div>
                @else
                <p class="text-center text-muted my-5">Data untuk chart tidak tersedia.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Include halaman lain jika ada --}}
{{-- @extends('backend.grafik_kehadiran.index') --}}
{{-- Baris di atas saya komentari karena sepertinya tidak relevan di partial dashboard --}}