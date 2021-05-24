<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Time;
use Auth;
use Illuminate\Http\Response;

class LocationController extends Controller
{

    protected $prayTimeUrl = 'http://128.199.69.124:1446/cek/list/sholat';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = Location::paginate(10);
        return response()->json($data);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $cek = Location::all();
        if (count($cek) > 0) {
            Location::where('status', 1)->update(['status' => 0]);
        }
        $save = new Location;
        $save->nama_tempat = $request->nm_tmpt;
        $save->status = 1;
        $save->user_id = 1;
        $save->save();

        return response()->json(['status' => 'ok']);
    }

    public function active($id)
    {
        Location::where('id', $id)->update(['status' => 1]);
        Location::where('id', '!=', $id)->update(['status' => 0]);
        // $data = Location::where('status', 1)->first();
        // Time::where('status',0)->update(['location_id' => $data->id]);

        return response()->json(['status' => 'ok']);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $edits = Location::find($id);
        return response()->json($edits);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $save = Location::find($id)
            ->update([
                'nama_tempat' => $request->nm_tmpt,
            ]);

        return response()->json(['status' => 'sukses']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $del = Location::find($id);
        $del->delete();
        return response()->json(['status' => 'ok']);
    }

    public function updateLokasiAndTime(Request $request)
    {
        $cekLocation = Location::first();
        $cekTime = Time::first();
        $reqPlace = request()->place;
        $cekUrl = @file_get_contents("{$this->prayTimeUrl}/{$reqPlace}");
        $jsonUrl = json_decode($cekUrl);

        try {
            // cek jika offline
            if (!$cekUrl) {
                $locationUpdated = Location::where('id', $cekLocation->id)->update([
                    'nama_tempat' => $reqPlace ?? $cekLocation->nama_tempat
                ]);
                if (!$locationUpdated) {
                    throw new Exception("Error When Updating");
                }

                $cekLoc = Location::first();

                return response()->json([
                    'place' => $cekLoc->nama_tempat,
                    'message' => "offlineupdateplace"
                ], 200);
            }
            // Jika Online
            $save_Loc = Location::where('id', $cekLocation->id)->update([
                'nama_tempat' => $request->place ?? $cekLocation->nama_tempat,
                'user_id' => 1,
                'status' => 1
            ]);
            $exp = explode(":",$jsonUrl->shurooq);
            $jam = sprintf('%02d',$exp[0]+1);
            $duha = $jam.':'.$exp[1];
            $cekLoc = Location::first();
            $timeUpdated = Time::where('id', $cekTime->id)->update([
                'user_id' => 1,
                'location_id' => $cekLoc->id,
                'tahajud' => $jsonUrl->tahajud,
                'subuh' => $jsonUrl->fajr,
                'syurooq' => $jsonUrl->shurooq,
                'duha' => $duha,
                'dhuhur' => $jsonUrl->dhuhr,
                'ashar' => $jsonUrl->asr,
                'maghrib' => $jsonUrl->maghrib,
                'isya' => $jsonUrl->isha,
                'status' => 1
            ]);

            if (!$save_Loc && !$timeUpdated) {
                throw new Exception("Error When Updating");
            }
            return response()->json([
                'tahajud' => $jsonUrl->tahajud,
                'subuh' => $jsonUrl->fajr,
                'syurooq' => $jsonUrl->shurooq,
                'duha' => $duha,
                'duhur' => $jsonUrl->dhuhr,
                'ashar' => $jsonUrl->asr,
                'maghrib' => $jsonUrl->maghrib,
                'isya' => $jsonUrl->isha
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'online' => $e->getLine()
            ], 500);
        }
    }

    public function deleteAll()
    {
        $del = DB::connections('sqlite')
            ->table('locations')
            ->delete();
        return back();
    }

    public function getTimeWithLocation(Request $request)
    {
        if ($request->place && $request->has('place')) {
            $get = @file_get_contents('http://128.199.69.124:1446/cek/list/sholat/' . urlencode($request->place));
            if ($get == true) {
                $dec = json_decode($get);
                $exp = explode(":",$dec->shurooq);
                $jam = sprintf('%02d',$exp[0]+1);
                $duha = $jam.':'.$exp[1];

                $save_Loc = Location::create(['nama_tempat' => $request->place, 'user_id' => 1, 'status' => 1]);
                Time::create(['user_id' => 1, 'location_id' => $save_Loc->id, 'tahajud' => $dec->tahajud, 'subuh' => $dec->fajr, 'duha' => $duha, 'syurooq' => $dec->shurooq, 'dhuhur' => $dec->dhuhr, 'ashar' => $dec->asr, 'maghrib' => $dec->maghrib, 'isya' => $dec->isha, 'status' => 1]);
                
                return response(['tahajud' => $dec->tahajud, 'subuh' => $dec->fajr, 'syurooq' => $dec->shurooq, 'duha' => $duha, 'duhur' => $dec->dhuhr, 'ashar' => $dec->asr, 'maghrib' => $dec->maghrib, 'isya' => $dec->isha]);
            } else {
                return response()->json(['msg' => 'Location Not Found'], 404);
            }
        }
    }
}
