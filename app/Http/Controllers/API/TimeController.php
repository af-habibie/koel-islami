<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Time;
use App\Models\Location;
use App\Models\ModHadist;
use App\Models\Playlist;
use Auth;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use DB;

class TimeController extends Controller
{
    public function index()
    {
        $url = @file_get_contents('http://128.199.69.124:1446');
        $place = Location::first();
        $apiAdzan = Time::with('locTime', 'userTime')->orderBy('id', 'desc')->first();

        // ================= jika offline
        if (!$url && !$place && !$apiAdzan) {
            return response()->json(['msg' => 'offline first install'], 500);
        }
        // ============================

        // ============= jika online
        if ($url && !$place && !$apiAdzan) {
            return response()->json(['msg' => 'sesuaikan lokasi'], 500);
        }
        // ============== 

        // ============= jika lokasi kosong
        if (!$place) {
            return response()->json(['msg' => 'lokasi tidak ditemukan'], 404);
        }
        // ============== END

        // ============= Jika apiadzan kosong atau tidak kosong
        $url = @file_get_contents('http://128.199.69.124:1446/cek/list/sholat/' . $place->nama_tempat);
        if ($url == true) {
            // if(count($apiAdzan) == 0){
            //     return response()->json(['msg' => 'status online tapi data time belum ada'], 404);
            // }

            $json = json_decode($url, true);
            $tahajud = date('H:i', strtotime($json['tahajud']));
            $fajr = date('H:i', strtotime($json['fajr']));
            $syuruq = date('H:i', strtotime($json['shurooq']));
            $exp = explode(":", $syuruq);
            $jam = sprintf('%02d', $exp[0] + 1);
            $duha = $jam . ':' . $exp[1];
            $duhr = date('H:i', strtotime($json['dhuhr']));
            $ashar = date('H:i', strtotime($json['asr']));
            $maghrib = date('H:i', strtotime($json['maghrib']));
            $isya = date('H:i', strtotime($json['isha']));

            // ===== Simpan jadwal dari api jika online dan belum ada data
            if (!$apiAdzan) {
                $insert = Time::create([
                    'location_id' => $place->id,
                    'tahajud' => $tahajud,
                    'subuh' => $fajr,
                    'syurooq' => $syuruq,
                    'duha' => $duha,
                    'dhuhur' => $duhr,
                    'ashar' => $ashar,
                    'maghrib' => $maghrib,
                    'isya' => $isya,
                    'status' => 1]);
            }

            $cekIt = Time::with('locTime', 'userTime')->orderBy('id', 'desc')->first();
            $tahajud = $cekIt->tahajud;
            $fajr = $cekIt->subuh;
            $syuruq = $cekIt->syurooq;
            $duha = $cekIt->duha;
            $dzuhur = $cekIt->dhuhur;
            $ashar = $cekIt->ashar;
            $maghrib = $cekIt->maghrib;
            $isya = $cekIt->isya;
            $message = 'online exist';
            $updateApi = 'enable';

            $data = array(
                'place' => $place,
                'tahajud' => $tahajud,
                'subuh' => $fajr,
                'syurooq' => $syuruq,
                'duha' => $duha,
                'duhur' => $dzuhur,
                'ashar' => $ashar,
                'maghrib' => $maghrib,
                'isya' => $isya
            );
            return response()->json([
                'msg' => $message,
                'buttonApi' => $updateApi,
                'data' => $data
            ], 200);

        } else {
            if (!$apiAdzan) {
                return response()->json(['msg' => 'tidak ada data di time table'], 404);
            }
            $updateApi = 'disable';
            $message = 'offline exists';
            $tahajud = $apiAdzan->tahajud;
            $fajr = $apiAdzan->subuh;
            $syuruq = $apiAdzan->syurooq;
            $duha = $apiAdzan->duha;
            $dzuhur = $apiAdzan->dhuhur;
            $ashar = $apiAdzan->ashar;
            $maghrib = $apiAdzan->maghrib;
            $isya = $apiAdzan->isya;
            //return response()->json(['msg' => 'tidak ada data di time table'], 404);

            $data = array(
                'place' => $place,
                'tahajud' => $tahajud,
                'subuh' => $fajr,
                'syurooq' => $syuruq,
                'duha' => $duha,
                'duhur' => $dzuhur,
                'ashar' => $ashar,
                'maghrib' => $maghrib,
                'isya' => $isya,
                'time_raspi' => date('H:i:s'),
                'date_raspi' => date('Y-m-d')
            );
            return response()->json([
                'msg' => $message,
                'buttonApi' => $updateApi,
                'data' => $data
            ], 200);
        }
        // ============= END IF
    }

