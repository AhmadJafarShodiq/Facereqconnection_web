<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $school = School::first();
        return view('admin.school.index', compact('school'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
            'primary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        $school = School::first() ?? new School();
        
        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('schools', 'public');
            $school->logo = $path;
        }

        // Update other fields
        $school->nama_sekolah = $validated['nama_sekolah'];
        $school->latitude = $validated['latitude'];
        $school->longitude = $validated['longitude'];
        $school->radius = $validated['radius'];
        
        if (!empty($validated['primary_color'])) {
            $school->primary_color = $validated['primary_color'];
        }

        $school->save();

        return redirect()->back()->with('success', 'Pengaturan sekolah berhasil diperbarui.');
    }
}
