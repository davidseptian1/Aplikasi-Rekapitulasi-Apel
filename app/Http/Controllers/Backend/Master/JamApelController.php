<?php

namespace App\Http\Controllers\Backend\Master;

use App\Models\JamApel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JamApelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Pengaturan Jam Apel',
            'jamPagi' => JamApel::where('type', 'pagi')->first(),
            'jamSore' => JamApel::where('type', 'sore')->first(),
        ];

        return view('backend.pengaturan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
{
    $request->validate([
        'pagi_start_time' => 'required|date_format:H:i',
        'pagi_end_time' => 'required|date_format:H:i|after:pagi_start_time',
        'sore_start_time' => 'required|date_format:H:i',
        'sore_end_time' => 'required|date_format:H:i|after:sore_start_time',
    ]);

    JamApel::updateOrCreate(
        ['type' => 'pagi'],
        [
            'start_time' => $request->pagi_start_time,
            'end_time' => $request->pagi_end_time,
        ]
    );

    JamApel::updateOrCreate(
        ['type' => 'sore'],
        [
            'start_time' => $request->sore_start_time,
            'end_time' => $request->sore_end_time,
        ]
    );

    return redirect()->route('jam-apel.index')->with('success', 'Pengaturan jam apel berhasil diperbarui.');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
