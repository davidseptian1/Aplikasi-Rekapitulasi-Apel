@extends('layouts.app-backend')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header">
                <h5>{{ $pages }}</h5>
                <div class="list-btn">
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input type="date" class="form-control date-picker" id="apel-date" value="{{ $date }}">
                                <button class="btn btn-primary" type="button" id="refresh-date">
                                    <i class="fa fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('laporan-subdis.index', ['type' => 'pagi', 'date' => $date]) }}"
                                class="btn btn-outline-primary {{ $type == 'pagi' ? 'active' : '' }}">
                                <i class="fa fa-sun me-1"></i> Apel Pagi
                            </a>
                            <a href="{{ route('laporan-subdis.index', ['type' => 'sore', 'date' => $date]) }}"
                                class="btn btn-outline-primary {{ $type == 'sore' ? 'active' : '' }}">
                                <i class="fa fa-moon me-1"></i> Apel Sore
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('backend.partials.alert')

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Subdis</th>
                                        <th>Total Anggota</th>
                                        <th>Hadir</th>
                                        <th>Tidak Hadir</th>
                                        <th>Persentase</th>
                                        <th class="text-end no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subdis as $row)
                                    @php
                                    $apelSession = $row->apelSessions->first();
                                    $totalAnggota = $row->users()->where('role', 'personil')->count();
                                    $hadir = $apelSession ? $apelSession->attendances->where('keterangan_id',
                                    1)->count() : 0;
                                    $tidakHadir = $totalAnggota - $hadir;
                                    $persentase = $totalAnggota > 0 ? round(($hadir/$totalAnggota)*100, 2) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $totalAnggota }}</td>
                                        <td>{{ $hadir }}</td>
                                        <td>{{ $tidakHadir }}</td>
                                        <td>{{ $persentase }}%</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <button class="btn btn-sm bg-info-light me-2 btn-detail"
                                                    data-subdis-id="{{ $row->id }}" data-date="{{ $date }}"
                                                    data-type="{{ $type }}">
                                                    <i class="fe fe-eye me-1"></i> Detail
                                                </button>
                                                <a href="{{ route('laporan-subdis.cetak-pdf', ['subdis_id' => $row->id, 'date' => $date, 'type' => $type]) }}"
                                                    class="btn btn-sm bg-danger-light" target="_blank">
                                                    <i class="fe fe-download me-1"></i> PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Laporan Subdis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="mb-1" id="modalSubdisName"></h6>
                    <p class="text-muted mb-0" id="modalDateType"></p>
                </div>

                <h6 class="mb-3">Daftar Anggota</h6>
                <div class="table-responsive">
                    <table class="table table-bordered" id="anggotaTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Pangkat</th>
                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-3 mt-4">Rekapitulasi Keterangan</h6>
                <div class="table-responsive">
                    <table class="table table-bordered" id="keteranganTable">
                        <thead>
                            <tr>
                                <th>Status Kehadiran</th>
                                <th>Jumlah</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Date picker change handler
        $('#apel-date').on('change', function() {
            const selectedDate = $(this).val();
            const currentType = '{{ $type }}';
            window.location.href = `{{ route('laporan-subdis.index') }}?date=${selectedDate}&type=${currentType}`;
        });

        // Refresh button handler
        $('#refresh-date').on('click', function() {
            const today = new Date().toISOString().split('T')[0];
            $('#apel-date').val(today);
            window.location.href = `{{ route('laporan-subdis.index') }}?date=${today}&type={{ $type }}`;
        });

        // Detail modal handler
        $(document).on('click', '.btn-detail', function() {
            const subdisId = $(this).data('subdis-id');
            const date = $(this).data('date');
            const type = $(this).data('type');

            // Show loading state
            $('#anggotaTable tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
            $('#keteranganTable tbody').html('<tr><td colspan="3" class="text-center">Memuat data...</td></tr>');

            // Set modal title
            $('#modalSubdisName').text($(this).closest('tr').find('td:eq(1)').text());
            $('#modalDateType').text(`${date} - Apel ${type.charAt(0).toUpperCase() + type.slice(1)}`);

            // Load data via AJAX
            $.ajax({
                url: '{{ route("laporan-subdis.detail") }}',
                type: 'GET',
                data: {
                    subdis_id: subdisId,
                    date: date,
                    type: type
                },
                success: function(response) {
                    // Populate anggota table
                    let anggotaHtml = '';
                    response.anggotas.forEach((anggota, index) => {
                        anggotaHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${anggota.name}</td>
                                <td>${anggota.pangkat || '-'}</td>
                                <td>${anggota.keterangan || '-'}</td>
                            </tr>
                        `;
                    });
                    $('#anggotaTable tbody').html(anggotaHtml);

                    // Populate keterangan table
                    let keteranganHtml = '';
                    response.keteranganSummary.forEach(item => {
                        keteranganHtml += `
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.count}</td>
                                <td>${item.percentage}%</td>
                            </tr>
                        `;
                    });
                    $('#keteranganTable tbody').html(keteranganHtml);
                },
                error: function() {
                    $('#anggotaTable tbody').html('<tr><td colspan="4" class="text-center">Gagal memuat data</td></tr>');
                    $('#keteranganTable tbody').html('<tr><td colspan="3" class="text-center">Gagal memuat data</td></tr>');
                }
            });

            $('#detailModal').modal('show');
        });
    });
</script>
@endpush