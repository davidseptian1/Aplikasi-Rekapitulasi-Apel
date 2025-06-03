<div class="row">
    {{-- APEL Section --}}
    <div class="col-lg-8 mb-4">
        <h5 class="dashboard-section-title mb-3">APEL HARI INI
            @if($pokminSubdis)
            (<span class="text-primary fw-normal">{{ $pokminSubdis->name }}</span>)
            @endif
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card apel-card shadow-sm h-100">
                    <img src="{{ asset('assets/img/laptop-icon-green.png') }}" alt="Apel Pagi"
                        style="width: 70px; height: 70px;">
                    <h6>Apel Pagi</h6>
                    <a href="{{ route('rekap-apel.anggota', ['id' => $pokminSubdis->id ?? 0, 'type' => 'pagi', 'date' => $today]) }}"
                        class="btn btn-success {{ !$pokminSubdis ? 'disabled' : '' }}">
                        <i class="fas fa-list-check me-1"></i> Rekap!
                    </a>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card apel-card shadow-sm h-100">
                    <img src="{{ asset('assets/img/laptop-icon-red.png') }}" alt="Apel Sore"
                        style="width: 70px; height: 70px;">
                    <h6>Apel Sore</h6>
                    <a href="{{ route('rekap-apel.anggota', ['id' => $pokminSubdis->id ?? 0, 'type' => 'sore', 'date' => $today]) }}"
                        class="btn btn-danger {{ !$pokminSubdis ? 'disabled' : '' }}">
                        <i class="fas fa-list-check me-1"></i> Rekap!
                    </a>
                </div>
            </div>
        </div>
        @if($pokminSubdis)
        <div class="alert alert-light border" role="alert" style="font-size: 0.875rem;">
            <i class="fas fa-info-circle me-1 text-primary"></i>
            Rekap Apel Pagi dapat dilaksanakan, karena Jam menunjukkan Pukul {{ now()->format('H:i') }} WIB.
            {{-- Consider making this message dynamic based on actual apel times --}}
        </div>
        @else
        <div class="alert alert-warning" role="alert" style="font-size: 0.875rem;">
            <i class="fas fa-exclamation-triangle me-1"></i> Anda belum ditugaskan sebagai penanggung jawab Subdis.
            Hubungi Admin.
        </div>
        @endif
    </div>

    {{-- Piket Hari Ini Section --}}
    <div class="col-lg-4 mb-4">
        <h5 class="dashboard-section-title mb-3">Piket Hari Ini</h5>
        @if($piketToday)
        <div class="piket-personel-card mb-2">
            <img src="{{ $piketToday->pajaga?->photo_url ?? asset('assets/img/default-user.jpg') }}" alt="Pa Jaga">
            <span class="piket-role">Pa Jaga</span>
            <span class="piket-name">{{ $piketToday->pajaga?->name ?? 'N/A' }}</span>
            <small class="piket-detail d-block">{{ $piketToday->pajaga?->biodata?->pangkat?->name ?? '' }}</small>
        </div>
        <div class="piket-personel-card mb-2">
            <img src="{{ $piketToday->bajagaFirst?->photo_url ?? asset('assets/img/default-user.jpg') }}" alt="Ba Jaga">
            <span class="piket-role">Ba Jaga</span>
            <span class="piket-name">{{ $piketToday->bajagaFirst?->name ?? 'N/A' }}</span>
            <small class="piket-detail d-block">{{ $piketToday->bajagaFirst?->biodata?->pangkat?->name ?? '' }}</small>
        </div>
        <div class="piket-personel-card">
            <img src="{{ $piketToday->bajagaSecond?->photo_url ?? asset('assets/img/default-user.jpg') }}"
                alt="Jaga Tariat">
            <span class="piket-role">Jaga Tariat</span>
            <span class="piket-name">{{ $piketToday->bajagaSecond?->name ?? 'N/A' }}</span>
            <small class="piket-detail d-block">{{ $piketToday->bajagaSecond?->biodata?->pangkat?->name ?? '' }}</small>
        </div>
        @else
        <div class="dashboard-card text-center text-muted py-4">
            <i class="fas fa-calendar-times fa-2x mb-2"></i>
            <p class="mb-0">Belum ada data piket untuk hari ini.</p>
        </div>
        @endif
    </div>
</div>

@if($pokminSubdis)
<div class="row mt-2">
    <div class="col-12">
        <h5 class="dashboard-section-title mb-3">KETERANGAN SIMPAN REKAP APEL (Subdis: {{ $pokminSubdis->name }})</h5>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover rekap-status-table mb-0">
                        <thead>
                            <tr>
                                <th>Jenis Apel</th>
                                <th>Jam Sesi Dibuat</th>
                                <th>Tanggal</th>
                                <th>Keterangan Rekap</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apel_sessions_info_pokmin as $info)
                            <tr>
                                <td>Apel {{ ucfirst($info['type']) }}</td>
                                <td>{{ $info['jam'] }}</td>
                                <td>{{ $info['tanggal'] }}</td>
                                <td>{{ $info['keterangan_rekap'] }}</td>
                                <td><span class="badge {{ $info['status_badge'] }}">{{ $info['status_text'] }}</span>
                                </td>
                                <td class="text-center">
                                    @if($info['needs_verification_action'])
                                    <a href="{{ route('rekap-apel.anggota', ['id' => $pokminSubdis->id, 'type' => $info['type'], 'date' => $today]) }}"
                                        class="btn btn-sm btn-warning">Segera Verif!</a>
                                    @else
                                    <a href="{{ route('rekap-apel.anggota', ['id' => $pokminSubdis->id, 'type' => $info['type'], 'date' => $today]) }}"
                                        class="btn btn-sm btn-outline-primary">Lihat/Kelola</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">Belum ada data rekap apel untuk hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif