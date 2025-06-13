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
        $selectedType = $request->input('type', 'pagi');
        $selectedSubdisId = $request->input('subdis_id');

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
            ->where('apel_sessions.type', $selectedType)
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

        $data = [
            'title' => 'Grafik Kehadiran',
            'pages' => 'Grafik Kehadiran',
            'startDate' => $filterStartDate,
            'endDate' => $filterEndDate,
            'selectedType' => $selectedType,
            'selectedSubdisId' => $selectedSubdisId,
            'subdisList' => $subdisList,
            'chartKeteranganLabels' => $chartKeteranganLabels,
            'chartKeteranganData' => $chartKeteranganData,
            'totalAttendanceRecordsForPie' => $totalAttendanceRecords,
        ];

        return view('backend.grafik_kehadiran.index', $data);
    }
}
