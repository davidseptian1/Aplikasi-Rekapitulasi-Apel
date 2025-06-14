<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApelAttendance;
use App\Models\Subdis;
use App\Models\Keterangan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF; // For PDF export
use Illuminate\Support\Str;

class LaporanPersonelController extends Controller
{
    public function laporanKeterangan(Request $request)
    {
        // Add the 'type' filter, defaulting to 'pagi'
        $selectedDate = $request->input('date', now()->format('Y-m-d'));
        $selectedType = $request->input('type', 'pagi'); // New filter
        $selectedSubdisId = $request->input('subdis_id');
        $selectedKeteranganId = $request->input('keterangan_id');

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
            ->where('apel_sessions.type', $selectedType) // Apply the new type filter
            ->with(['user.biodata.pangkat', 'keterangan', 'session.subdis']);

        if ($selectedSubdisId) {
            $attendancesQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
        }

        if ($selectedKeteranganId) {
            $attendancesQuery->where('apel_attendances.keterangan_id', $selectedKeteranganId);
        }

        // Get all matching records for the table display
        $allAttendances = $attendancesQuery->select(
            'users.name as user_name',
            'users.nrp as user_nrp',
            'keterangans.name as keterangan_name',
            'apel_sessions.type as apel_type'
        )
            ->orderBy('users.name')
            ->get();

        $data = [
            'title' => 'Laporan Keterangan Personel',
            'pages' => 'Laporan Kehadiran',
            'filterDate' => $filterDate,
            'selectedType' => $selectedType, // Pass type to the view
            'selectedSubdisId' => $selectedSubdisId,
            'selectedKeteranganId' => $selectedKeteranganId,
            'subdisList' => $subdisList,
            'masterKeterangans' => $masterKeterangans,
            'allAttendances' => $allAttendances, // Pass the flat collection for the table
        ];

        return view('backend.laporan_personel.keterangan', $data);
    }

    public function cetakPdfKeterangan(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'type' => 'required|in:pagi,sore', // Validate the new type filter
            'subdis_id' => 'nullable|exists:subdis,id',
            'keterangan_id' => 'nullable|exists:keterangans,id',
        ]);

        $filterDate = $request->date;
        $filterType = $request->type; // Get type for PDF
        $selectedSubdisId = $request->subdis_id;
        $selectedKeteranganId = $request->keterangan_id;

        $attendancesQuery = ApelAttendance::join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
            ->join('users', 'apel_attendances.user_id', '=', 'users.id')
            ->join('keterangans', 'apel_attendances.keterangan_id', '=', 'keterangans.id')
            ->whereDate('apel_sessions.date', $filterDate)
            ->where('apel_sessions.type', $filterType); // Apply type filter to PDF query

        if ($selectedSubdisId) {
            $attendancesQuery->where('apel_sessions.subdis_id', $selectedSubdisId);
        }
        if ($selectedKeteranganId) {
            $attendancesQuery->where('apel_attendances.keterangan_id', $selectedKeteranganId);
        }

        $allAttendances = $attendancesQuery->select(
            'users.name as user_name',
            'users.nrp as user_nrp',
            'keterangans.name as keterangan_name',
            'apel_sessions.type as apel_type'
        )
            ->orderBy('users.name')
            ->get();

        $currentSubdis = $selectedSubdisId ? Subdis::find($selectedSubdisId) : null;
        $currentKeterangan = $selectedKeteranganId ? Keterangan::find($selectedKeteranganId) : null;

        $data = [
            'filterDate' => $filterDate,
            'filterType' => $filterType, // Pass type to PDF view
            'allAttendances' => $allAttendances,
            'currentSubdisName' => $currentSubdis ? $currentSubdis->name : 'Semua Subdis',
            'currentKeteranganName' => $currentKeterangan ? $currentKeterangan->name : 'Semua Keterangan',
            'reportDate' => Carbon::now()->translatedFormat('d F Y, H:i:s'),
        ];

        $pdf = PDF::loadView('backend.laporan_personel.keterangan_pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        $fileName = 'laporan-keterangan-' . $filterDate . '-' . Str::slug($data['currentSubdisName']) . '.pdf';

        return $pdf->download($fileName);
    }
}
