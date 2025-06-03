<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $data = [
            'title' => 'Edit Profil',
            'pages' => 'Profil',
            'user' => $user,
        ];

        return view('backend.profile.edit', $data);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update the basic information.
     */
    public function updateBasicInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'no_telpon' => 'nullable|string|max:30',
            'username' => 'required|string|max:55|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'photos' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        $photoName = $user->photos;
        if ($request->hasFile('photos')) {
            // Delete old photo if exists
            if ($photoName) {
                Storage::disk('public')->delete('uploads/photos/' . $photoName);
            }

            $photo = $request->file('photos');
            $extension = $photo->getClientOriginalExtension();
            $photoName = Str::random(40) . '.' . $extension;
            $photo->storeAs('uploads/photos', $photoName, 'public');
        }

        // Update user
        $user->update([
            'name' => $request->name,
            'no_telpon' => $request->no_telpon,
            'username' => $request->username,
            'email' => $request->email,
            'photos' => $photoName,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Informasi dasar berhasil diperbarui');
    }

    /**
     * Update the password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diperbarui');
    }
}
