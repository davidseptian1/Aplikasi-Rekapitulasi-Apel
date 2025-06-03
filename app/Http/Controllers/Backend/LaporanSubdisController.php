<?php

namespace App\Http\Controllers\Backend;

use App\Models\Subdis;
use App\Models\Keterangan;
use App\Models\ApelSession;
use Illuminate\Support\Str;
// use App\Models\ApelAttendance; // Not directly used if fetching through session
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User; // Added User model
use Carbon\Carbon; // For date formatting
use PDF; // Assuming you're using barryvdh/laravel-dompdf
use Illuminate\Support\Facades\Auth; // If you need auth for reports

class LaporanSubdisController extends Controller
{
    public function cetakPdf(Request $request)
    {
        $request->validate([
            'subdis_id' => 'required|exists:subdis,id',
            'date' => 'required|date_format:Y-m-d',
            'type' => 'required|in:pagi,sore',
        ]);

        $date = $request->get('date');
        $type = $request->get('type');
        $subdisId = $request->get('subdis_id');

        $subdis = Subdis::findOrFail($subdisId);

        // Fetch all 'personil' role users for this subdis
        $allPersonil = User::where('subdis_id', $subdisId)
            ->where('role', 'personil')
            ->with(['biodata.pangkat', 'biodata.jabatan']) // Eager load biodata details
            ->orderBy('name') // Order by name for consistent listing
            ->get();

        // Fetch the specific ApelSession for the given subdis, date, and type
        // Also eager load its attendances and the related keterangan for each attendance
        $apelSession = ApelSession::where('subdis_id', $subdisId)
            ->whereDate('date', $date)
            ->where('type', $type)
            ->with(['attendances.keterangan', 'attendances.user']) // user on attendance is good for cross-check or if not listing allPersonil primarily
            ->first(); // Expecting only one session

        // Fetch all master Keterangan types for the summary section
        $masterKeterangans = Keterangan::orderBy('name')->get();

        // Prepare data for the view
        $data = [
            'subdis' => $subdis,
            'allPersonil' => $allPersonil,
            'apelSession' => $apelSession, // This will be null if no session exists
            'date' => $date,
            'type' => $type,
            'masterKeterangans' => $masterKeterangans,
            'reportDate' => Carbon::now()->translatedFormat('d F Y, H:i:s'), // For "Printed on"
        ];

        $pdf = PDF::loadView('backend.laporan_subdis.pdf', $data);

        // Set paper orientation to landscape if the table is wide
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'laporan-subdis-' . Str::slug($subdis->name) . '-' . $date . '-' . $type . '.pdf';
        return $pdf->download($fileName);
        // return $pdf->stream($fileName); // Or use stream() to display in browser
    }
}
