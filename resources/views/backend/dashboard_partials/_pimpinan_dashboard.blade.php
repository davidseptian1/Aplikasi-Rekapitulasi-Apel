
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
        {{-- Placeholder for a third stat card or different info --}}
        <div class="stat-card h-100 d-flex flex-column justify-content-center align-items-center"
            style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
            <i class="fas fa-calendar-check text-info"></i>
            <span class="stat-label mt-2">Kehadiran Hari Ini</span>
            <small class="text-muted">(Detail akan ditampilkan di Laporan)</small>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <div class="card shadow-sm dashboard-card">
            <div class="card-header bg-light border-bottom-0">
                <h5 class="card-title-custom mb-0"><i class="fas fa-chart-bar me-2"></i>Total Personel Tiap Subdis</h5>
            </div>
            <div class="card-body">
                @if(isset($personel_per_subdis_chart) && $personel_per_subdis_chart->count() > 0)
                <div class="chart-container">
                    <canvas id="personelSubdisChart"></canvas>
                </div>
                @else
                <p class="text-center text-muted">Data untuk chart tidak tersedia.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@extends('backend.grafik_kehadiran.index')