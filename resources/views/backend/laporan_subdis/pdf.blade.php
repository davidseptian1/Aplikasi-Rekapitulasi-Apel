<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Subdis - {{ $subdis->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            /* Base font size for PDF */
            line-height: 1.4;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            /* Borders for all cells */
            padding: 6px 8px;
            /* Adjusted padding */
            text-align: left;
        }

        th {
            background-color: #f0f2f5;
            /* Light grey for table header */
            font-weight: bold;
            text-align: center;
            /* Center align header text */
        }

        td.no {
            text-align: center;
            width: 5%;
        }

        td.pangkat,
        td.keterangan-value {
            text-align: center;
        }

        .summary-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            border: 1px solid #e0e0e0;
        }

        .summary-card h4 {
            font-size: 14px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        .summary-grid {
            /* Using table for multi-column layout for better DOMPDF compatibility */
            width: 100%;
        }

        .summary-grid td {
            border: none;
            padding: 3px 0;
            width: 33.33%;
            /* For 3 columns */
            vertical-align: top;
        }

        .summary-item {
            display: block;
            /* Each item on its own line within the td if needed */
        }

        .summary-name {
            /* display: inline-block; */
            /* width: 70%; */
        }

        .summary-count {
            font-weight: bold;
            /* display: inline-block; */
            /* width: 25%; */
            text-align: right;
        }

        .footer {
            text-align: right;
            font-size: 9px;
            color: #777;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .float-left {
            float: left;
            width: 48%;
            margin-right: 2%;
        }

        .float-right {
            float: left;
            width: 48%;
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
            <h1>Laporan Subdis: {{ $subdis->name }}</h1>
            <p>
                Untuk Apel {{ ucfirst($type) }}
                pada Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <h4>Daftar Personel:</h4>
        <table>
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:35%;">Nama Personel</th>
                    <th style="width:25%;">Pangkat</th>
                    <th style="width:35%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotalHadir = 0; @endphp
                @forelse ($allPersonil as $index => $personel)
                @php
                $keteranganName = 'Belum Diisi';
                $isHadir = false;
                if ($apelSession && $apelSession->attendances) {
                $attendanceRecord = $apelSession->attendances->where('user_id', $personel->id)->first();
                if ($attendanceRecord && $attendanceRecord->keterangan) {
                $keteranganName = $attendanceRecord->keterangan->name;
                if (strtolower($keteranganName) == 'hadir') {
                $isHadir = true;
                $grandTotalHadir++;
                }
                } elseif ($attendanceRecord) {
                $keteranganName = '-';
                }
                }
                @endphp
                <tr>
                    <td class="no">{{ $loop->iteration }}</td>
                    <td>{{ $personel->name }}</td>
                    <td class="pangkat">{{ $personel->biodata?->pangkat?->name ?? '-' }}</td>
                    <td class="keterangan-value">{{ $keteranganName }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data personil pada subdis ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($apelSession)
        <div class="summary-card">
            <h4>Keterangan Total:</h4>
            @php
            $keteranganTotalsForSummary = [];
            $totalDirekap = $apelSession->attendances->count();

            foreach ($masterKeterangans as $ketMaster) {
            $count = $apelSession->attendances->where('keterangan_id', $ketMaster->id)->count();
            $keteranganTotalsForSummary[$ketMaster->name] = $count;
            }

            $summaryPlaceholders = ['TL'=>0, 'MPP'=>0, 'DIK'=>0, 'BP'=>0, 'Cuti LP'=>0];
            foreach($summaryPlaceholders as $pkName => $pkCount){
            if(!array_key_exists($pkName, $keteranganTotalsForSummary)){
            $keteranganTotalsForSummary[$pkName] = $pkCount;
            }
            }

            $keteranganTotalsForSummary['Hadir'] = $grandTotalHadir;


            $totalPersonilSubdis = $allPersonil->count();
            $totalKurang = $totalPersonilSubdis - $totalDirekap;
            if ($totalKurang < 0) $totalKurang=0; $displayOrder=['Hadir', 'Izin' , 'Sakit' , 'Dinas Luar' , 'Cuti' ];
                $orderedSummary=[]; foreach($displayOrder as $key) { if(isset($keteranganTotalsForSummary[$key])) {
                $orderedSummary[$key]=$keteranganTotalsForSummary[$key]; unset($keteranganTotalsForSummary[$key]); } }
                ksort($keteranganTotalsForSummary); $orderedSummary=array_merge($orderedSummary,
                $keteranganTotalsForSummary); @endphp <table class="summary-grid">
                <tr>
                    <td>
                        @php $i = 0; $itemsPerColumn = ceil(count($orderedSummary) / 3); @endphp
                        @foreach($orderedSummary as $name => $count)
                        @if($name === 'TL' && $i % $itemsPerColumn !==0 && $i >
                        $itemsPerColumn*floor($i/$itemsPerColumn) ) @php $i = $itemsPerColumn*ceil($i/$itemsPerColumn);
                        echo "</td>
                    <td>"; @endphp @endif
                        <div class="summary-item">
                            <span class="summary-name">{{ $name }}:</span>
                            <span class="summary-count">{{ $count }}</span>
                        </div>
                        @php $i++; if($i % $itemsPerColumn == 0 && $i < count($orderedSummary)) echo "</td><td>" ;
                            @endphp @endforeach </td>
                </tr>
                </table>
                <div class="clearfix"></div>
                <div class="keterangan-totals-separator"></div>
                <table class="summary-grid" style="margin-top:10px;">
                    <tr>
                        <td>
                            <div class="summary-item fw-bold"><span class="summary-name">Jumlah:</span> <span
                                    class="summary-count">{{ $totalDirekap }}</span></div>
                        </td>
                        <td>
                            <div class="summary-item"><span class="summary-name">Kurang:</span> <span
                                    class="summary-count">{{ $totalKurang }}</span></div>
                        </td>
                        <td>
                            <div class="summary-item"><span class="summary-name">Hadir (Total):</span> <span
                                    class="summary-count">{{ $grandTotalHadir }}</span></div>
                        </td>
                    </tr>
                </table>

        </div>
        @else
        <p style="text-align:center; font-style:italic;">Belum ada sesi apel atau data kehadiran untuk tanggal dan tipe
            yang dipilih.</p>
        @endif

        <div class="footer">
            Dicetak pada: {{ $reportDate }}
        </div>
    </div>
</body>

</html>