<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\School;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ======================
    // CHECK-IN
    // ======================
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required',
            'longitude' => 'required',
            'foto'      => 'nullable|image',
        ]);

        $user  = $request->user();
        $today = Carbon::today()->toDateString();
        $now   = now();

    
        if (Attendance::where('user_id', $user->id)->where('tanggal', $today)->exists()) {
            return response()->json(['status'=>false,'message'=>'Sudah absen hari ini'],409);
        }

       
        $jamMulaiAbsen = Carbon::createFromTime(4, 0, 0);
        $jamAkhirAbsen = Carbon::createFromTime(9, 0, 0);

        if ($now->lt($jamMulaiAbsen) || $now->gt($jamAkhirAbsen)) {
            return response()->json([
                'status'=>false,
                'message'=>'Absen masuk hanya bisa jam 07:00 - 09:00'
            ],403);
        }

        
        $school = School::first();
        $distance = $this->distance(
            $school->latitude,
            $school->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > $school->radius) {
            return response()->json([
                'status'=>false,
                'message'=>'Kamu di luar area sekolah'
            ],403);
        }

        
        $jamTerlambat = Carbon::createFromTime(8, 15, 0);
        $status = $now->lte($jamTerlambat) ? 'hadir' : 'terlambat';

        // foto
        $path = $request->hasFile('foto')
            ? $request->file('foto')->store('absensi','public')
            : null;

        $attendance = Attendance::create([
            'user_id'   => $user->id,
            'tanggal'   => $today,
            'jam_masuk' => $now,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'foto'      => $path,
            'status'    => $status,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Check-in berhasil',
            'data'=>$attendance
        ]);
    }

    public function checkOut(Request $request)
    {
        $attendance = Attendance::where('user_id',$request->user()->id)
            ->where('tanggal',Carbon::today()->toDateString())
            ->first();

        if (!$attendance) {
            return response()->json(['status'=>false,'message'=>'Belum check-in'],404);
        }

        if ($attendance->jam_pulang) {
            return response()->json(['status'=>false,'message'=>'Sudah check-out'],409);
        }

        $now = now();

       
        $jamMulaiPulang = Carbon::createFromTime(5, 0, 0);
        $jamAkhirPulang = Carbon::createFromTime(18, 0, 0);

        if ($now->lt($jamMulaiPulang) || $now->gt($jamAkhirPulang)) {
            return response()->json([
                'status'=>false,
                'message'=>'Check-out hanya bisa jam 15:00 - 18:00'
            ],403);
        }

        $status = $now->lt($jamMulaiPulang)
            ? 'pulang_dini'
            : 'pulang';

        $attendance->update([
            'jam_pulang' => $now,
            'status'     => $status,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Check-out berhasil',
            'data'=>$attendance
        ]);
    }

    public function history(Request $request)
    {
        $data = Attendance::where('user_id', $request->user()->id)
            ->orderByDesc('tanggal')
            ->get()
            ->map(function ($item) {
                return [
                    'date'     => Carbon::parse($item->tanggal)->format('Y-m-d'),
                    'time_in'  => optional($item->jam_masuk)->format('H:i:s'),
                    'time_out' => optional($item->jam_pulang)->format('H:i:s'),
                    'status'   => ucfirst($item->status),
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

   
    private function distance($lat1,$lon1,$lat2,$lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2))
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
              * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1609.344;
    }
}
