<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Piket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatPiketController extends Controller
{
    /**
     * Display a listing of the Piket history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', now()->format('Y-m-d'));

        // Validate if the date is in the correct format, fallback to today if not
        try {
            $filterDate = Carbon::createFromFormat('Y-m-d', $selectedDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $filterDate = now()->format('Y-m-d');
        }

        $pikets = Piket::whereDate('piket_date', $filterDate)
            ->with([
                'pajaga:id,name', // Select only necessary fields
                'pajaga.biodata.pangkat:id,name',
                'bajagaFirst:id,name',
                'bajagaFirst.biodata.pangkat:id,name',
                'bajagaSecond:id,name',
                'bajagaSecond.biodata.pangkat:id,name',
                'creator:id,name'
            ])
            ->orderBy('created_at', 'desc') // Show latest created for the day if multiple (shouldn't happen)
            ->get();

        $data = [
            'title' => 'Riwayat Sesi Piket',
            'pages' => 'Riwayat Sesi Piket', // For breadcrumb/page title consistency
            'pikets' => $pikets,
            'filterDate' => $filterDate,
        ];

        return view('backend.riwayat_piket.index', $data);
    }
}
