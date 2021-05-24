<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class ApiAdzan extends Controller
{
    //
    public function index()
    {
        $jam = date("H:i:s");
        $date = date('d-m-Y');
        $place = \App\Models\Location::where('status',1)->first();
        $apiAdzan = \App\Models\Time::with('locTime','userTime')->orderBy('id','desc')->first();

        return response()->json([
                        'jam' => $jam,
                        'tanggal' => $date,
                        'tahajud' => $apiAdzan->tahajud,
                        'shurooq' => $apiAdzan->syurooq,
                        'duha' => $apiAdzan->duha,
                        'subuh' => $apiAdzan->subuh, 
                        'dhuhur' => $apiAdzan->dhuhur, 
                        'ashar' => $apiAdzan->ashar, 
                        'maghrib' => $apiAdzan->maghrib, 
                        'isya' => $apiAdzan->isya, 
                        ]);
    }
}