    public function updateTimeAPI()
    {
        $cek = Time::all();
        if (!$cek) {
            Time::where('status', 1)->update(['status' => 0]);
        }

        $place = Location::first();
        $url = @file_get_contents('http://128.199.69.124:1446/cek/list/sholat/' . $place->nama_tempat);
        if ($url == true) {
            $json = json_decode($url, true);
            $tahajud = date('H:i', strtotime($json['tahajud']));
            $fajr = date('H:i', strtotime($json['fajr']));
            $syuruq = date('H:i', strtotime($json['shurooq']));
            $exp = explode(":", $syuruq);
            $jam = sprintf('%02d', $exp[0] + 1);
            $duha = $jam . ':' . $exp[1];
            $dzuhur = date('H:i', strtotime($json['dhuhr']));
            $ashar = date('H:i', strtotime($json['asr']));
            $maghrib = date('H:i', strtotime($json['maghrib']));
            $isya = date('H:i', strtotime($json['isha']));

            Time::orderBy('id', 'DESC')->update([
                'tahajud' => $tahajud,
                'subuh' => $fajr,
                'syurooq' => $syuruq,
                'duha' => $duha,
                'dhuhur' => $dzuhur,
                // 'ashar' => $ashar,
                'maghrib' => $maghrib,
                'isya' => $isya,
            ]);
        } else {
            return response(['msg' => 'failed to get time from Internet'], 403);
        }
        $time = Time::orderBy('id', 'desc')->first();
        $data = array(
            'tahajud' => $tahajud,
            'subuh' => $fajr,
            'syurooq' => $syuruq,
            'duha' => $duha,
            'duhur' => $dzuhur,
            'ashar' => $ashar,
            'maghrib' => $maghrib,
            'isya' => $isya
        );

        return response()->json(['status' => 'ok', 'data' => $data], 200); // response nya status dan tampilan data terbaru dan disimpan dari db
    }

