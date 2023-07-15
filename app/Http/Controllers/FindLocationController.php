<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class FindLocationController extends Controller
{
    // public function index(Request $request)
    // {

    //     //inject data lokasi kabupaten pati
    //     //get data user dr lokasi pati yg jangkauannya 50km
    //     $latitude = -6.9691528;
    //     $longitude = 111.4130521;
    //     $distanceLimit = 10;

    //     $earthRadius = 6371; // Radius Bumi dalam kilometer
    //     $showResult = DB::table("users")
    //         ->select(
    //             "users.id",
    //             DB::raw("{$earthRadius} * ACOS(
    //                 COS(RADIANS({$latitude})) * COS(RADIANS(users.latitude)) *
    //                 COS(RADIANS(users.longtitude) - RADIANS({$longitude})) +
    //                 SIN(RADIANS({$latitude})) * SIN(RADIANS(users.latitude))
    //             ) AS distance")
    //         )
    //         ->whereRaw("{$earthRadius} * ACOS(
    //                 COS(RADIANS({$latitude})) * COS(RADIANS(users.latitude)) *
    //                 COS(RADIANS(users.longtitude) - RADIANS({$longitude})) +
    //                 SIN(RADIANS({$latitude})) * SIN(RADIANS(users.latitude))
    //             ) <= ?", [$distanceLimit])
    //         ->get();

    //     dd($showResult);
    // }
    // public function cekradius(Request $request)
    // {
    //     // $bloraLatitude = -6.9614;
    //     // $bloraLongitude = 111.4189;

    //     $bloraLatitude = -6.9691528;
    //     $bloraLongitude = 111.4130521;
    //     $patiLatitude = -6.7573;
    //     $patiLongitude = 111.0386;

    //     $distance = DB::selectOne("
    //         SELECT ROUND(6371 * ACOS(
    //             COS(RADIANS(?)) * COS(RADIANS(?)) * COS(RADIANS(? - ?)) +
    //             SIN(RADIANS(?)) * SIN(RADIANS(?))
    //         )) AS distance
    //     ", [$bloraLatitude, $patiLatitude, $bloraLongitude, $patiLongitude, $bloraLatitude, $patiLatitude])->distance;

    //     echo "Jarak antara Blora dan Pati: " . $distance . " km";
    // }







    //cara mendapatkan latitude dan longitude
    public function getLongitudeLatitude()
    {
        $cityQuery = 'pati'; // Ganti dengan nama kota yang ingin Anda cari

        $coordinates = $this->getCityCoordinates($cityQuery);
        // dd($coordinates);
        if ($coordinates) {
            $latitude = $coordinates['latitude'];
            $longitude = $coordinates['longitude'];
            $distanceLimit = 58; // Radius jarak dalam kilometer

            $earthRadius = 6371; // Radius Bumi dalam kilometer

            $showResult = DB::table("users")
                ->select(
                    "users.*", // Menampilkan semua kolom dari tabel users
                    DB::raw("{$earthRadius} * ACOS(
                COS(RADIANS({$latitude})) * COS(RADIANS(users.latitude)) *
                COS(RADIANS(users.longtitude) - RADIANS({$longitude})) +
                SIN(RADIANS({$latitude})) * SIN(RADIANS(users.latitude))
            ) AS distance")
                )
                ->having('distance', '<=', $distanceLimit)
                ->get();

            dd($showResult);
        } else {
            echo "Kota tidak ditemukan.";
        }
    }

    public function getCityCoordinates($cityQuery)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->get("https://nominatim.openstreetmap.org/search?format=json&q={$cityQuery}&countrycodes=ID");
        $data = json_decode($response->getBody(), true);

        if (!empty($data)) {
            $latitude = $data[0]['lat'];
            $longitude = $data[0]['lon'];

            return [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        }

        return null;
    }
    //end  code cara mendapatkan latitude dan longitude


}
