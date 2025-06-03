<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <i class="fas fa-users text-primary"></i>
            <span class="stat-value">{{ $total_personel_admin ?? 0 }}</span>
            <span class="stat-label">Total Personel</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <i class="fas fa-building text-success"></i>
            <span class="stat-value">{{ $total_subdis_admin ?? 0 }}</span>
            <span class="stat-label">Total Subdis</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <i class="fas fa-user-tag text-info"></i>
            <span class="stat-value">{{ $total_roles_admin ?? 0 }}</span>
            <span class="stat-label">Total Role Pengguna</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <i class="fas fa-user-clock text-warning"></i>
            <span class="stat-value">{{ $piketToday ? 'Terjadwal' : 'Belum Ada' }}</span>
            <span class="stat-label">Piket Hari Ini</span>
        </div>
    </div>
</div>


{{-- APEL & Piket Sections --}}
<div class="row mt-2">
    <div class="col-lg-8 mb-4">
        <h5 class="dashboard-section-title mb-3">AKSI CEPAT APEL</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card apel-card shadow-sm h-100">
                    <img src="{{ asset('assets/img/laptop-icon-green.png') }}" alt="Apel Pagi"
                        style="width: 70px; height: 70px;">
                    <h6>Apel Pagi</h6>
                    <a href="{{ route('rekap-apel.index', ['type' => 'pagi', 'date' => $today]) }}"
                        class="btn btn-success"><i class="fas fa-eye me-1"></i> Lihat Rekap Global</a>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card apel-card shadow-sm h-100">
                    <img src="{{ asset('assets/img/laptop-icon-red.png') }}" alt="Apel Sore"
                        style="width: 70px; height: 70px;">
                    <h6>Apel Sore</h6>
                    <a href="{{ route('rekap-apel.index', ['type' => 'sore', 'date' => $today]) }}"
                        class="btn btn-danger"><i class="fas fa-eye me-1"></i> Lihat Rekap Global</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <h5 class="dashboard-section-title mb-3">Piket Hari Ini</h5>
        @if($piketToday)
        <div class="piket-personel-card mb-2">
            <img src="{{ $piketToday->pajaga?->photo_url ?? asset('assets/img/default-user.jpg') }}" alt="Pa Jaga">
            <span class="piket-role">Pa Jaga</span>
            <span class="piket-name">{{ $piketToday->pajaga?->name ?? 'N/A' }}</span>
        </div>
        <div class="piket-personel-card mb-2">
            <img src="{{ $piketToday->bajagaFirst?->photo_url ?? asset('assets/img/default-user.jpg') }}" alt="Ba Jaga">
            <span class="piket-role">Ba Jaga</span>
            <span class="piket-name">{{ $piketToday->bajagaFirst?->name ?? 'N/A' }}</span>
        </div>
        <div class="piket-personel-card">
            <img src="{{ $piketToday->bajagaSecond?->photo_url ?? asset('assets/img/default-user.jpg') }}"
                alt="Jaga Tariat">
            <span class="piket-role">Jaga Tariat</span>
            <span class="piket-name">{{ $piketToday->bajagaSecond?->name ?? 'N/A' }}</span>
        </div>
        <div class="text-center mt-2">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPiketModal">
                <i class="fas fa-edit me-1"></i> Edit Piket Hari Ini
            </button>
        </div>
        @else
        <div class="dashboard-card text-center text-muted py-3">
            <i class="fas fa-calendar-times fa-2x mb-2"></i>
            <p class="mb-1">Belum ada data piket untuk hari ini.</p>
            <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addPiketModal">
                <i class="fas fa-plus-circle me-1"></i> Tambah Piket Hari Ini
            </button>
        </div>
        @endif
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <h5 class="dashboard-section-title mb-3">Status Rekap Apel Semua Subdis (Hari Ini)</h5>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover rekap-status-table mb-0">
                        <thead>
                            <tr>
                                <th>Nama Subdis</th>
                                <th>Kasubdis</th>
                                <th>Jenis Apel</th>
                                <th>Jam (Sesi Dibuat)</th>
                                <th>Tanggal</th>
                                <th>Keterangan Rekap</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($admin_apel_display_list) && count($admin_apel_display_list) > 0)
                            @foreach($admin_apel_display_list as $item)
                            <tr>
                                <td>
                                    <a href="{{ $item['link_anggota'] }}">{{ $item['subdis_name'] }}</a>
                                </td>
                                <td>{{ $item['kasubdis_name'] }}</td>
                                <td>Apel {{ ucfirst($item['type']) }}</td>
                                <td>{{ $item['jam'] }}</td>
                                <td>{{ $item['tanggal'] }}</td>
                                <td>{{ $item['keterangan_rekap'] }}</td>
                                <td>
                                    <span class="badge {{ $item['status_badge'] }}">{{ $item['status_text'] }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                    // Determine if alert button should be shown
                                    $showAlertButton = false;
                                    if ($item['personil_count_for_subdis'] > 0) { // Only alert if subdis has members
                                    if (in_array($item['status_text'], ['Sementara', 'Belum Ada Sesi', 'Perlu Dicek']))
                                    {
                                    $showAlertButton = true;
                                    }
                                    }
                                    @endphp

                                    @if($showAlertButton && !empty($item['kasubdis_no_telpon']))
                                    @php
                                    $cleanPhoneNumber = preg_replace('/[^0-9]/', '', $item['kasubdis_no_telpon']);
                                    if (substr($cleanPhoneNumber, 0, 1) === '0') {
                                    $waPhoneNumber = '62' . substr($cleanPhoneNumber, 1);
                                    } elseif (substr($cleanPhoneNumber, 0, 2) === '62') {
                                    $waPhoneNumber = $cleanPhoneNumber;
                                    } else {
                                    $waPhoneNumber = '62' . $cleanPhoneNumber; // Default if no leading 0 or 62
                                    }

                                    $waMessage = "Assalamualaikum Pak/Bu " . urlencode($item['kasubdis_name']) . ",
                                    mohon untuk segera melengkapi dan mengirimkan data rekap Apel " .
                                    ucfirst($item['type']) . " untuk Subdis " . urlencode($item['subdis_name']) . "
                                    tanggal " . urlencode($item['tanggal']) . ". Terima kasih.";
                                    @endphp
                                    <a href="https://api.whatsapp.com/send?phone={{ $waPhoneNumber }}&text={{ $waMessage }}"
                                        class="btn btn-sm btn-wa" target="_blank"
                                        title="Ingatkan Kasubdis via WhatsApp">
                                        <i class="fab fa-whatsapp"></i> Alert
                                    </a>
                                    @else
                                    <a href="{{ $item['link_anggota'] }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="8" class="text-center py-3">Tidak ada data subdis untuk ditampilkan.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>