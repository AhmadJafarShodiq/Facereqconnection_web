<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $school = School::first();

        if (!$school) {
            return response()->json([
                'status' => false,
                'message' => 'Data sekolah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $school->id,
                'nama_sekolah' => $school->nama_sekolah,
                'latitude' => $school->latitude,
                'longitude' => $school->longitude,
                'radius' => $school->radius,
                'logo_url' => $school->logo_url,
                'primary_color' => $school->primary_color,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $school = School::first() ?? new School();

        $validated = $request->validate([
            'nama_sekolah' => 'sometimes|string',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'radius' => 'sometimes|integer',
            'primary_color' => 'sometimes|string',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('schools', 'public');
            $school->logo = $path;
        }

        // Fill only the validated data except logo (already handled)
        $school->fill($request->except('logo'));
        $school->save();

        return response()->json([
            'status' => true,
            'message' => 'Data sekolah berhasil diperbarui',
            'data' => $school
        ]);
    }
}
