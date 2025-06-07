<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApelAttendance;
use App\Models\Subdis;
use App\Models\Keterangan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class GrafikKehadiranController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input rentang tanggal, dengan default bulan ini
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $selectedSubdisId = $request->input('subdis_id'); // Bisa null untuk "Semua Subdis"

        // Validasi tanggal untuk keamanan
        try {
            $filterStartDate = Carbon::parse($startDate)->format('Y-m-d');
            $filterEndDate = Carbon::parse($endDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $filterStartDate = now()->startOfMonth()->format('Y-m-d');
            $filterEndDate = now()->endOfMonth()->format('Y-m-d');
        }

        $subdisList = Subdis::orderBy('name')->get();
        $masterKeterangans = Keterangan::orderBy('id')->get();

        // --- Data untuk "Total Keterangan Kehadiran" (Bar Chart) & "Persentase Kehadiran" (Pie Chart) ---
        $keteranganQuery = ApelAttendance::join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
            ->join('keterangans', 'apel_attendances.keterangan_id', '=', 'keterangans.id')
            // Gunakan whereBetween untuk rentang tanggal
            ->whereBetween('apel_sessions.date', [$filterStartDate, $filterEndDate])
            ->select('keterangans.name as keterangan_name', DB::raw('count(apel_attendances.id) as total'))
            ->groupBy('keterangans.name')
            ->orderBy('keterangans.id');

        if ($selectedSubdisId) {
            $keteranganQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
        }
        $keteranganCounts = $keteranganQuery->pluck('total', 'keterangan_name');

        $chartKeteranganLabels = [];
        $chartKeteranganData = [];
        $totalAttendanceRecords = 0;
        foreach ($masterKeterangans as $ket) {
            $chartKeteranganLabels[] = $ket->name;
            $count = $keteranganCounts->get($ket->name, 0);
            $chartKeteranganData[] = $count;
            $totalAttendanceRecords += $count;
        }

        // --- DATA BARU: "Tren Kehadiran Harian" (Line Chart) ---
        $hadirKeteranganId = Keterangan::where('name', 'LIKE', 'Hadir%')->first()?->id;

        $trenHarianQuery = ApelAttendance::join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
            ->whereBetween('apel_sessions.date', [$filterStartDate, $filterEndDate])
            ->select(DB::raw('DATE(apel_sessions.date) as tanggal'), DB::raw('count(apel_attendances.id) as total_hadir'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc');

        if ($hadirKeteranganId) {
            $trenHarianQuery->where('apel_attendances.keterangan_id', $hadirKeteranganId);
        }
        if ($selectedSubdisId) {
            $trenHarianQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
        }
        $trenHarianDataRaw = $trenHarianQuery->get()->keyBy('tanggal');

        // Siapkan data untuk line chart, pastikan semua hari dalam rentang ada untuk menghindari garis putus
        $period = CarbonPeriod::create($filterStartDate, $filterEndDate);
        $chartTrenHarianLabels = [];
        $chartTrenHarianData = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $chartTrenHarianLabels[] = $date->format('d M'); // Format label (e.g., 01 Jun)
            $chartTrenHarianData[] = $trenHarianDataRaw->get($dateString, (object)['total_hadir' => 0])->total_hadir;
        }


        $data = [
            'title' => 'Grafik Kehadiran',
            'pages' => 'Grafik Kehadiran',
            'startDate' => $filterStartDate, // Kirim start date
            'endDate' => $filterEndDate,     // Kirim end date
            'selectedSubdisId' => $selectedSubdisId,
            'subdisList' => $subdisList,
            'chartKeteranganLabels' => $chartKeteranganLabels,
            'chartKeteranganData' => $chartKeteranganData,
            'totalAttendanceRecordsForPie' => $totalAttendanceRecords,
            // Data untuk Chart Baru
            'chartTrenHarianLabels' => $chartTrenHarianLabels,
            'chartTrenHarianData' => $chartTrenHarianData,
        ];

        return view('backend.grafik_kehadiran.index', $data);
    }
}
