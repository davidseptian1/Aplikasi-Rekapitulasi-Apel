<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Piket;
use App\Models\Subdis;
use App\Models\JamApel;
use App\Models\Keterangan;
use App\Models\ApelSession;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ApelAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PDF; // Assuming Barryvdh DOMPDF

class RekapApelController extends Controller
{
    /**
     * Display a listing of the resource (Rekap Apel Index Page).
     * Calculates and passes display statuses for each subdis session.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $type = $request->get('type', 'pagi');
        $currentUser = Auth::user();

        $subdisQuery = Subdis::withCount(['users as personil_count' => function ($query) {
            $query->where('role', 'personil');
        }])->with([
            'user', // Penanggung Jawab Subdis (Kasubdis)
            'apelSessions' => function ($query) use ($date, $type) {
                $query->whereDate('date', $date)
                    ->where('type', $type)
                    ->withCount([
                        'attendances', // Total ApelAttendance records for this specific session
                        'attendances as draft_attendances_count' => fn($q) => $q->where('status', 'draft'),
                        'attendances as submitted_attendances_count' => fn($q) => $q->where('status', 'submitted'),
                        'attendances as verified_attendances_count' => fn($q) => $q->where('status', 'verified'),
                        'attendances as done_attendances_count' => fn($q) => $q->where('status', 'done'),
                    ]);
            }
        ]);

        if ($currentUser->role === 'pokmin') {
            $subdisQuery->where('user_id', $currentUser->id);
        }

        $subdisList = $subdisQuery->orderBy('name')->get();

        $isAnySubdisActuallyTerverifikasi = false;
        $isAnySubdisInBlockingState = false;
        $countOfSubdisSelesai = 0;
        $countOfRelevantSubdis = 0;

        if ($subdisList->isEmpty() && Subdis::whereHas('users', fn($q) => $q->where('role', 'personil'))->count() > 0) {
            $isAnySubdisInBlockingState = true;
        }

        foreach ($subdisList as $sub) {
            $session = $sub->apelSessions->first();
            $sub->display_waktu_apel = '-';
            $sub->display_tanggal_apel = Carbon::parse($date)->translatedFormat('d M Y');
            $sub->display_status_text = 'Belum Ada Data';
            $sub->display_badge_class = 'badge-belum-ada-data';
            $totalPersonil = $sub->personil_count ?? 0;

            if ($totalPersonil > 0) {
                $countOfRelevantSubdis++;
            }

            if ($session) {
                $sub->display_waktu_apel = Carbon::parse($session->created_at)->translatedFormat('H:i');
                $sub->display_tanggal_apel = Carbon::parse($session->date)->translatedFormat('d M Y');
                $attendancesCount = $session->attendances_count ?? 0;
                $draftCount = $session->draft_attendances_count ?? 0;
                $submittedCount = $session->submitted_attendances_count ?? 0;
                $verifiedCount = $session->verified_attendances_count ?? 0;
                $doneCount = $session->done_attendances_count ?? 0;

                if ($totalPersonil == 0) {
                    $sub->display_status_text = 'Tidak Ada Anggota';
                    $sub->display_badge_class = 'badge-tidak-ada-anggota';
                } elseif ($attendancesCount < $totalPersonil) {
                    $sub->display_status_text = 'Sementara';
                    $sub->display_badge_class = 'badge-sementara';
                } elseif ($draftCount > 0) {
                    $sub->display_status_text = 'Sementara';
                    $sub->display_badge_class = 'badge-sementara';
                } elseif ($doneCount == $totalPersonil && $totalPersonil > 0) {
                    $sub->display_status_text = 'Selesai';
                    $sub->display_badge_class = 'badge-selesai';
                    if ($totalPersonil > 0) $countOfSubdisSelesai++;
                } elseif (($verifiedCount + $doneCount) == $totalPersonil && $totalPersonil > 0) {
                    $sub->display_status_text = 'Terverifikasi';
                    $sub->display_badge_class = 'badge-terverifikasi';
                } elseif ($submittedCount > 0) {
                    $sub->display_status_text = 'Terkirim';
                    $sub->display_badge_class = 'badge-terkirim';
                } else {
                    $sub->display_status_text = 'Perlu Dicek';
                    $sub->display_badge_class = 'badge-perlu-dicek';
                }
            }

            if ($totalPersonil > 0) {
                if ($sub->display_status_text === 'Terverifikasi') {
                    $isAnySubdisActuallyTerverifikasi = true;
                }
                if (in_array($sub->display_status_text, ['Sementara', 'Terkirim', 'Perlu Dicek', 'Belum Ada Data'])) {
                    $isAnySubdisInBlockingState = true;
                }
            }
        }

        $allDataCanBeMarkedAsDone = $isAnySubdisActuallyTerverifikasi && !$isAnySubdisInBlockingState;
        if (Subdis::whereHas('users', fn($q) => $q->where('role', 'personil'))->count() === 0) {
            $allDataCanBeMarkedAsDone = false;
        }
        if ($subdisList->isEmpty() && $countOfRelevantSubdis > 0) {
            $allDataCanBeMarkedAsDone = false;
        }

        $enableShareButtonGlobal = false; // Default to false
        if ($countOfRelevantSubdis > 0 && $countOfSubdisSelesai == $countOfRelevantSubdis && !$isAnySubdisInBlockingState && !$isAnySubdisActuallyTerverifikasi) {
            // Enable share if all relevant subdis are 'Selesai' and none are in intermediate states like 'Terverifikasi' or blocking states.
            $enableShareButtonGlobal = true;
        }
        if (Subdis::whereHas('users', fn($q) => $q->where('role', 'personil'))->count() === 0) { // No subdis with members
            $enableShareButtonGlobal = false; // Nothing to share
        }
        if ($subdisList->isEmpty() && $countOfRelevantSubdis > 0) { // View is empty for pokmin but global relevant subdis exist
            $enableShareButtonGlobal = false; // Pokmin cannot determine global share state from this view.
        }


        $data = [
            'title' => 'Rekap Apel',
            'pages' => 'Detail Rekap',
            'subdis' => $subdisList,
            'date' => $date,
            'type' => $type,
            'allDataCanBeMarkedAsDone' => $allDataCanBeMarkedAsDone,
            'enableShareButtonGlobal' => $enableShareButtonGlobal,
        ];

        return view('backend.rekap_apel.index', $data);
    }

    /**
     * Show detail rekap for a subdis (Laporan Subdis page).
     */
    /**
     * Display detail rekap for a specific subdis.
     * Now aligns with the structure of laporan_global.
     */
    public function showSubdis($id, Request $request)
    {
        $validated = $request->validate([
            'date' => 'sometimes|date_format:Y-m-d',
            'type' => 'sometimes|in:pagi,sore',
        ]);

        $filterDate = $validated['date'] ?? now()->format('Y-m-d');
        $filterType = $validated['type'] ?? 'pagi'; // Default to pagi if not specified
        $currentUser = Auth::user();

        $subdis = Subdis::withCount(['users as personil_count' => fn($q) => $q->where('role', 'personil')])
            ->with([
                'user:id,name', // Kasubdis
                'users' => function ($q_users) { // Personnel list for this subdis
                    $q_users->where('role', 'personil')->with('biodata.pangkat')->orderBy('name');
                },
                'apelSessions' => function ($q_session) use ($filterDate, $filterType) {
                    $q_session->whereDate('date', $filterDate)
                        ->where('type', $filterType)
                        ->with(['attendances.keterangan']);
                }
            ])->findOrFail($id);

        // Authorization check (ensure this uses your existing logic)
        if (!$this->checkSubdisAccess($currentUser, $subdis, false)) { // false as it's not the showAnggota page
            Log::warning('Unauthorized access attempt to showSubdis', ['user_id' => $currentUser->id, 'subdis_id' => $id]);
            abort(403, 'Anda tidak memiliki akses ke detail subdis ini.');
        }

        $masterKeterangans = Keterangan::orderBy('name')->get();

        $piketHariIni = Piket::whereDate('piket_date', $filterDate)
            ->with(['pajaga:id,name', 'bajagaFirst:id,name', 'bajagaSecond:id,name'])
            ->first();

        // Calculate aggregated totals for THIS subdis
        $keteranganTotalsSubdis = [];
        foreach ($masterKeterangans as $mk) {
            $keteranganTotalsSubdis[$mk->name] = 0;
        }
        $placeholders = ['TL' => 0, 'MPP' => 0, 'DIK' => 0, 'BP' => 0, 'Cuti LP' => 0]; // From image_3
        foreach ($placeholders as $name => $val) {
            if (!isset($keteranganTotalsSubdis[$name])) $keteranganTotalsSubdis[$name] = 0;
        }

        $totalPersonilThisSubdis = $subdis->personil_count;
        $totalAttendancesRecordedThisSubdis = 0;
        $session = $subdis->apelSessions->first();

        if ($session) {
            $totalAttendancesRecordedThisSubdis = $session->attendances->count();
            foreach ($session->attendances as $attendance) {
                if ($attendance->keterangan) {
                    $keteranganTotalsSubdis[$attendance->keterangan->name] = ($keteranganTotalsSubdis[$attendance->keterangan->name] ?? 0) + 1;
                } else {
                    if (!isset($keteranganTotalsSubdis['Tanpa Keterangan'])) $keteranganTotalsSubdis['Tanpa Keterangan'] = 0;
                    $keteranganTotalsSubdis['Tanpa Keterangan']++;
                }
            }
        }
        $totalKurangThisSubdis = $totalPersonilThisSubdis - $totalAttendancesRecordedThisSubdis;
        if (!isset($keteranganTotalsSubdis['Hadir'])) $keteranganTotalsSubdis['Hadir'] = 0;


        $data = [
            'title' => 'Laporan Subdis: ' . $subdis->name,
            'pages' => 'Laporan Subdis',
            'subdis' => $subdis, // The specific Subdis model with its users and session
            'apelSessionInstance' => $session, // Pass the specific session instance
            'filterDate' => $filterDate,
            'filterType' => $filterType,
            'masterKeterangans' => $masterKeterangans, // For summary structure
            'keteranganTotalsSubdis' => $keteranganTotalsSubdis,
            'totalPersonilSubdis' => $totalPersonilThisSubdis,
            'totalDirekapSubdis' => $totalAttendancesRecordedThisSubdis,
            'totalKurangSubdis' => $totalKurangThisSubdis > 0 ? $totalKurangThisSubdis : 0,
            'piketHariIni' => $piketHariIni,
        ];

        return view('backend.rekap_apel.show_subdis', $data);
    }

