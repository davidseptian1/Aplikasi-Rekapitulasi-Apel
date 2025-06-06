<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApelAttendance;
use App\Models\ApelSession;
use App\Models\Subdis;
use App\Models\User;
use App\Models\Keterangan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrafikKehadiranController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', now()->format('Y-m-d'));
        $selectedSubdisId = $request->input('subdis_id'); // Can be null for "All Subdis"

        try {
            $filterDate = Carbon::parse($selectedDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $filterDate = now()->format('Y-m-d');
        }

        $subdisList = Subdis::orderBy('name')->get();
        $masterKeterangans = Keterangan::orderBy('id')->get(); // For consistent chart label order

        // --- Data for "Total Keterangan Kehadiran" (Bar Chart) & "Persentase Kehadiran" (Pie Chart) ---
        $keteranganQuery = ApelAttendance::join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
            ->join('keterangans', 'apel_attendances.keterangan_id', '=', 'keterangans.id')
            ->whereDate('apel_sessions.date', $filterDate)
            ->select('keterangans.name as keterangan_name', DB::raw('count(apel_attendances.id) as total'))
            ->groupBy('keterangans.name')
            ->orderBy('keterangans.id'); // Consistent order

        if ($selectedSubdisId) {
            $keteranganQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
        }
        $keteranganCounts = $keteranganQuery->pluck('total', 'keterangan_name');

        // Ensure all master keterangan types are present, even if count is 0
        $chartKeteranganLabels = [];
        $chartKeteranganData = [];
        $totalAttendanceRecords = 0;
        foreach ($masterKeterangans as $ket) {
            $chartKeteranganLabels[] = $ket->name;
            $count = $keteranganCounts->get($ket->name, 0);
            $chartKeteranganData[] = $count;
            $totalAttendanceRecords += $count;
        }

        // --- Data for "Rekap Kehadiran Berdasarkan Hadir dan Tidak Hadir" (Bar Chart) ---
        $hadirKeteranganId = Keterangan::where('name', 'LIKE', 'Hadir%')->first()?->id; // Assuming 'Hadir' is the primary presence status
        $hadirCount = 0;

        $hadirQuery = ApelAttendance::join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
            ->whereDate('apel_sessions.date', $filterDate);
        if ($hadirKeteranganId) {
            $hadirQuery->where('apel_attendances.keterangan_id', $hadirKeteranganId);
        } else {
            // If no "Hadir" keterangan, this chart might not be meaningful or needs adjustment
            Log::warning('Keterangan "Hadir" not found for Grafik Kehadiran.');
        }

        if ($selectedSubdisId) {
            $hadirQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
            $totalPersonelInScope = User::where('role', 'personil')->where('subdis_id', $selectedSubdisId)->where('is_active', '1')->count();
        } else {
            $totalPersonelInScope = User::where('role', 'personil')->where('is_active', '1')->count();
        }
        $hadirCount = $hadirQuery->count();
        $tidakHadirCount = $totalPersonelInScope - $hadirCount;
        if ($tidakHadirCount < 0) $tidakHadirCount = 0; // Cannot be negative


        $data = [
            'title' => 'Grafik Kehadiran',
            'pages' => 'Grafik Kehadiran',
            'filterDate' => $filterDate,
            'selectedSubdisId' => $selectedSubdisId,
            'subdisList' => $subdisList,
            'chartKeteranganLabels' => $chartKeteranganLabels,
            'chartKeteranganData' => $chartKeteranganData,
            'totalAttendanceRecordsForPie' => $totalAttendanceRecords, // For pie chart percentages
            'chartHadirTidakHadirLabels' => ['Hadir', 'Tidak Hadir'],
            'chartHadirTidakHadirData' => [$hadirCount, $tidakHadirCount],
        ];

        return view('backend.grafik_kehadiran.index', $data);
    }
}
