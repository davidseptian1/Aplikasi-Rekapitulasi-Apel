<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApelAttendance;
use App\Models\ApelSession;
use App\Models\Subdis;
use App\Models\User;
use App\Models\Keterangan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF; // For PDF export
use Illuminate\Support\Str;

class LaporanPersonelController extends Controller
{
    public function laporanKeterangan(Request $request)
    {
        $selectedDate = $request->input('date', now()->format('Y-m-d'));
        $selectedSubdisId = $request->input('subdis_id'); // Optional
        $selectedKeteranganId = $request->input('keterangan_id'); // Optional

        try {
            $filterDate = Carbon::parse($selectedDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $filterDate = now()->format('Y-m-d');
        }

        $subdisList = Subdis::orderBy('name')->get();
        $masterKeterangans = Keterangan::orderBy('name')->get();

        // Base query for attendances on the selected date
        $attendancesQuery = ApelAttendance::join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
            ->join('users', 'apel_attendances.user_id', '=', 'users.id')
            ->join('keterangans', 'apel_attendances.keterangan_id', '=', 'keterangans.id')
            ->whereDate('apel_sessions.date', $filterDate)
            ->with(['user.biodata.pangkat', 'keterangan', 'session.subdis']); // session.subdis for subdis name if needed

        if ($selectedSubdisId) {
            $attendancesQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
        }

        if ($selectedKeteranganId) {
            $attendancesQuery->where('apel_attendances.keterangan_id', $selectedKeteranganId);
        }

        // Select fields needed for the report
        $attendancesQuery->select(
            'users.name as user_name',
            'users.nrp as user_nrp', // Assuming NRP is relevant
            'keterangans.name as keterangan_name',
            'keterangans.id as keterangan_id', // For grouping
            // Add other fields if needed, e.g., from biodata, session
            'apel_sessions.type as apel_type',
            'apel_sessions.subdis_id as subdis_id' // For grouping by subdis if needed later
        );

        // Get all attendances and group them in PHP.
        // For large datasets, consider database grouping if performance becomes an issue.
        $allAttendances = $attendancesQuery->orderBy('keterangans.name')->orderBy('users.name')->get();

        // Group by keterangan_name for card display
        $groupedAttendances = $allAttendances->groupBy('keterangan_name');

        // If a specific keterangan is filtered, only pass that group
        if ($selectedKeteranganId) {
            $filteredKeterangan = $masterKeterangans->find($selectedKeteranganId);
            if ($filteredKeterangan) {
                $groupedAttendances = $groupedAttendances->only($filteredKeterangan->name);
            }
        }


        $data = [
            'title' => 'Laporan Keterangan Personel',
            'pages' => 'Laporan Keterangan',
            'filterDate' => $filterDate,
            'selectedSubdisId' => $selectedSubdisId,
            'selectedKeteranganId' => $selectedKeteranganId,
            'subdisList' => $subdisList,
            'masterKeterangans' => $masterKeterangans, // For filter dropdown
            'groupedAttendances' => $groupedAttendances, // Data grouped by keterangan_name
            'totalRecords' => $allAttendances->count() // For pagination info, though pagination is complex for this card layout
        ];

        return view('backend.laporan_personel.keterangan', $data);
    }

    public function cetakPdfKeterangan(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'subdis_id' => 'nullable|exists:subdis,id',
            'keterangan_id' => 'nullable|exists:keterangans,id',
        ]);

        $filterDate = $request->date;
        $selectedSubdisId = $request->subdis_id;
        $selectedKeteranganId = $request->keterangan_id;

        $subdisList = Subdis::orderBy('name')->get(); // For displaying selected subdis name
        $masterKeterangans = Keterangan::orderBy('name')->get();

        $attendancesQuery = ApelAttendance::join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
            ->join('users', 'apel_attendances.user_id', '=', 'users.id')
            ->leftJoin('biodatas', 'users.id', '=', 'biodatas.user_id') // Use leftJoin if biodata/pangkat might be null
            ->leftJoin('pangkats', 'biodatas.pangkat_id', '=', 'pangkats.id')
            ->join('keterangans', 'apel_attendances.keterangan_id', '=', 'keterangans.id')
            ->whereDate('apel_sessions.date', $filterDate)
            ->with(['session.subdis']);

        if ($selectedSubdisId) {
            $attendancesQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
        }
        if ($selectedKeteranganId) {
            $attendancesQuery->where('apel_attendances.keterangan_id', $selectedKeteranganId);
        }

        $attendancesQuery->select(
            'users.name as user_name',
            'pangkats.name as pangkat_name', // Get pangkat name
            'keterangans.name as keterangan_name',
            'keterangans.id as keterangan_id',
            'apel_sessions.type as apel_type',
            'apel_sessions.subdis_id'
        );

        $allAttendances = $attendancesQuery->orderBy('keterangans.name')->orderBy('users.name')->get();
        $groupedAttendances = $allAttendances->groupBy('keterangan_name');

        $currentSubdis = $selectedSubdisId ? $subdisList->find($selectedSubdisId) : null;
        $currentKeterangan = $selectedKeteranganId ? $masterKeterangans->find($selectedKeteranganId) : null;

        $data = [
            'filterDate' => $filterDate,
            'groupedAttendances' => $groupedAttendances,
            'masterKeterangans' => $masterKeterangans, // To list all keterangan types even if no data
            'currentSubdisName' => $currentSubdis ? $currentSubdis->name : 'Semua Subdis',
            'currentKeteranganName' => $currentKeterangan ? $currentKeterangan->name : 'Semua Keterangan',
            'reportDate' => Carbon::now()->translatedFormat('d F Y, H:i:s'),
        ];

        $pdf = PDF::loadView('backend.laporan_personel.keterangan_pdf', $data);
        $pdf->setPaper('a4', 'portrait'); // Or landscape if needed
        $fileName = 'laporan-keterangan-' . $filterDate . '-' . Str::slug($data['currentSubdisName']) . '.pdf';
        return $pdf->download($fileName);
    }
}
