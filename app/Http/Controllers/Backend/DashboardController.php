<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Piket;
use App\Models\Subdis;
use App\Models\JamApel;
use App\Models\ApelSession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // For distinct roles if no Role model
use Illuminate\Support\Str;      // For Str::slug if needed in controller
use App\Models\Keterangan; // Keep if used by other dashboard parts or future needs

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $today = now()->format('Y-m-d');
        $todayCarbon = Carbon::parse($today);

        $data = [
            'title' => 'Dashboard',
            'pages' => 'Dashboard',
            'today' => $today,
            'todayCarbon' => $todayCarbon,
            'currentTimeFormatted' => now()->translatedFormat('l, d F Y H:i') . ' WIB',
        ];

        $piketToday = Piket::with([
            'pajaga.biodata.pangkat',
            'bajagaFirst.biodata.pangkat',
            'bajagaSecond.biodata.pangkat',
            'creator'
        ])
            ->whereDate('piket_date', $today)
            ->first();
        $data['piketToday'] = $piketToday;
        $data['hasPiketToday'] = (bool)$piketToday;

        if (in_array($currentUser->role, ['superadmin', 'piket'])) {
            $data['usersForPiketSelection'] = User::where('is_active', '1')
                ->orderBy('name')->get(['id', 'name']);
        }

        switch ($currentUser->role) {
            case 'pokmin':
                $pokminSubdis = Subdis::where('user_id', $currentUser->id)->first();
                $data['pokminSubdis'] = $pokminSubdis;
                $data['apel_sessions_info_pokmin'] = [];

                // Ambil pengaturan jam apel
                $jamApelSettings = JamApel::all()->keyBy('type');
                $now = Carbon::now();

                if ($pokminSubdis) {
                    $personilCount = User::where('subdis_id', $pokminSubdis->id)->where('role', 'personil')->count();
                    foreach (['pagi', 'sore'] as $type) {
                        $session = ApelSession::where('subdis_id', $pokminSubdis->id)
                            ->whereDate('date', $today)
                            ->where('type', $type)
                            ->withCount([
                                'attendances',
                                'attendances as draft_count' => fn($q) => $q->where('status', 'draft'),
                                'attendances as submitted_count' => fn($q) => $q->where('status', 'submitted'),
                                'attendances as verified_count' => fn($q) => $q->where('status', 'verified'),
                                'attendances as done_count' => fn($q) => $q->where('status', 'done')
                            ])->first();

                        // Logika Pengecekan Waktu
                        $setting = $jamApelSettings->get($type);
                        $isActiveTime = false;
                        $timeMessage = "Jadwal belum diatur.";

                        if ($setting) {
                            $startTime = Carbon::parse($setting->start_time);
                            $endTime = Carbon::parse($setting->end_time);

                            if ($now->isBetween($startTime, $endTime)) {
                                $isActiveTime = true;
                                $timeMessage = "Waktu rekap sedang berlangsung hingga pukul " . $endTime->format('H:i') . " WIB.";
                            } elseif ($now->isBefore($startTime)) {
                                $timeMessage = "Rekap Apel " . ucfirst($type) . " baru bisa dilakukan mulai pukul " . $startTime->format('H:i') . " WIB.";
                            } else { // $now->isAfter($endTime)
                                $timeMessage = "Waktu rekap Apel " . ucfirst($type) . " telah berakhir pada pukul " . $endTime->format('H:i') . " WIB.";
                            }
                        }

                        $statusInfo = $this->determineSessionDisplayStatus($session, $personilCount);
                        $data['apel_sessions_info_pokmin'][] = [
                            'subdis_id' => $pokminSubdis->id,
                            'type' => $type,
                            'jam' => $session ? Carbon::parse($session->created_at)->format('H:i') : ($type === 'pagi' ? '07:00' : '15:00'),
                            'tanggal' => $todayCarbon->translatedFormat('d M Y'),
                            'keterangan_rekap' => $session ? (($session->attendances_count ?? 0) . '/' . $personilCount . ' direkap') : ($personilCount > 0 ? 'Belum ada sesi' : 'Tidak ada anggota'),
                            'status_text' => $statusInfo['text'],
                            'status_badge' => $statusInfo['badge'],
                            'needs_verification_action' => $session && $statusInfo['text'] === 'Terkirim',
                            'is_active_time' => $isActiveTime,
                            'time_message' => $timeMessage,
                        ];
                    }
                }
                break;

            case 'piket':
                if ($data['hasPiketToday']) {
                    $allSubdisForAdmin = Subdis::withCount(['users as personil_count' => fn($q) => $q->where('role', 'personil')])
                        ->with([
                            'user:id,name,no_telpon', // Kasubdis info - ensure no_telpon is included
                            'apelSessions' => function ($query) use ($today) {
                                $query->whereDate('date', $today)
                                    ->withCount([
                                        'attendances',
                                        'attendances as draft_count' => fn($q) => $q->where('status', 'draft'),
                                        'attendances as submitted_count' => fn($q) => $q->where('status', 'submitted'),
                                        'attendances as verified_count' => fn($q) => $q->where('status', 'verified'),
                                        'attendances as done_count' => fn($q) => $q->where('status', 'done')
                                    ]);
                            }
                        ])->orderBy('name')->get();

                    $subdisListForPiket = [];
                    foreach ($allSubdisForAdmin as $sub) {
                        $personilCount = $sub->personil_count ?? 0;
                        foreach (['pagi', 'sore'] as $type) {
                            $session = $sub->apelSessions->where('type', $type)->first();
                            $statusInfo = $this->determineSessionDisplayStatus($session, $personilCount);

                            $subdisListForPiket[] = [
                                'subdis_id' => $sub->id,
                                'subdis_name' => $sub->name,
                                'kasubdis_name' => $sub->user?->name ?? '-',
                                'kasubdis_no_telpon' => $sub->user?->no_telpon, // Add phone number
                                'personil_count_for_subdis' => $personilCount, // For checking if "Belum Ada Sesi" needs alert
                                'type' => $type,
                                'jam' => $session ? Carbon::parse($session->created_at)->format('H:i') : ($type === 'pagi' ? '07:00' : '15:00'),
                                'tanggal' => $todayCarbon->translatedFormat('d M Y'),
                                'keterangan_rekap' => $session ? (($session->attendances_count ?? 0) . '/' . $personilCount . ' direkap') : ($personilCount > 0 ? 'Belum ada sesi' : 'Tidak ada anggota'),
                                'status_text' => $statusInfo['text'],
                                'status_badge' => $statusInfo['badge'],
                                'link_anggota' => route('rekap-apel.anggota', ['id' => $sub->id, 'date' => $today, 'type' => $type]),
                            ];
                        }
                    }

                    $data['subdisListForPiketDashboard'] = $subdisListForPiket;
                }
                break;

            case 'pimpinan':
                // 1. Tentukan Rentang Tanggal untuk Filter
                $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
                $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

                // Kirim variabel tanggal ke view untuk ditampilkan di form filter
                $data['startDate'] = $startDate;
                $data['endDate'] = $endDate;

                // 2. Data Statistik Umum (tidak terpengaruh tanggal)
                $data['total_personel_pimpinan'] = User::where('role', 'personil')->where('is_active', '1')->count();
                $data['total_subdis_pimpinan'] = Subdis::count();

                // 3. Data untuk Grafik Personel per Subdis (tidak terpengaruh tanggal)
                $data['personel_per_subdis_chart'] = Subdis::withCount(['users as users_count' => fn($q) => $q->where('role', 'personil')->where('is_active', '1')])
                    ->orderBy('name')
                    ->get(['id', 'name']);

                // 4. Data BARU untuk Grafik Kehadiran Berdasarkan Rentang Tanggal
                $kehadiranData = DB::table('apel_attendances')
                    ->join('keterangans', 'apel_attendances.keterangan_id', '=', 'keterangans.id')
                    ->join('apel_sessions', 'apel_attendances.apel_session_id', '=', 'apel_sessions.id')
                    ->whereBetween('apel_sessions.date', [$startDate, $endDate])
                    ->select('keterangans.name', DB::raw('count(*) as total'))
                    ->groupBy('keterangans.name')
                    ->orderBy('total', 'desc')
                    ->get();

                // Siapkan data untuk Chart.js
                $data['kehadiranChartData'] = [
                    'labels' => $kehadiranData->pluck('name'),
                    'data' => $kehadiranData->pluck('total'),
                ];

                break;

            case 'superadmin':
                $data['total_personel_admin'] = User::where('is_active', '1')->count();
                $data['total_subdis_admin'] = Subdis::count();
                $data['total_roles_admin'] = User::select('role')->distinct()->count();

                $allSubdisForAdmin = Subdis::withCount(['users as personil_count' => fn($q) => $q->where('role', 'personil')])
                    ->with([
                        'user:id,name,no_telpon', // Kasubdis info - ensure no_telpon is included
                        'apelSessions' => function ($query) use ($today) {
                            $query->whereDate('date', $today)
                                ->withCount([
                                    'attendances',
                                    'attendances as draft_count' => fn($q) => $q->where('status', 'draft'),
                                    'attendances as submitted_count' => fn($q) => $q->where('status', 'submitted'),
                                    'attendances as verified_count' => fn($q) => $q->where('status', 'verified'),
                                    'attendances as done_count' => fn($q) => $q->where('status', 'done')
                                ]);
                        }
                    ])->orderBy('name')->get();

                $adminApelDisplayList = [];
                foreach ($allSubdisForAdmin as $sub) {
                    $personilCount = $sub->personil_count ?? 0;
                    foreach (['pagi', 'sore'] as $type) {
                        $session = $sub->apelSessions->where('type', $type)->first();
                        $statusInfo = $this->determineSessionDisplayStatus($session, $personilCount);

                        $adminApelDisplayList[] = [
                            'subdis_id' => $sub->id,
                            'subdis_name' => $sub->name,
                            'kasubdis_name' => $sub->user?->name ?? '-',
                            'kasubdis_no_telpon' => $sub->user?->no_telpon, // Add phone number
                            'personil_count_for_subdis' => $personilCount, // For checking if "Belum Ada Sesi" needs alert
                            'type' => $type,
                            'jam' => $session ? Carbon::parse($session->created_at)->format('H:i') : ($type === 'pagi' ? '07:00' : '15:00'),
                            'tanggal' => $todayCarbon->translatedFormat('d M Y'),
                            'keterangan_rekap' => $session ? (($session->attendances_count ?? 0) . '/' . $personilCount . ' direkap') : ($personilCount > 0 ? 'Belum ada sesi' : 'Tidak ada anggota'),
                            'status_text' => $statusInfo['text'],
                            'status_badge' => $statusInfo['badge'],
                            'link_anggota' => route('rekap-apel.anggota', ['id' => $sub->id, 'date' => $today, 'type' => $type]),
                        ];
                    }
                }
                $data['admin_apel_display_list'] = $adminApelDisplayList;
                // Piket data for superadmin
                $data['piketToday'] = $piketToday; // Already fetched
                // $data['usersForPiketSelection'] is already conditionally fetched at the top
                break;
        }

        return view('backend.index', $data);
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
        $draftCount = $session->draft_count ?? 0;
        $submittedCount = $session->submitted_count ?? 0;
        $verifiedCount = $session->verified_count ?? 0;
        $doneCount = $session->done_count ?? 0;

        if ($attendancesCount < $totalPersonil) {
            return ['text' => 'Sementara', 'badge' => 'badge-sementara'];
        }
        if ($draftCount > 0) {
            return ['text' => 'Sementara', 'badge' => 'badge-sementara'];
        }
        if ($doneCount == $totalPersonil) {
            return ['text' => 'Selesai', 'badge' => 'badge-selesai'];
        }
        if (($verifiedCount + $doneCount) == $totalPersonil) {
            return ['text' => 'Terverifikasi', 'badge' => 'badge-terverifikasi'];
        }
        if ($submittedCount > 0) {
            return ['text' => 'Terkirim', 'badge' => 'badge-terkirim'];
        }
        return ['text' => 'Perlu Dicek', 'badge' => 'badge-perlu-dicek'];
    }

    public function store(Request $request)
    {
        $request->validate([
            'piket_date' => 'required|date',
            'pajaga_by' => 'required|exists:users,id',
            'bajaga_first_by' => 'required|exists:users,id',
            'bajaga_second_by' => 'required|exists:users,id',
        ]);

        if (Piket::existsForDate($request->piket_date)) {
            return redirect()->back()->with('error', 'Data piket untuk tanggal ini sudah ada.')->withInput();
        }
        Piket::create([
            'piket_date' => $request->piket_date,
            'pajaga_by' => $request->pajaga_by,
            'bajaga_first_by' => $request->bajaga_first_by,
            'bajaga_second_by' => $request->bajaga_second_by,
            'created_by' => Auth::id(),
        ]);
        return redirect()->route('dashboard.index')->with('success', 'Data piket berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pajaga_by' => 'required|exists:users,id',
            'bajaga_first_by' => 'required|exists:users,id',
            'bajaga_second_by' => 'required|exists:users,id',
        ]);
        $piket = Piket::findOrFail($id);
        $piket->update($request->only(['pajaga_by', 'bajaga_first_by', 'bajaga_second_by']));
        return redirect()->route('dashboard.index')->with('success', 'Data piket berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $piket = Piket::findOrFail($id);
        $piket->delete();
        return redirect()->route('dashboard.index')->with('success', 'Data piket berhasil dihapus.');
    }
}
