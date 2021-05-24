<?php

namespace App\Console\Commands;

use App\Models\ModHadist;
use App\Models\Time;
use Illuminate\Console\Command;

class CronHadist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play:hadist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron untuk membuat waktu hadist random';

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
        $time = date('H:i'); // jam format 00:00
	//dd($time);
        $adzan = Time::where('status', 1)->get();
        $jml = 2; // maksimal 6 hadist
        // ===== cek bila list hadist random sudah di play semua/tidak ada yang status_play nya 0 
        $cekHadist = ModHadist::where('status_play', '0')->get();
        if (count($cekHadist) == 0) {
            // ==== maka update semua hadist status nya jadi 0 lagi dan jam nya jadi '-' semua lagi ====
            ModHadist::where('status_play', '1')->update(['time_to_play' => '-', 'status_play' => '0']);
        }
        // =============

        foreach ($adzan as $key => $getValue) {
            // ===
            echo "ini untuk random waktu hadist\n";
            // =====

            // ========== 10 menit sebelum adzan ==========
            $timeSubuhSep = waktu($getValue->subuh);
            $timeDzuSep = waktu($getValue->dhuhur);
            $timeAshSep = waktu($getValue->ashar);
            $timeIsyaSep = waktu($getValue->isya);
            // ============================================

            // ========== 10 menit sesudah adzan ==========
            $timeSubuhAf = tambahWaktu($getValue->subuh);
            $timeDzuAf = tambahWaktu($getValue->dhuhur);
            $timeAshAf = tambahWaktu($getValue->ashar);
            $timeIsyaAf = tambahWaktu($getValue->isya);
            // ============================================

            // === Subuh ==
            if ($timeSubuhSep || $timeSubuhAf) {
                echo "sebelum atau sesudah subuh\n";
                echo $this->hadistPlay();
                $date = $timeSubuhAf;
                echo $date . "\n";
                for ($i = 0; $i < $jml; $i++) {
                    $ex = explode(':', $date);
                    $plusMin = '00' + rand(6, 20);
                    $plusHou = $ex[0] + rand(3, 6);
                    $time_schedule = sprintf('%02d', $plusHou) . ":" . sprintf('%02d', $plusMin);
                    // do {
                    //     $wak = $time_schedule;
                    // } while($plusHou <> $time_schedule);
                    echo $time_schedule . "\n";
                    $f = ModHadist::where('status_play', 0)->where('time_to_play', '-')->limit(1)->update(['time_to_play' => $time_schedule]);
                    if ($f) {
                        echo "berhasil";
                    } else {
                        echo "gagal";
                    }
                }
            }
            // === Dzuhur ==
            if ($timeDzuSep || $timeDzuAf) {
                echo "sebelum atau sesudah dzuhur\n";
                echo $this->hadistPlay();
                $date = $timeDzuAf;
                echo $date . "\n";
                $ex = explode(':', $date);
                $plusMin = '00' + rand(6, 20);
                $plusHou = $ex[0] + rand(1, 2);
                $time_schedule = sprintf('%02d', $plusHou) . ":" . sprintf('%02d', $plusMin);
                echo $time_schedule . "\n";
                $f = ModHadist::where('status_play', 0)->where('time_to_play', '-')->limit(1)->update(['time_to_play' => $time_schedule]);
                if ($f) {
                    echo "berhasil";
                } else {
                    echo "gagal";
                }
            }
            // === Ashar ==
            if ($timeAshSep || $timeAshAf) {
                echo "sebelum atau sesudah ashar\n";
                echo $this->hadistPlay();
                $date = $timeAshAf;
                echo $date . "\n";
                $ex = explode(':', $date);
                $plusMin = '00' + rand(6, 20);
                $plusHou = $ex[0] + rand(1, 2);
                $time_schedule = sprintf('%02d', $plusHou) . ":" . sprintf('%02d', $plusMin);
                echo $time_schedule . "\n";
                $f = ModHadist::where('status_play', 0)->where('time_to_play', '-')->limit(1)->update(['time_to_play' => $time_schedule]);
                if ($f) {
                    echo "berhasil";
                } else {
                    echo "gagal";
                }
            }
            // === Isya ==
            // if ($timeIsyaSep || $timeIsyaAf) {
            //     echo "sebelum atau sesudah ashar\n";
            //     echo $this->hadistPlay();
            //     $date = $timeIsyaAf;
            //     echo $date . "\n";
            //     $ex = explode(':', $date);
            //     $plusMin = '00' + rand(10, 30);
            //     $plusHou = $ex[0] + rand(0, 1);
            //     $time_schedule = sprintf('%02d', $plusHou) . ":" . sprintf('%02d', $plusMin);
            //     echo $time_schedule . "\n";
            //     $f = ModHadist::where('status_play', 0)->where('time_to_play', '-')->limit(1)->update(['time_to_play' => $time_schedule]);
            //     if ($f) {
            //         echo "berhasil";
            //     } else {
            //         echo "gagal";
            //     }
            // }


        }
    }

    public function hadistPlay()
    {
        $hadist = ModHadist::select('title', 'path')->where('status_play', 0)->inRandomOrder()->first();
        if (count($hadist) > 0) {
            echo $hadist->path . "\n";
        }
    }

    public function pauseMurotal()
    {
        $pid = exec("pidof play ");
        echo $pid;
        if ($pid != '') {
            exec("kill -STOP $pid");     // PAUSE AUDIO with SIGNAL STOP
            //exec('amixer sset PCM 88%'); // Volume naik karena akan adzan dan ada hadist play
        }
    }

    public function resumeMurrotal()
    {
        //exec('amixer sset PCM 88%'); // Volume turun jadi normal
        $pid = exec("pidof play ");
        if ($pid != '') {
            shell_exec("kill -CONT $pid"); // prosess without sudo
        }
    }
}
