<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Time;
use Auth;
use Illuminate\Console\Command;

class Apiinsert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateapi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update api ke database setiap hari';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $place = Location::where('status', 1)->first();
        // $url = file_get_contents("http://128.199.69.124:1446/cek/list/sholat/" . $place->nama_tempat);
        // if($url == True)
        // {
        //     $json = json_decode($url, true);
        //     $tahajud = date('H:i', strtotime($json['tahajud']));
        //     $fajr = date('H:i', strtotime($json['fajr']));
        //     $syuruq = date('H:i', strtotime($json['shurooq']));
        //     $exp = explode(":", $syuruq);
        //     $jam = sprintf('%02d', $exp[0] + 1);
        //     $duha = $jam . ':' . $exp[1];
        //     $dzuhur = date('H:i', strtotime($json['dhuhr']));
        //     $ashar = date('H:i', strtotime($json['asr']));
        //     $maghrib = date('H:i', strtotime($json['maghrib']));
        //     $isya = date('H:i', strtotime($json['isha']));

        //     Time::orderBy('id', 'DESC')->update([
        //         'tahajud' => $tahajud,
        //         'subuh' => $fajr,
        //         'syurooq' => $syuruq,
        //         'duha' => $duha,
        //         'dhuhur' => $dzuhur,
        //         // 'ashar' => $ashar,
        //         'maghrib' => $maghrib,
        //         'isya' => $isya,
        //     ]);
            
        //     echo "Berhasil update jadwal solat \n";
        // }
        exec("/usr/bin/python /D:/xampp/htdocs/koel/scrapmuslimpro/jadwalsholatbekasi.py > /dev/null 2>>/tmp/log-koel.txt&");

    }
}