    public function pauseAdzan()
    {
        //$time = request()->keyTime;
        date_default_timezone_set('Asia/Jakarta');
        $time = date('H:i');
        $adzan = Time::where('status', 1)->get();
        $listHadist = ModHadist::where('status_play', 0)->get();
        $ti = Time::where('status', 1)->first();
        $mag = $ti->maghrib;
        $isy = $ti->isya;
        // ======= play hadist secara acak
        foreach ($listHadist as $key => $getHadist) {
            $ex = explode(':', $getHadist->time_to_play);
            $get_H = $ex[0];
            $ex_m = explode(":", $mag);
            $ex_i = explode(":", $isy);
            $creta = $getHadist->created_at;//createdat//;
            $ex = explode("-", $creta);
            $getDay = $ex[2];
            $exx = explode(" ", $getDay);
            //dd($getDay);
            //dd($exx[0]);
            $time2 = 29;
            if ($get_H < $ex_m[0] || $get_H > $ex_i[0]) {
                //echo $get_H." Akan Output JSON<br>" ;
                if ($time == $getHadist->time_to_play && $exx[0] == $time2 && count($listHadist) <= 10) {
                    $hadist = ModHadist::where('time_to_play', $time)->update(['status_play' => 1]);
                    return response()->json(['status' => 5, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl($getHadist->path)]);
                }
            }

        }
        // ===============================

        foreach ($adzan as $key => $getValue) {
            $tahajud = $getValue->tahajud;
            $fajr = $getValue->subuh;
            $syuruq = $getValue->syurooq;
            $duha = $getValue->duha;
            $dzuhur = $getValue->dhuhur;
            $ashar = $getValue->ashar;
            $maghrib = $getValue->maghrib;
            $isya = $getValue->isya;

            // ===== Waktu sepuluh menit sebelum adzan
            if ($time == waktu($fajr)) {
                return response()->json(['status' => 3, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/ten/subuh.mp3')]);
            }
            if ($time == waktu($dzuhur)) {
                return response()->json(['status' => 3, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/ten/dzhuhur.mp3')]);
            }
            if ($time == waktu($ashar)) {
                return response()->json(['status' => 3, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/ten/ashar.mp3')]);
            }
            if ($time == waktu($maghrib)) {
                return response()->json(['status' => 3, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/ten/magrib.mp3')]);
            }
            if ($time == waktu($isya)) {
                return response()->json(['status' => 3, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/ten/isya.mp3')]);
            }
            // =====================================

            // ===== Waktu 5 menit sebelum adzan
            if ($time == waktuLima($fajr)) {
                return response()->json(['status' => 2, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/five/subuh.mp3')]);
            }
            if ($time == waktuLima($dzuhur)) {
                return response()->json(['status' => 2, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/five/dzhuhur.mp3')]);
            }
            if ($time == waktuLima($ashar)) {
                return response()->json(['status' => 2, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/five/ashar.mp3')]);
            }
            if ($time == waktuLima($maghrib)) {
                return response()->json(['status' => 2, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/five/magrib.mp3')]);
            }
            if ($time == waktuLima($isya)) {
                return response()->json(['status' => 2, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => App::staticUrl('public/adzan/five/isya.mp3')]);
            }
            // ====================================


            // ========== Waktu Adzan, Tahajud, Duha dan Syurooq ==========
            if ($time == $fajr) {
                $call = hadistNempel("Hadist Subuh");
                return response()->json(['status' => 1, 'mp3alert' => App::staticUrl('public/adzan/alert/alert.mp3'), 'mp3' => App::staticUrl('public/adzan/adzan/fajr_128_44.mp3'), 'mp3hadistnempel' => $call ?? false]);
            }
            if ($time == $tahajud) {
                $call = hadistNempel("Hadist Tahajud");
                return response()->json(['status' => 1, 'mp3alert' => App::staticUrl('public/adzan/alert/alert.mp3'), 'mp3' => App::staticUrl('public/adzan/adzan/tahajud.mp3'), 'mp3hadistnempel' => $call ?? false]);
            }
            if ($time == $duha) {
                $call = hadistNempel("Hadist Duha");
                return response()->json(['status' => 5, 'mp3alert' => App::staticUrl('public/adzan/alert/tingnung.mp3'), 'mp3' => $call ?? false]);
            }
            if ($time == $dzuhur) {
                $call = hadistNempel("Hadist Dzuhur");
                return response()->json(['status' => 1, 'mp3alert' => App::staticUrl('public/adzan/alert/alert.mp3'), 'mp3' => App::staticUrl('public/adzan/adzan/mecca_56_22.mp3'), 'mp3hadistnempel' => $call ?? false]);
            }
            if ($time == $ashar) {
                $call = hadistNempel("Hadist Ashar");
                return response()->json(['status' => 1, 'mp3alert' => App::staticUrl('public/adzan/alert/alert.mp3'), 'mp3' => App::staticUrl('public/adzan/adzan/mecca_56_22.mp3'), 'mp3hadistnempel' => $call ?? false]);
            }
            if ($time == $maghrib) {
                $call = hadistNempel("Hadist Maghrib");
                $timeout = $this->find_time_diff($maghrib, $isya);
                return response()->json(['status' => 4, 'mp3alert' => App::staticUrl('public/adzan/alert/alert.mp3'), 'mp3' => App::staticUrl('public/adzan/adzan/mecca_56_22.mp3'), 'mp3hadistnempel' => $call ?? false, 'timeout' => $timeout], 200);
            }
            if ($time == $isya) {
                $call = hadistNempel("Hadist Isya");
                return response()->json(['status' => 1, 'mp3alert' => App::staticUrl('public/adzan/alert/alert.mp3'), 'mp3' => App::staticUrl('public/adzan/adzan/mecca_56_22.mp3'), 'mp3hadistnempel' => $call ?? false]);
            }
            // ====================================

            // ============= Melanjutkan audio setelah 10 menit
            if ($time > $tahajud) {
                return $this->resumeAudio($tahajud);
            }
            if ($time > $fajr) {
                return $this->resumeAudio($fajr);
            }
            if ($time > $syuruq) {
                return $this->resumeAudio($syuruq);
            }
            if ($time > $duha) {
                return $this->resumeAudio($duha);
            }
            if ($time > $dzuhur) {
                return $this->resumeAudio($dzuhur);
            }
            if ($time > $ashar) {
                return $this->resumeAudio($ashar);
            }
            if ($time > $maghrib) {
                return $this->resumeAudio($maghrib);
            }
            if ($time > $isya) {
                return $this->resumeAudio($isya);
            }
            // ===============================================

        }

    }

    private function find_time_diff($t1, $t2)
    {
        $a1 = explode(":", $t1);
        $a2 = explode(":", $t2);
        $time1 = (($a1[0] * 60 * 60) + ($a1[1] * 60));
        $time2 = (($a2[0] * 60 * 60) + ($a2[1] * 60));
        $diff = abs($time1 - $time2);
        return $diff * 1000;
    }

    // === long pause detik

    private function resumeAudio($whatSchedule)
    {
        $time = date('H:i'); // jam format 00:00
        $addSep = tambahWaktu($whatSchedule);
        if ($time == $addSep) {
            return response()->json(['status' => 0]);
        }
    }

    public function updateWaktu(Request $request)
    {
        $cekTime = Time::first();
        // $exp = explode(":", $request->duha);
        // $jam = sprintf('%02d',$exp[0]+1);
        // $duha = $jam.':'.$exp[1];
        //echo $duha;
        try {
            $timeUpdated = Time::where('id', $cekTime->id)->update([
                'tahajud' => request()->tahajud ?? $cekTime->tahajud,
                'subuh' => request()->fajr ?? $cekTime->subuh,
                'dhuhur' => request()->dzuhur ?? $cekTime->dhuhur,
                'isya' => request()->isya ?? $cekTime->isya,
                'ashar' => request()->ashar ?? $cekTime->ashar,
                'syurooq' => request()->syuruq ?? $cekTime->syurooq,
                'duha' => request()->duha ?? $cekTime->duha,
                'maghrib' => request()->maghrib ?? $cekTime->maghrib,
            ]);
            if (!$timeUpdated) {
                throw new Exception("Error When Updating");
            }

            return response()->json(["message" => "ok"], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $cek = Time::where('status', 1)->get();
        if (count($cek) > 0) {
            Time::where('status', 1)->update(['status' => 0]);
        }
        $loc = Location::where('status', 1)->first();
        $exp = explode(":", $request->syuruq);
        $jam = sprintf('%02d', $exp[0] + 1);
        $duha = $jam . ':' . $exp[1];
        $sa = Time::create([
            'user_id' => 1,
            'location_id' => $loc->id,
            'tahajud' => $request->tahajud,
            'subuh' => $request->fajr,
            'syurooq' => $request->syuruq,
            'duha' => $duha,
            'dhuhur' => $request->dzuhur,
            'ashar' => $request->ashar,
            'maghrib' => $request->maghrib,
            'isya' => $request->isya,
            'status' => 1
        ]);

        return response()->json(['status' => 'sukses'], 200);
    }

    public function show($id)
    {
        //
    }

    public function active($id)
    {
        Time::where('id', $id)->update(['status' => 1]);
        Time::where('id', '!=', $id)->update(['status' => 0]);
        return response()->json(['status' => 'ok'], 200);
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
        $update = Time::find($id);
        $update->update([
            'tahajud' => $request->tahajud,
            'subuh' => $request->fajr,
            'syurooq' => $request->syuruq,
            'duha' => $request->duha,
            'dhuhur' => $request->dzuhur,
            'ashar' => $request->ashar,
            'maghrib' => $request->maghrib,
            'isya' => $request->isya,
        ]);
        return response()->json(['status' => 'ok'], 200);
    }

    public function destroy($id)
    {
        $del = Time::find($id);
        $del->delete();

        return response()->json(['status' => 'ok'], 200);
    }

    public function postBegin(Request $request)
    {
        $cek = Location::all();
        if (count($cek) > 0) {
            Location::where('status', 1)->update(['status' => 0]);
        }
        $save = new Location;
        $save->nama_tempat = $request->place;
        $save->status = 1;
        $save->user_id = 1;
        $save->save();

        // $exp = explode(":",$request->syuruq);
        // $duha = $exp[0]+1 .':'.$exp[1];
        $loc = Location::where('status', 1)->first();
        $sa = Time::create([
            'user_id' => 1,
            'location_id' => $loc->id,
            'tahajud' => $request->tahajud,
            'subuh' => $request->fajr,
            'syurooq' => $request->syuruq,
            'duha' => $request->duha,
            'dhuhur' => $request->dzuhur,
            'ashar' => $request->ashar,
            'maghrib' => $request->maghrib,
            'isya' => $request->isya,
            'status' => 1
        ]);

        return response()->json(['status' => 'sukses'], 200);
    }
}
