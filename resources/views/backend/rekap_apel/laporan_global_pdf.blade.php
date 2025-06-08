<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Global Rekap Apel</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.2;
            color: #333;
        }

        @page {
            margin: 20mm 15mm;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 3px 0 0;
            font-size: 10px;
        }

        .filter-info-pdf {
            font-size: 10px;
            margin-bottom: 10px;
            text-align: left;
            font-style: italic;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8.5px;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid #777;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }

        table.report-table th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }

        table.report-table td.no {
            width: 4%;
            text-align: center;
        }

        table.report-table td.pangkat,
        table.report-table td.keterangan-value {
            text-align: center;
        }

        .subdis-section-pdf {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .subdis-title-pdf {
            font-size: 11px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 5px;
            border: 1px solid #ccc;
            margin-bottom: -1px;
        }

        .summary-card-pdf {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 15px;
            background-color: #fdfdfd;
            page-break-inside: avoid;
        }

        .summary-card-pdf h4 {
            font-size: 11px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .summary-grid-pdf {
            width: 100%;
        }

        .summary-grid-pdf td {
            border: none;
            padding: 2px 0;
            width: 33.33%;
            font-size: 8.5px;
            vertical-align: top;
        }

        .summary-name-pdf {}

        .summary-count-pdf {
            font-weight: bold;
            text-align: right;
        }

        .summary-totals-separator-pdf {
            border-top: 1px dashed #999 !important;
            margin-top: 5px;
            padding-top: 5px;
        }

        .piket-info-pdf {
            border: 1px solid #ccc;
            padding: 8px;
            margin-top: 15px;
            background-color: #fdfdfd;
            page-break-inside: avoid;
        }

        .piket-info-pdf h4 {
            font-size: 11px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 8px;
        }

        .piket-info-pdf ul {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
            font-size: 8.5px;
        }

        .piket-info-pdf ul li strong {
            min-width: 70px;
            display: inline-block;
        }

        .footer-pdf {
            position: fixed;
            bottom: -15mm;
            left: 0mm;
            right: 0mm;
            height: 20mm;
            font-size: 8px;
            color: #555;
            border-top: 0.5px solid #ccc;
            padding-top: 5px;
        }

        .footer-pdf .left {
            float: left;
            text-align: left;
        }

        .footer-pdf .right {
            float: right;
            text-align: right;
        }

        .footer-pdf .page-number:after {
            content: counter(page) " / " counter(pages);
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Global Rekapitulasi Apel</h1>
            <p>
                Apel {{ ucfirst($filterType) }}
                @if($filterSubdisId)
                    <br>Subdis: {{ $selectedSubdisName }}
                @else
                    <br>Semua Subdis
                @endif
                <br>Tanggal: {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('l, d F Y') }}
            </p>
        </div>

        @if($subdisData->isEmpty())
            <p style="text-align:center;">Tidak ada data rekap apel untuk filter yang dipilih.</p>
        @else
            @foreach($subdisData as $sub)
                <div class="subdis-section-pdf">
                    <h3 class="subdis-title-pdf">Subdis: {{ $sub->name }} (Total Personel: {{ $sub->personil_count }})</h3>

                    @php
                        $apelSession = $sub->apelSessions->first();
                        $summaryByKeterangan = [];

                        foreach ($sub->users as $personel) {
                            $keteranganName = 'Belum Ada Sesi';
                            if ($apelSession) {
                                $attendanceRecord = $apelSession->attendances->where('user_id', $personel->id)->first();
                                if ($attendanceRecord && $attendanceRecord->keterangan) {
                                    $keteranganName = $attendanceRecord->keterangan->name;
                                } elseif($attendanceRecord) {
                                    $keteranganName = '-';
                                } else {
                                    $keteranganName = 'Belum Diisi';
                                }
                            }
                            $summaryByKeterangan[$keteranganName] = ($summaryByKeterangan[$keteranganName] ?? 0) + 1;
                        }

                        // Urutkan: Hadir dulu, lalu sisanya
                        $orderedSummary = [];
                        if (isset($summaryByKeterangan['Hadir'])) {
                            $orderedSummary['Hadir'] = $summaryByKeterangan['Hadir'];
                        }
                        foreach ($summaryByKeterangan as $key => $value) {
                            if ($key !== 'Hadir') {
                                $orderedSummary[$key] = $value;
                            }
                        }
                    @endphp

                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Nama Keterangan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderedSummary as $name => $count)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td>{{ $count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="text-align:center; font-style:italic;">Tidak ada data personel dalam subdis ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach

            {{-- Global Summary Card --}}
            <div class="summary-card-pdf">
                <h4>Ringkasan Total Keterangan (@if($filterSubdisId) {{ $selectedSubdisName }} @else Semua Subdis @endif)</h4>
                <table class="summary-grid-pdf">
                    @php
                        $orderedGrandTotals = [];
                        if(isset($grandTotals['Hadir'])) {
                            $orderedGrandTotals['Hadir'] = $grandTotals['Hadir'];
                        }
                        foreach($grandTotals as $name => $count) {
                            if(strtolower($name) !== 'hadir') {
                                $orderedGrandTotals[$name] = $count;
                            }
                        }
                        $chunkedTotals = array_chunk($orderedGrandTotals, ceil(count($orderedGrandTotals) / 3), true);
                    @endphp
                    <tr>
                        @foreach($chunkedTotals as $chunk)
                        <td valign="top">
                            @foreach($chunk as $name => $count)
                            <div class="summary-item">
                                <span class="summary-name-pdf">{{ $name }}:</span>
                                <span class="summary-count-pdf">{{ $count }}</span>
                            </div>
                            @endforeach
                        </td>
                        @endforeach
                        @for ($i = count($chunkedTotals); $i < 3; $i++)
                            <td>&nbsp;</td>
                        @endfor
                    </tr>
                </table>
                <div class="summary-totals-separator-pdf"></div>
                <table class="summary-grid-pdf" style="margin-top:5px;">
                    <tr>
                        <td>
                            <div class="summary-item fw-bold">
                                <span class="summary-name-pdf">Jumlah (Direkap):</span>
                                <span class="summary-count-pdf">{{ $totalDirekapKeseluruhan }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="summary-item">
                                <span class="summary-name-pdf">Kurang (Belum Direkap):</span>
                                <span class="summary-count-pdf">{{ $totalKurangKeseluruhan }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="summary-item">
                                <span class="summary-name-pdf">Total Personel Terdata:</span>
                                <span class="summary-count-pdf">{{ $totalPersonilKeseluruhan }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:left; font-weight:bold; padding-top:5px;">
                            <div class="summary-item">
                                <span class="summary-name-pdf">Hadir Aktual:</span>
                                <span class="summary-count-pdf">{{ $grandTotals['Hadir'] ?? 0 }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        {{-- Informasi Piket --}}
        @if($piketHariIni)
        <div class="piket-info-pdf">
            <h4>Petugas Piket Tanggal {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d F Y') }}</h4>
            <ul>
                <li><strong>Pa Jaga:</strong> {{ $piketHariIni->pajaga?->name ?? 'N/A' }}</li>
                <li><strong>Ba Jaga I:</strong> {{ $piketHariIni->bajagaFirst?->name ?? 'N/A' }}</li>
                <li><strong>Jaga Tariat (Ba Jaga II):</strong> {{ $piketHariIni->bajagaSecond?->name ?? 'N/A' }}</li>
            </ul>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer-pdf">
            <div class="left">
                Laporan Global Rekap Apel - {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d M Y') }} - {{ ucfirst($filterType) }}
            </div>
            <div class="right">
                Dicetak pada: {{ $timestampCetak }} oleh: {{ $dicetakOleh }} - <span class="page-number"></span>
            </div>
        </div>
    </div>
</body>

</html>