{{-- Form filter rentang tanggal (tetap dipertahankan) --}}
<div class="card dashboard-card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('dashboard.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
                <label for="type_filter" class="form-label">Sesi Apel</label>
                <select name="type" id="type_filter" class="form-select">
                    <option value="pagi" {{ $selectedType=='pagi' ? 'selected' : '' }}>Pagi</option>
                    <option value="sore" {{ $selectedType=='sore' ? 'selected' : '' }}>Sore</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Kartu Statistik (tetap dipertahankan) --}}
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
    {{-- GRAFIK BARU: Tren Kehadiran Harian (Line Chart) --}}
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm dashboard-card">
            <div class="card-header bg-light border-bottom-0">
                <h5 class="card-title-custom mb-0"><i class="fas fa-chart-line me-2"></i>Tren Kehadiran Harian</h5>
            </div>
            <div class="card-body">
                @if(!empty($chartTrenHarianData))
                <div class="chart-container" style="height:320px;">
                    <canvas id="trenHarianChart"></canvas>
                </div>
                @else
                <p class="text-center text-muted my-5">Tidak ada data kehadiran pada rentang tanggal yang dipilih.</p>
                @endif
            </div>
        </div>
    </div>
</div>