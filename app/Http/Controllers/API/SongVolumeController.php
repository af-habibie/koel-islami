<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\SongVolumeRequest;
use App\Models\SongVolume;

class SongVolumeController extends Controller
{
    protected $request;

    /**
     * SongVolumeController constructor.
     * @param $request
     */
    public function __construct(SongVolumeRequest $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $vol = SongVolume::all()->map(function ($songVolume) {
            return [
                'name_volume' => $songVolume->name_volume,
                'presentage' => $songVolume->presentage
            ];
        });
        return response()->json(["data" => $vol], 200);
    }

    public function showSesi($sesi)
    {
        $vol = SongVolume::where("name_volume", $sesi)->first();
        return response()->json(['sesi' => $sesi, 'presentage' => $vol->presentage], 200);
    }

    public function updateVolume($sesi)
    {
        SongVolume::where("name_volume", "like", "%{$sesi}%")->update([
            'presentage' => $this->request->valueVol
        ]);
        exec('amixer -q -M sset PCM '.$this->request->valueVol.'%');
        
        return response()->json(['message' => 'Ok'], 200);
    }

}
