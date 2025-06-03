<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keterangan Personel</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #555;
        }

        .filter-info {
            font-size: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .keterangan-section {
            margin-bottom: 15px;
        }

        .keterangan-title {
            font-size: 12px;
            font-weight: bold;
            background-color: #f0f2f5;
            padding: 6px;
            border: 1px solid #dee2e6;
            border-bottom: none;
            margin-bottom: 0;
        }

        table.personel-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            /* Space after each group's table */
        }

        table.personel-list th,
        table.personel-list td {
            border: 1px solid #ccc;
            padding: 5px 7px;
            text-align: left;
        }

        table.personel-list th {
            background-color: #f9f9f9;
            font-weight: normal;
            text-align: center;
        }

        table.personel-list td.no {
            width: 5%;
            text-align: center;
        }

        table.personel-list td.nama {
            width: 55%;
        }

        table.personel-list td.pangkat {
            width: 40%;
            text-align: center;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .footer {
            text-align: right;
            font-size: 8px;
            color: #777;
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Keterangan Personel</h1>
            <p>
                Subdis: {{ $currentSubdisName }} <br>
                Keterangan: {{ $currentKeteranganName }} <br>
                Untuk Tanggal: {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('l, d F Y') }}
            </p>
        </div>

        @if($groupedAttendances->isEmpty())
        <p class="no-data">Tidak ada data keterangan ditemukan untuk filter yang dipilih.</p>
        @else
        @foreach($groupedAttendances as $keteranganNameGroup => $attendancesInGroup)
        <div class="keterangan-section">
            <h4 class="keterangan-title">{{ $keteranganNameGroup }} (Total: {{ $attendancesInGroup->count() }})</h4>
            @if($attendancesInGroup->count() > 0)
            <table class="personel-list">
                <thead>
                    <tr>
                        <th class="no">No</th>
                        <th class="nama">Nama Personel</th>
                        <th class="pangkat">Pangkat</th>
                        {{-- <th class="apel-type">Apel</th> --}} {{-- Uncomment if you need Apel Type (Pagi/Sore) per
                        entry --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendancesInGroup as $index => $att)
                    <tr>
                        <td class="no">{{ $index + 1 }}</td>
                        <td class="nama">
                            {{ $att->user_name }}
                            @if($att->user_nrp) <small style="color:#555;">(NRP: {{ $att->user_nrp }})</small> @endif
                        </td>
                        <td class="pangkat">{{ $att->pangkat_name ?? '-' }}</td>
                        {{-- <td class="apel-type">{{ ucfirst($att->apel_type) }}</td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="no-data" style="border-top:none; padding:5px;">Tidak ada personel dengan keterangan ini.</p>
            @endif
        </div>
        @if(!$loop->last)
        {{-- <div class="page-break"></div> --}} {{-- Optional: page break after each keterangan group --}}
        @endif
        @endforeach
        @endif

        <div class="footer">
            Dicetak pada: {{ $reportDate }}
        </div>
    </div>
</body>

</html>