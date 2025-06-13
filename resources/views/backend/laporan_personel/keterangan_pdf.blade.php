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

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        .main-table th {
            background-color: #f0f2f5;
            font-weight: bold;
            text-align: center;
        }

        .main-table td.no {
            width: 5%;
            text-align: center;
        }

        .main-table td.jenis-apel {
            text-align: center;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
            padding: 20px;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Keterangan Personel</h1>
            <p>
                Tanggal: {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('l, d F Y') }} <br>
                Jenis Apel: {{ ucfirst($filterType) }} <br>
                Subdis: {{ $currentSubdisName }} | Keterangan: {{ $currentKeteranganName }}
            </p>
        </div>

        @if($allAttendances->isEmpty())
        <p class="no-data">Tidak ada data ditemukan untuk filter yang dipilih.</p>
        @else
        <table class="main-table">
            <thead>
                <tr>
                    <th class="no">No</th>
                    <th>Nama Personel</th>
                    <th>Keterangan Kehadiran</th>
                    <th>Jenis Apel</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allAttendances as $index => $attendance)
                <tr>
                    <td class="no">{{ $index + 1 }}</td>
                    <td>{{ $attendance->user_name }}</td>
                    <td>{{ $attendance->keterangan_name }}</td>
                    <td class="jenis-apel">{{ ucfirst($attendance->apel_type) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="footer">
            Dicetak pada: {{ $reportDate }}
        </div>
    </div>
</body>

</html>