    /**
     * Generate PDF for a specific Subdis Laporan Rekap Apel.
     */
    public function cetakLaporanSubdisPdf(Request $request, $id) // $id is subdis_id
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'type' => 'required|in:pagi,sore',
        ]);

        $filterDate = $validated['date'];
        $filterType = $validated['type'];

        $subdis = Subdis::withCount(['users as personil_count' => fn($q) => $q->where('role', 'personil')])
            ->with([
                'user:id,name',
                'users' => function ($q_users) {
                    $q_users->where('role', 'personil')->with('biodata.pangkat')->orderBy('name');
                },
                'apelSessions' => function ($q_session) use ($filterDate, $filterType) {
                    $q_session->whereDate('date', $filterDate)
                        ->where('type', $filterType)
                        ->with(['attendances.keterangan', 'attendances.user.biodata.pangkat']);
                }
            ])->findOrFail($id);

        $masterKeterangans = Keterangan::orderBy('name')->get();
        $piketHariIni = Piket::whereDate('piket_date', $filterDate)
            ->with(['pajaga:id,name', 'bajagaFirst:id,name', 'bajagaSecond:id,name'])
            ->first();

        $keteranganTotalsSubdis = [];
        foreach ($masterKeterangans as $mk) {
            $keteranganTotalsSubdis[$mk->name] = 0;
        }
        $placeholders = ['TL' => 0, 'MPP' => 0, 'DIK' => 0, 'BP' => 0, 'Cuti LP' => 0];
        foreach ($placeholders as $name => $val) {
            if (!isset($keteranganTotalsSubdis[$name])) $keteranganTotalsSubdis[$name] = 0;
        }

        $totalPersonilThisSubdis = $subdis->personil_count;
        $totalAttendancesRecordedThisSubdis = 0;
        $session = $subdis->apelSessions->first();

        if ($session) {
            $totalAttendancesRecordedThisSubdis = $session->attendances->count();
            foreach ($session->attendances as $attendance) {
                if ($attendance->keterangan) {
                    $keteranganTotalsSubdis[$attendance->keterangan->name] = ($keteranganTotalsSubdis[$attendance->keterangan->name] ?? 0) + 1;
                } else {
                    if (!isset($keteranganTotalsSubdis['Tanpa Keterangan'])) $keteranganTotalsSubdis['Tanpa Keterangan'] = 0;
                    $keteranganTotalsSubdis['Tanpa Keterangan']++;
                }
            }
        }
        $totalKurangThisSubdis = $totalPersonilThisSubdis - $totalAttendancesRecordedThisSubdis;
        if (!isset($keteranganTotalsSubdis['Hadir'])) $keteranganTotalsSubdis['Hadir'] = 0;


        $dataForPdf = [
            'subdis' => $subdis, // Single subdis object
            'apelSessionInstance' => $session, // Single session object
            'filterDate' => $filterDate,
            'filterType' => $filterType,
            'keteranganTotalsSubdis' => $keteranganTotalsSubdis,
            'totalPersonilSubdis' => $totalPersonilThisSubdis,
            'totalDirekapSubdis' => $totalAttendancesRecordedThisSubdis,
            'totalKurangSubdis' => $totalKurangThisSubdis > 0 ? $totalKurangThisSubdis : 0,
            'piketHariIni' => $piketHariIni,
            'dicetakOleh' => Auth::user()->name,
            'timestampCetak' => Carbon::now()->translatedFormat('d F Y, H:i:s') . ' WIB',
        ];

        // Use a distinct PDF view or make the global one highly adaptable
        $pdf = PDF::loadView('backend.rekap_apel.laporan_subdis_pdf', $dataForPdf);
        $pdf->setPaper('a4', 'portrait'); // Portrait might be better for single subdis list
        $fileName = 'laporan-subdis-' . Str::slug($subdis->name) . '-' . $filterDate . '-' . $filterType . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Show detail apel anggota for a subdis (Rekap Personel page).
     */
    public function showAnggota($id, Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $type = $request->get('type', 'pagi');
        $currentUser = Auth::user();
        $subdis = Subdis::with('user')->findOrFail($id);

        if (!$this->checkSubdisAccess($currentUser, $subdis, true)) {
            Log::warning('Unauthorized access attempt to showAnggota',);
            return redirect()->route('dashboard.index')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $apelSession = ApelSession::firstOrCreate(
            ['date' => $date, 'type' => $type, 'subdis_id' => $id,],
            ['created_by' => $currentUser->id,]
        );

        // ==============================================================
        // START: PENAMBAHAN LOGIKA ATURAN WAKTU
        // ==============================================================

        // 1. Ambil pengaturan jam apel
        $jamApelSetting = JamApel::where('type', $type)->first();
        $now = Carbon::now();

        // 2. Inisialisasi variabel. Secara default, non-pokmin selalu bisa rekap.
        $pokminCanRekap = true;
        $rekapTimeMessage = '';

        // 3. Terapkan aturan HANYA untuk role 'pokmin'
        if ($currentUser->role === 'pokmin') {
            if ($jamApelSetting) {
                $startTime = Carbon::parse($jamApelSetting->start_time);
                $endTime = Carbon::parse($jamApelSetting->end_time);

                if ($now->isBetween($startTime, $endTime)) {
                    $pokminCanRekap = true;
                    $rekapTimeMessage = "Waktu rekap sedang berlangsung hingga pukul " . $endTime->format('H:i') . " WIB.";
                } elseif ($now->isBefore($startTime)) {
                    $pokminCanRekap = false;
                    $rekapTimeMessage = "Rekap Apel " . ucfirst($type) . " baru bisa dilakukan mulai pukul " . $startTime->format('H:i') . " WIB.";
                } else { // $now->isAfter($endTime)
                    $pokminCanRekap = false;
                    $rekapTimeMessage = "Waktu rekap Apel " . ucfirst($type) . " telah berakhir pada pukul " . $endTime->format('H:i') . " WIB.";
                }
            } else {
                // Jika pengaturan tidak ada, pokmin tidak bisa rekap
                $pokminCanRekap = false;
                $rekapTimeMessage = "Pengaturan jam untuk Apel " . ucfirst($type) . " belum diatur oleh Administrator.";
            }
        }
        // ==============================================================
        // END: PENAMBAHAN LOGIKA ATURAN WAKTU
        // ==============================================================

        $anggotas = User::where('subdis_id', $id)
            ->where('role', 'personil')
            ->with([
                'biodata.pangkat',
                'biodata.jabatan',
                'apelAttendances' => fn($q) => $q->where('apel_session_id', $apelSession->id)->with('keterangan')
            ])->orderBy('name')->get();

        $keterangans = Keterangan::orderBy('name')->get();

        $data = [
            'title' => 'Rekap Personel',
            'pages' => 'Rekap Personel',
            'subdis' => $subdis,
            'anggotas' => $anggotas,
            'keterangans' => $keterangans,
            'apelSession' => $apelSession,
            'date' => $date,
            'type' => $type,
            'pokminCanRekap' => $pokminCanRekap,
            'rekapTimeMessage' => $rekapTimeMessage,
        ];

        return view('backend.rekap_apel.show_anggota', $data);
    }

    /**
     * Helper for subdis access check.
     */
    private function checkSubdisAccess($user, $subdis, $isForShowAnggotaPage = false)
    {
        if (in_array($user->role, ['superadmin', 'piket'])) {
            return true;
        }
        if ($user->role === 'pokmin') {
            return $subdis->user_id == $user->id;
        }
        if ($user->role === 'personil' && !$isForShowAnggotaPage) {
            return $subdis->id == $user->subdis_id;
        }
        return false;
    }

    /**
     * Update keterangan for a single anggota. Only changes keterangan_id.
     */
    public function updateKeterangan(Request $request, $id)
    {
        $request->validate([
            'keterangan_id' => 'required|exists:keterangans,id',
            'apel_session_id' => 'required|exists:apel_sessions,id',
        ]);
        $userToUpdate = User::findOrFail($id);
        $apelSession = ApelSession::findOrFail($request->apel_session_id);
        $currentUser = Auth::user();

        if (!$this->canUpdateAttendance($currentUser, $userToUpdate, $apelSession)) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        $existingAttendance = ApelAttendance::where('apel_session_id', $request->apel_session_id)->where('user_id', $id)->first();
        if ($currentUser->role === 'pokmin' && $existingAttendance && in_array($existingAttendance->status, ['verified', 'done', 'submitted'])) {
            return response()->json(['success' => false, 'message' => 'Pokmin tidak dapat mengubah data yang sudah dikirim/diverifikasi/selesai.'], 403);
        }
        $attendance = ApelAttendance::updateOrCreate(
            ['apel_session_id' => $request->apel_session_id, 'user_id' => $id,],
            ['keterangan_id' => $request->keterangan_id,]
        );
        if (!$existingAttendance && !$attendance->status) {
            $attendance->status = 'draft';
            $attendance->save();
        }
        return response()->json(['success' => true, 'message' => 'Keterangan diperbarui.', 'data' => $attendance->load('keterangan')]);
    }

    /**
     * Authorization for updating individual attendance keterangan.
     */
    private function canUpdateAttendance($currentUser, $targetUser, $apelSession)
    {
        if (in_array($currentUser->role, ['superadmin', 'piket'])) {
            return true;
        }
        if ($currentUser->role === 'pokmin') {
            $subdisOfSession = $apelSession->subdis;
            if (!$subdisOfSession && $apelSession->subdis_id) {
                $subdisOfSession = Subdis::find($apelSession->subdis_id);
            }
            return $subdisOfSession && $subdisOfSession->user_id == $currentUser->id && $targetUser->subdis_id == $subdisOfSession->id;
        }
        return false;
    }

    /**
     * Submit attendances for a session (selected or all drafts).
     */
    public function submitSession($sessionId, Request $request)
    {
        $apelSession = ApelSession::with('subdis')->findOrFail($sessionId);
        $currentUser = Auth::user();
        if (!($currentUser->role === 'superadmin' || ($currentUser->role === 'pokmin' && $apelSession->subdis->user_id == $currentUser->id))) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        $query = ApelAttendance::where('apel_session_id', $sessionId)->where('status', 'draft')->whereNotNull('keterangan_id');
        $actionType = "semua";
        if ($request->has('user_ids') && is_array($request->user_ids) && count($request->user_ids) > 0) {
            $request->validate(['user_ids.*' => 'exists:users,id']);
            $query->whereIn('user_id', $request->user_ids);
            $actionType = "terpilih";
        }
        $updatedCount = $query->update(['status' => 'submitted', 'submitted_by' => $currentUser->id, 'submitted_at' => now(),]);
        if ($updatedCount > 0) {
            return response()->json(['success' => true, 'message' => "{$updatedCount} data {$actionType} berhasil dikirim."]);
        }
        return response()->json(['success' => false, 'message' => "Tidak ada data draft valid {$actionType} untuk dikirim."]);
    }

    /**
     * Update keterangan for multiple anggota and set status to 'draft'.
     */
    public function updateKeteranganBulk(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'keterangan_id' => 'required|exists:keterangans,id',
            'apel_session_id' => 'required|exists:apel_sessions,id',
            // action parameter removed, this method now only sets to draft
        ]);
        $apelSession = ApelSession::with('subdis')->findOrFail($request->apel_session_id);
        $currentUser = Auth::user();
        if (!($currentUser->role === 'superadmin' || ($currentUser->role === 'pokmin' && $apelSession->subdis->user_id == $currentUser->id))) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        $updatedCount = 0;
        $skippedCount = 0;
        DB::transaction(function () use ($request, &$updatedCount, &$skippedCount) {
            foreach ($request->user_ids as $userId) {
                $existingAttendance = ApelAttendance::where('apel_session_id', $request->apel_session_id)->where('user_id', $userId)->first();
                if (Auth::user()->role === 'pokmin' && $existingAttendance && in_array($existingAttendance->status, ['verified', 'done', 'submitted'])) {
                    $skippedCount++;
                    continue;
                }
                ApelAttendance::updateOrCreate(
                    ['apel_session_id' => $request->apel_session_id, 'user_id' => $userId,],
                    [
                        'keterangan_id' => $request->keterangan_id,
                        'status' => 'draft',
                        'submitted_by' => null,
                        'submitted_at' => null,
                        'verified_by' => null,
                        'verified_at' => null,
                    ]
                );
                $updatedCount++;
            }
        });
        $message = "";
        if ($updatedCount > 0) $message .= "Keterangan diterapkan sebagai draft untuk {$updatedCount} anggota.";
        if ($skippedCount > 0) $message .= ($updatedCount > 0 ? " " : "") . "{$skippedCount} anggota dilewati (status final).";
        if ($updatedCount == 0 && $skippedCount > 0) $message = "Tidak ada anggota yang keterangannya dapat diubah ke draft (semua {$skippedCount} anggota terpilih sudah final).";
        else if ($updatedCount == 0 && $skippedCount == 0 && count($request->user_ids) > 0) $message = "Tidak ada anggota yang diproses (kemungkinan semua sudah final atau tidak ada yang dipilih).";
        else if ($updatedCount == 0 && $skippedCount == 0) $message = "Tidak ada anggota yang dipilih atau diproses.";


        return response()->json(['success' => ($updatedCount > 0), 'message' => $message]);
    }

    /**
     * Verify all SUBMITTED attendances for a session.
     */
    public function verifySession($sessionId)
    {
        $apelSession = ApelSession::findOrFail($sessionId);
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['superadmin', 'piket'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        $verifiedCount = ApelAttendance::where('apel_session_id', $sessionId)->where('status', 'submitted')
            ->update(['status' => 'verified', 'verified_by' => $currentUser->id, 'verified_at' => now(),]);
        if ($verifiedCount > 0) {
            return response()->json(['success' => true, 'message' => "{$verifiedCount} data berhasil diverifikasi."]);
        }
        return response()->json(['success' => false, 'message' => "Tidak ada data menunggu verifikasi."]);
    }

    /**
     * Mark all VERIFIED attendances as DONE for a specific date and type.
     */
    public function markAsDone(Request $request)
    {
        $request->validate(['date' => 'required|date_format:Y-m-d', 'type' => 'required|in:pagi,sore']);
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['superadmin', 'piket'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $subdisWithPersonnelIds = Subdis::whereHas('users', fn($q) => $q->where('role', 'personil'))->pluck('id');
        if ($subdisWithPersonnelIds->isEmpty()) {
            $message = Subdis::count() === 0 ? 'Tidak ada subdis dalam sistem.' : 'Tidak ada subdis dengan personil untuk diselesaikan.';
            return response()->json(['success' => true, 'message' => $message]);
        }

        $sessionsToConsider = ApelSession::whereDate('date', $request->date)
            ->where('type', $request->type)->whereIn('subdis_id', $subdisWithPersonnelIds)
            ->with('subdis:id,name')
            ->withCount([
                'attendances as non_verified_done_attendances' => fn($q) => $q->whereNotIn('status', ['verified', 'done']),
                'attendances as recorded_attendances_count'
            ])->get();

        if ($sessionsToConsider->count() < $subdisWithPersonnelIds->count()) {
            $missingSubdisNames = Subdis::whereIn('id', $subdisWithPersonnelIds)
                ->whereNotIn('id', $sessionsToConsider->pluck('subdis_id'))
                ->pluck('name')->implode(', ');
            return response()->json(['success' => false, 'message' => "Gagal: Sesi apel belum dibuat untuk semua subdis ({$missingSubdisNames})."]);
        }

        foreach ($sessionsToConsider as $session) {
            $totalPersonilInSubdis = Subdis::find($session->subdis_id)->users()->where('role', 'personil')->count();
            if ($totalPersonilInSubdis > 0) {
                if ($session->non_verified_done_attendances_count > 0 || $session->recorded_attendances_count < $totalPersonilInSubdis) {
                    return response()->json(['success' => false, 'message' => "Gagal: Data belum lengkap/terverifikasi untuk semua anggota di Subdis: " . $session->subdis->name]);
                }
            }
        }

        $updatedCount = ApelAttendance::whereIn('apel_session_id', $sessionsToConsider->pluck('id'))
            ->where('status', 'verified')->update(['status' => 'done']);

        if ($updatedCount > 0) {
            return response()->json(['success' => true, 'message' => "{$updatedCount} data rekap apel berhasil diselesaikan."]);
        }
        return response()->json(['success' => true, 'message' => 'Tidak ada data terverifikasi untuk diselesaikan atau sudah selesai.']);
    }

    private function determineSessionDisplayStatus($session, $totalPersonil)
    {
        if ($totalPersonil == 0) {
            return ['text' => 'Tidak Ada Anggota', 'badge' => 'badge-tidak-ada-anggota'];
        }
        if (!$session) {
            return ['text' => 'Belum Ada Sesi', 'badge' => 'badge-belum-ada-data'];
        }

        $attendancesCount = $session->attendances_count ?? 0;
        $draftCount = $session->draft_attendances_count ?? 0;
        $submittedCount = $session->submitted_attendances_count ?? 0;
        $verifiedCount = $session->verified_attendances_count ?? 0;
        $doneCount = $session->done_attendances_count ?? 0;

        if ($attendancesCount < $totalPersonil) {
            return ['text' => 'Sementara', 'badge' => 'badge-sementara'];
        }
        if ($draftCount > 0) {
            return ['text' => 'Sementara', 'badge' => 'badge-sementara'];
        }
        if ($doneCount == $totalPersonil && $totalPersonil > 0) {
            return ['text' => 'Selesai', 'badge' => 'badge-selesai'];
        }
        if (($verifiedCount + $doneCount) == $totalPersonil && $totalPersonil > 0) {
            return ['text' => 'Terverifikasi', 'badge' => 'badge-terverifikasi'];
        }
        if ($submittedCount > 0) {
            return ['text' => 'Terkirim', 'badge' => 'badge-terkirim'];
        }
        return ['text' => 'Perlu Dicek', 'badge' => 'badge-perlu-dicek'];
    }

    // --- NEW METHODS FOR GLOBAL LAPORAN ---
    public function showLaporanGlobal(Request $request)
    {
        $validated = $request->validate([
            'date' => 'sometimes|date_format:Y-m-d',
            'type' => 'sometimes|in:pagi,sore',
            'subdis_id' => 'nullable|exists:subdis,id'
        ]);

        $filterDate = $validated['date'] ?? now()->format('Y-m-d');
        $filterType = $validated['type'] ?? 'pagi';
        $filterSubdisId = $validated['subdis_id'] ?? null;
        $todayCarbon = Carbon::parse($filterDate); // Use filterDate for consistency

        $subdisListFilter = Subdis::orderBy('name')->get(['id', 'name']);
        $appName = config('app.name', 'INSTANSI XYZ'); // Get app name or use a default

        $query = Subdis::query();
        if ($filterSubdisId) {
            $query->where('id', $filterSubdisId);
        }

        $subdisData = $query->withCount(['users as personil_count' => fn($q) => $q->where('role', 'personil')])
            ->with([
                'user:id,name',
                'users' => function ($q_users) {
                    $q_users->where('role', 'personil')
                        ->with(['biodata.pangkat', 'biodata.jabatan']) // Load jabatan too for completeness
                        ->orderBy('name');
                },
                'apelSessions' => function ($q_session) use ($filterDate, $filterType) {
                    $q_session->whereDate('date', $filterDate)
                        ->where('type', $filterType)
                        ->with(['attendances.keterangan', 'attendances.user']); // User on attendance needed for list
                }
            ])
            ->orderBy('name')
            ->get();

        $masterKeterangans = Keterangan::orderBy('id')->get(); // Order by ID or a specific display order column

        $piketHariIni = Piket::whereDate('piket_date', $filterDate)
            ->with(['pajaga:id,name', 'bajagaFirst:id,name', 'bajagaSecond:id,name'])
            ->first();

        // --- Data Aggregation for Report and Message ---
        $grandTotals = [];
        // Initialize with all master keterangan AND specific placeholders from image
        foreach ($masterKeterangans as $mk) {
            $grandTotals[$mk->name] = 0;
        }
        $placeholders = ['TL' => 0, 'MPP' => 0, 'DIK' => 0, 'BP' => 0, 'Cuti LP' => 0, 'Tanpa Keterangan' => 0];
        foreach ($placeholders as $name => $val) {
            if (!isset($grandTotals[$name])) $grandTotals[$name] = 0;
        }
        if (!isset($grandTotals['Hadir'])) $grandTotals['Hadir'] = 0;


        $allPersonnelInScope = 0;
        $totalAttendancesRecorded = 0;
        $personelByKeteranganForMessage = []; // To store names for the message

        foreach ($subdisData as $sub) {
            $allPersonnelInScope += $sub->personil_count;
            $session = $sub->apelSessions->first();
            if ($session) {
                $totalAttendancesRecorded += $session->attendances->count();
                foreach ($session->attendances as $attendance) {
                    if ($attendance->keterangan) {
                        $kName = $attendance->keterangan->name;
                        $grandTotals[$kName] = ($grandTotals[$kName] ?? 0) + 1;

                        // For the WhatsApp/Email message "KETERANGAN :" part
                        if (strtolower($kName) !== 'hadir') { // Typically list non-hadir names
                            $userNameWithSubdis = $attendance->user->name;
                            if (!$filterSubdisId) { // If showing all subdis, add subdis name to user
                                $userNameWithSubdis .= " (" . $sub->name . ")";
                            }
                            $personelByKeteranganForMessage[$kName][] = $userNameWithSubdis;
                        }
                    } else {
                        $grandTotals['Tanpa Keterangan']++;
                        $userNameWithSubdis = $attendance->user->name;
                        if (!$filterSubdisId) {
                            $userNameWithSubdis .= " (" . $sub->name . ")";
                        }
                        $personelByKeteranganForMessage['Tanpa Keterangan'][] = $userNameWithSubdis;
                    }
                }
            }
        }
        $grandTotalKurang = $allPersonnelInScope - $totalAttendancesRecorded;
        $hadirActual = $grandTotals['Hadir'] ?? 0;

        // --- Construct the Message for WA/Email ---
        $jamApelWIB = ($filterType === 'pagi') ? "07:00" : "16:00"; // Example, adjust as needed

        $pesan = "Selamat " . (Carbon::now()->hour < 12 ? "Pagi" : (Carbon::now()->hour < 18 ? "Siang" : "Malam")) . ",\n\n";
        $pesan .= "Mohon izin melaporkan kekuatan Apel " . ucfirst($filterType) .
            " Personel " . $appName .
            ($filterSubdisId && $subdisData->first() ? " Subdis " . $subdisData->first()->name : " Keseluruhan") .
            " pada hari " . $todayCarbon->translatedFormat('l, d F Y') .
            " jam " . $jamApelWIB . " WIB:\n";
        $pesan .= "Jumlah: *" . $allPersonnelInScope . "*\n";
        $pesan .= "Kurang: *" . ($grandTotalKurang > 0 ? $grandTotalKurang : 0) . "*\n";
        $pesan .= "Hadir: *" . $hadirActual . "*\n\n";

        $pesan .= "Ket :\n";
        foreach ($grandTotals as $keterangan => $jumlah) {
            // Only list keterangan with counts or standard ones from image
            if ($jumlah > 0 || in_array($keterangan, ['Hadir', 'BP', 'Cuti', 'Ijin', 'DL', 'Sakit', 'Tanpa Keterangan'])) {
                // Match image_11 format, "DL: 0" for example
                if (in_array($keterangan, ['Dinas Luar'])) $keterangan = 'DL'; // Abbreviate if needed
                $pesan .= ucfirst(strtolower($keterangan)) . ": " . $jumlah . "\n";
            }
        }
        $pesan .= "\n";

        if (count($personelByKeteranganForMessage) > 0) {
            $pesan .= "KETERANGAN :\n";
            foreach ($personelByKeteranganForMessage as $keterangan => $personels) {
                if (count($personels) > 0) {
                    // Abbreviate for message if needed
                    if (in_array($keterangan, ['Dinas Luar'])) $keterangan = 'DL';
                    $pesan .= ucfirst(strtolower($keterangan)) . ": " . count($personels) . "\n";
                    foreach ($personels as $namaPersonel) {
                        $pesan .= "- " . $namaPersonel . "\n";
                    }
                }
            }
            $pesan .= "\n";
        }

        $pesan .= "Jadwal Jaga:\n";
        if ($piketHariIni) {
            $pesan .= "Pajaga: " . ($piketHariIni->pajaga?->name ?? "Tidak ada") . "\n";
            $pesan .= "Bajaga: " . ($piketHariIni->bajagaFirst?->name ?? "Tidak ada") . "\n"; // Image shows "Bajaga", not "Bajaga I"
            $pesan .= "Jaga Tariat: " . ($piketHariIni->bajagaSecond?->name ?? "Tidak ada") . "\n";
        } else {
            $pesan .= "Informasi piket tidak tersedia untuk tanggal ini.\n";
        }
        $pesan .= "\nTerima kasih.";

        $data = [
            'title' => 'Laporan Global Rekap Apel',
            'pages' => 'Laporan Global Rekap Apel',
            'subdisData' => $subdisData,
            'filterDate' => $filterDate,
            'filterType' => $filterType,
            'filterSubdisId' => $filterSubdisId,
            'subdisListFilter' => $subdisListFilter,
            'masterKeterangans' => $masterKeterangans,
            'grandTotals' => $grandTotals,
            'totalPersonilKeseluruhan' => $allPersonnelInScope,
            'totalDirekapKeseluruhan' => $totalAttendancesRecorded,
            'totalKurangKeseluruhan' => $grandTotalKurang > 0 ? $grandTotalKurang : 0,
            'piketHariIni' => $piketHariIni,
            'pesanUntukBagikan' => $pesan, // Pass the formatted message to the view
            'subjectEmailBagikan' => "Laporan Global Rekap Apel - " . $todayCarbon->translatedFormat('d M Y') . " - " . ucfirst($filterType),
        ];

        return view('backend.rekap_apel.laporan_global', $data);
    }

    public function cetakLaporanGlobalPdf(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'type' => 'required|in:pagi,sore',
            'subdis_id' => 'nullable|exists:subdis,id' // subdis_id is optional
        ]);

        $filterDate = $validated['date'];
        $filterType = $validated['type'];
        $filterSubdisId = $validated['subdis_id'] ?? null; // Ensure it's null if not present

        $subdisQuery = Subdis::query();
        $selectedSubdisNameForTitle = 'Semua Subdis'; // Default title part

        if ($filterSubdisId) {
            $subdisQuery->where('id', $filterSubdisId);
            // Fetch the name for the title/filename if a specific subdis is filtered
            $filteredSubdisModel = Subdis::find($filterSubdisId);
            if ($filteredSubdisModel) {
                $selectedSubdisNameForTitle = $filteredSubdisModel->name;
            } else {
                $selectedSubdisNameForTitle = 'Subdis ID: ' . $filterSubdisId; // Fallback if somehow not found post-validation
            }
        }

        $subdisData = $subdisQuery->withCount(['users as personil_count' => fn($q) => $q->where('role', 'personil')])
            ->with([
                'user:id,name',
                'users' => function ($q_users) {
                    $q_users->where('role', 'personil')->with('biodata.pangkat')->orderBy('name');
                },
                'apelSessions' => function ($q_session) use ($filterDate, $filterType) {
                    $q_session->whereDate('date', $filterDate)
                        ->where('type', $filterType)
                        ->with(['attendances.keterangan', 'attendances.user.biodata.pangkat']);
                }
            ])
            ->orderBy('name')
            ->get();

        $masterKeterangans = Keterangan::orderBy('name')->get();
        $piketHariIni = Piket::whereDate('piket_date', $filterDate)
            ->with(['pajaga:id,name', 'bajagaFirst:id,name', 'bajagaSecond:id,name'])
            ->first();

        $grandTotals = [];
        foreach ($masterKeterangans as $mk) {
            $grandTotals[$mk->name] = 0;
        }
        $placeholders = ['TL' => 0, 'MPP' => 0, 'DIK' => 0, 'BP' => 0, 'Cuti LP' => 0];
        foreach ($placeholders as $name => $val) {
            if (!isset($grandTotals[$name])) $grandTotals[$name] = 0;
        }

        $allPersonnelInScope = 0;
        $totalAttendancesRecorded = 0;

        foreach ($subdisData as $sub) {
            $allPersonnelInScope += $sub->personil_count;
            $session = $sub->apelSessions->first();
            if ($session) {
                $totalAttendancesRecorded += $session->attendances->count();
                foreach ($session->attendances as $attendance) {
                    if ($attendance->keterangan) {
                        $grandTotals[$attendance->keterangan->name] = ($grandTotals[$attendance->keterangan->name] ?? 0) + 1;
                    } else {
                        if (!isset($grandTotals['Tanpa Keterangan'])) $grandTotals['Tanpa Keterangan'] = 0;
                        $grandTotals['Tanpa Keterangan']++;
                    }
                }
            }
        }
        $grandTotalKurang = $allPersonnelInScope - $totalAttendancesRecorded;
        if (!isset($grandTotals['Hadir'])) $grandTotals['Hadir'] = 0;

        $dataForPdf = [
            'subdisData' => $subdisData,
            'filterDate' => $filterDate,
            'filterType' => $filterType,
            'filterSubdisId' => $filterSubdisId, // Pass the original filterSubdisId
            'selectedSubdisName' => $selectedSubdisNameForTitle, // Use the fetched/default name
            'grandTotals' => $grandTotals,
            'totalPersonilKeseluruhan' => $allPersonnelInScope,
            'totalDirekapKeseluruhan' => $totalAttendancesRecorded,
            'totalKurangKeseluruhan' => $grandTotalKurang > 0 ? $grandTotalKurang : 0,
            'piketHariIni' => $piketHariIni,
            'dicetakOleh' => Auth::user()->name,
            'timestampCetak' => Carbon::now()->translatedFormat('d F Y, H:i:s') . ' WIB',
        ];

        $pdf = PDF::loadView('backend.rekap_apel.laporan_global_pdf', $dataForPdf);
        $pdf->setPaper('a4', 'landscape');

        $fileNameSubdisPart = $filterSubdisId ? Str::slug($selectedSubdisNameForTitle) : 'semua-subdis';
        $fileName = 'laporan-global-rekap-apel-' . $filterDate . '-' . $filterType . '-' . $fileNameSubdisPart . '.pdf';

        return $pdf->download($fileName);
    }
}
