<?php


namespace App\Services;

use App\Models\ModHadist;
use Illuminate\Support\Facades\DB;

class RandTimeHadistService
{
    public function insert($getTimeDif)
    {
        $getTimeDif->each(function ($dataDif) {
            $date = "04:10";
            $ex = explode(':', $date);

            $plusMin = $ex[1] + rand(6, 40);
            $plusHou = $ex[0] + rand(1, 14);
            // $time_schedule = sprintf('%02d', $plusHou) . ":" . sprintf('%02d', $plusMin);
            $time_schedule = '-';
            $query = DB::table('songs')->where('id', $dataDif)->first();
            $st = $query->path;
            $ex = explode("public/", $st);
            $hasil = "public/" . $ex[1];
            $query2 = DB::table('playlist_song')->where('song_id', $dataDif)->first();
            ModHadist::create(['title' => $query->title, 'song_id' => $query->id, 'playlist_id' => $query2->playlist_id, 'path' => $hasil, 'time_to_play' => $time_schedule, 'status_play' => 0]);
        });
    }

    public function updateIfDupe()
    {
        $get_all = DB::table('list_hadistrand')->get();
        $list_time = array();
        $dups = array();
        for ($az = 0; $az < count($get_all); $az++) {
            $list_time[] = $get_all[$az]->time_to_play;
        }
        foreach (array_count_values($list_time) as $val => $c) {
            if ($c > 1) {
                $dups[] = $val;
            }
        }
        $qq = array();
        for ($d = 0; $d < count($dups); $d++) {
            $qq [] = DB::table('list_hadistrand')->where('time_to_play', $dups[$d])->get();
        }
        for ($q = 0; $q < count($qq); $q++) {
            $date = "04:10";
            $ex = explode(':', $date);

            $plusMin = $ex[1] + rand(6, 40);
            $plusHou = $ex[0] + rand(1, 14);
            $time_schedule = sprintf('%02d', $plusHou) . ":" . sprintf('%02d', $plusMin);
            $update = ModHadist::where('id', $qq[0][$q]->id)->update(['time_to_play' => $time_schedule]);
        }
    }
}