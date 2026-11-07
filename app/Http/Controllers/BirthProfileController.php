<?php

namespace App\Http\Controllers;

use App\Models\BirthProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BirthProfileController
{
    public function index(Request $request)
    {
        return view('hindutithi.birth_profiles', ['profiles' => $request->user()->birthProfiles()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'birth_time' => ['required', 'date_format:H:i'],
            'timezone' => ['required', 'timezone'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'elevation_meters' => ['nullable', 'numeric', 'between:-500,9000'],
        ]);

        if ($request->user()->birthProfiles()->count() >= 20) {
            return back()->withErrors(['name' => 'You can save up to 20 birth profiles.']);
        }

        $request->user()->birthProfiles()->create($data);

        return back()->with('status', 'Birth profile saved.');
    }

    public function destroy(Request $request, BirthProfile $profile): RedirectResponse
    {
        abort_unless($profile->user_id === $request->user()->id, 404);
        $profile->delete();

        return back()->with('status', 'Birth profile removed.');
    }
}
