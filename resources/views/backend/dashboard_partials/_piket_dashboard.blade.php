@if(!$hasPiketToday)
{{-- State before Piket session is started (image_6.png) --}}
<div class="row justify-content-center mt-4">
    <div class="col-lg-7">
        <div class="card dashboard-card text-center mulai-sesi-piket-card shadow-lg">
            <img src="{{ asset('assets/img/illustrations/undraw_celebrating_2aox.svg') }}" alt="Mulai Sesi"
                style="max-width: 150px; margin-bottom: 20px; margin-left:auto; margin-right:auto;">
            <h5 class="card-title-custom">Sesi Piket Hari Ini Belum Dimulai</h5>
            <p class="text-muted my-3">Klik tombol di bawah untuk mendaftarkan petugas piket hari ini.</p>
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addPiketModal">
                <i class="fas fa-play-circle me-2"></i>Mulai Sesi Piket
            </button>
        </div>
        <div class="piket-info-text mt-3">
            <i class="fas fa-info-circle me-1"></i>
            Lakukan Sesi Piket berisikan: Pa Jaga, Ba Jaga, Jaga Tariat. Pendaftaran Piket akan otomatis terganti setiap
            harinya.
            Setelah melakukan pendaftaran sesi, piket dapat melakukan Verifikasi Rekap Apel sesuai dengan apel yang
            berlangsung.
        </div>
        <div class="dashboard-card text-center mt-3">
            <p class="text-muted mb-0">Belum ada data yang ditampilkan dengan Piket: Pa Jaga, Ba Jaga, Jaga Tariat.</p>
        </div>
    </div>
</div>
@else
{{-- State after Piket session is started (image_8.png) --}}
<div class="row">
    <div class="col-lg-12 mb-4">
        <h5 class="dashboard-section-title mb-3">APEL (Verifikasi Rekap)</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card apel-card shadow-sm h-100">
                    <img src="{{ asset('assets/img/laptop-icon-green.png') }}" alt="Apel Pagi"
                        style="width: 70px; height: 70px;">
                    <h6>Apel Pagi</h6>
                    <a href="{{ route('rekap-apel.index', ['type' => 'pagi', 'date' => $today]) }}"
                        class="btn btn-info btn-sm px-4">
                        <i class="fas fa-clipboard-check me-1"></i> Verif Rekap!
                    </a>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card apel-card shadow-sm h-100">
                    <img src="{{ asset('assets/img/laptop-icon-red.png') }}" alt="Apel Sore"
                        style="width: 70px; height: 70px;">
                    <h6>Apel Sore</h6>
                    <a href="{{ route('rekap-apel.index', ['type' => 'sore', 'date' => $today]) }}"
                        class="btn btn-info btn-sm px-4">
                        <i class="fas fa-clipboard-check me-1"></i> Verif Rekap!
                    </a>
                </div>
            </div>
        </div>
        <div class="piket-info-text mt-2">
            <i class="fas fa-user-shield me-1"></i>
            Sesi Piket hari ini telah didaftarkan. Anda dapat melakukan Verifikasi Rekap Apel untuk sesi Pagi dan Sore.
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <h5 class="dashboard-section-title mb-3">Data Piket Hari Ini</h5>
        <div class="row piket-data-hari-ini">
            @if($piketToday)
            <div class="col-md-4 mb-3">
                <div class="piket-personel-card h-100">
                    <img src="{{ $piketToday->pajaga?->photo_url ?? asset('assets/img/default-user.jpg') }}"
                        alt="Pa Jaga">
                    <span class="piket-role">Pa Jaga</span>
                    <span class="piket-name">{{ $piketToday->pajaga?->name ?? 'N/A' }}</span>
                    <small class="piket-detail d-block">{{ $piketToday->pajaga?->biodata?->pangkat?->name ?? ''
                        }}</small>
                    <small class="piket-detail d-block">Tanggal : {{ $piketToday->piket_date ?
                        \Carbon\Carbon::parse($piketToday->piket_date)->format('d/m/Y') : '-' }}</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="piket-personel-card h-100">
                    <img src="{{ $piketToday->bajagaFirst?->photo_url ?? asset('assets/img/default-user.jpg') }}"
                        alt="Ba Jaga">
                    <span class="piket-role">Ba Jaga</span>
                    <span class="piket-name">{{ $piketToday->bajagaFirst?->name ?? 'N/A' }}</span>
                    <small class="piket-detail d-block">{{ $piketToday->bajagaFirst?->biodata?->pangkat?->name ?? ''
                        }}</small>
                    <small class="piket-detail d-block">Tanggal : {{ $piketToday->piket_date ?
                        \Carbon\Carbon::parse($piketToday->piket_date)->format('d/m/Y') : '-' }}</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="piket-personel-card h-100">
                    <img src="{{ $piketToday->bajagaSecond?->photo_url ?? asset('assets/img/default-user.jpg') }}"
                        alt="Jaga Tariat">
                    <span class="piket-role">Jaga Tariat</span>
                    <span class="piket-name">{{ $piketToday->bajagaSecond?->name ?? 'N/A' }}</span>
                    <small class="piket-detail d-block">{{ $piketToday->bajagaSecond?->biodata?->pangkat?->name ?? ''
                        }}</small>
                    <small class="piket-detail d-block">Tanggal : {{ $piketToday->piket_date ?
                        \Carbon\Carbon::parse($piketToday->piket_date)->format('d/m/Y') : '-' }}</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <h5 class="dashboard-section-title mb-3">Detail Keterangan Rekap (Hari Ini)</h5>
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
                            @if(isset($subdisListForPiketDashboard) && count($subdisListForPiketDashboard) > 0)
                            @foreach($subdisListForPiketDashboard as $item)
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

@endif