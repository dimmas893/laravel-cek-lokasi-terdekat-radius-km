<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FindLocationController extends Controller
{
    public function index(Request $request) {

        //inject data lokasi kabupaten pati
        //get data user dr lokasi pati yg jangkauannya 50km
        $latitude = -6.7573;
        $longitude = 111.0386;
        $distanceLimit = 50;

        $earthRadius = 6371; // Radius Bumi dalam kilometer
        $showResult = DB::table("users")
            ->select(
                "users.id",
                DB::raw("{$earthRadius} * ACOS(
                    COS(RADIANS({$latitude})) * COS(RADIANS(users.latitude)) *
                    COS(RADIANS(users.longtitude) - RADIANS({$longitude})) +
                    SIN(RADIANS({$latitude})) * SIN(RADIANS(users.latitude))
                ) AS distance")
            )
            ->whereRaw("{$earthRadius} * ACOS(
                    COS(RADIANS({$latitude})) * COS(RADIANS(users.latitude)) *
                    COS(RADIANS(users.longtitude) - RADIANS({$longitude})) +
                    SIN(RADIANS({$latitude})) * SIN(RADIANS(users.latitude))
                ) <= ?", [$distanceLimit])
            ->get();

        dd($showResult);
    }
    public function cekradius(Request $request) {
        $bloraLatitude = -6.9614;
        $bloraLongitude = 111.4189;
        $patiLatitude = -6.7573;
        $patiLongitude = 111.0386;

        $distance = DB::selectOne("
            SELECT ROUND(6371 * ACOS(
                COS(RADIANS(?)) * COS(RADIANS(?)) * COS(RADIANS(? - ?)) +
                SIN(RADIANS(?)) * SIN(RADIANS(?))
            )) AS distance
        ", [$bloraLatitude, $patiLatitude, $bloraLongitude, $patiLongitude, $bloraLatitude, $patiLatitude])->distance;

        echo "Jarak antara Blora dan Pati: " . $distance . " km";
    }
}
