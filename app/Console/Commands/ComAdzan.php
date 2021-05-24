<?php

namespace App\Console\Commands;

use App\Models\ModHadist;
use App\Models\Time;
use App\Models\SongVolume;
use Illuminate\Console\Command;

class ComAdzan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play:adzan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play Adzan 5 Times A Day';

    /**
     * To bind document root folder so that it can be changed later on
     *
     * @var config helper
     */
    private $serverDocRoot;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->serverDocRoot = config('app.server_documentroot');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        // echo "Play adzan\n";
        $time = date('H:i'); // jam format 00:00
        $day = date('N');    // ===== notifikasi hari dalam seminggu menggunakan angka, 1 = senin sampe 7 = minggu
        $volhad = SongVolume::where('name_volume', 'hadist')->first();
        $voladzan = SongVolume::where('name_volume', 'adzan')->first();
        $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
        $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
        $adzan = Time::where('status', 1)->get();
        $hadist = ModHadist::where('status_play', 0)->get();
        $this->info("Executed Succesfully");

        // === play hadist random == 
        foreach ($hadist as $keyHadist) {
            if ($time == $keyHadist->time_to_play) {
                $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$volhad->presentage}%");
		//exec("amixer sset PCM {$volhad->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/{$keyHadist->path}");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
		//exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                ModHadist::where('time_to_play', $keyHadist->time_to_play)->update(['status_play' => 1]);
                $this->resumeAudioDua();
            }
        }
        // ======

        //exec('play /var/www/islami-mp/public/adzan/adzan-anak-muhammad-thaha-mp3.mp3');
        foreach ($adzan as $key => $getSchedule) {
            echo "Volume adzan : {$voladzan->presentage}% \n";
            echo "Volume hadist : {$volhad->presentage}% \n";
            echo "Volume murotal siang : {$volmsatu->presentage}% \n";
            echo "Volume murotal malam : {$volmdua->presentage}% \n\n";
            echo "Tahajud : $getSchedule->tahajud \n";
            echo "Subuh   : $getSchedule->subuh \n";
            echo "Duhur   : $getSchedule->dhuhur \n";
            echo "Ashar   : $getSchedule->ashar \n";
            echo "Mahgrib : $getSchedule->maghrib \n";
            echo "Isya    : $getSchedule->isya \n\n";

            // ========== 10 menit sebelum adzan ==========
            $timeSubuhSep = waktu($getSchedule->subuh);
            $timeDzuSep = waktu($getSchedule->dhuhur);
            $timeAshSep = waktu($getSchedule->ashar);
            $timeMaghSep = waktu($getSchedule->maghrib);
            $timeIsyaSep = waktu($getSchedule->isya);
            // ============================================

            // ========== 5 menit sebelum adzan ==========
            $timeSubuhLi = waktuLima($getSchedule->subuh);
            $timeDzuLi = waktuLima($getSchedule->dhuhur);
            $timeAshLi = waktuLima($getSchedule->ashar);
            $timeMaghLi = waktuLima($getSchedule->maghrib);
            $timeIsyaLi = waktuLima($getSchedule->isya);
            // ============================================

            // ======== tahajud
            if ($time == $getSchedule->tahajud) {
                $this->tahajud($getSchedule->tahajud);
            } else
                if ($time > $getSchedule->subuh) {
                    $this->resumeAudio($getSchedule->tahajud);
                }

            // ======== subuh
            if ($time == $timeSubuhSep) {
                echo "10 Menit lagi Adzan Subuh\n";
                $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
		        //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/ten/subuh.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                $this->resumeAudioDua();
            } else
                if ($time == $timeSubuhLi) {
                    echo "5 Menit lagi Adzan Subuh\n";
                    $this->pauseAudio();
                    exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                    //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                    exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/five/subuh.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                    //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                    $this->resumeAudioDua();
                } else
                    if ($time == $getSchedule->subuh) {
                        $this->subuh($getSchedule->subuh);
                    } else
                        if ($time > $getSchedule->subuh) {
                            $this->resumeAudio($getSchedule->subuh);
                        }

            // ========== Waktu Duha
            if ($time == $getSchedule->duha) {
                echo "Waktu duha\n";
                $call = hadistNempel("Hadist Duha");
                $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/{$call}");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                $this->resumeAudioDua();
            }
            // ======================

            // ========== Duhur dan Jumat
            if ($time == $timeDzuSep) {
                if ($day == '5') {
                    echo "10 Menit lagi Solat Jumat\n";
                    $this->pauseAudio();
                    exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                    //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                    exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/ten/dzhuhur.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                    //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                    $this->resumeAudioDua();
                } else {
                    echo "10 Menit lagi Adzan Duhur\n";
                    $this->pauseAudio();
                    exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                    //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                    exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/ten/dzhuhur.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                    //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                    $this->resumeAudioDua();
                }
            } else
                if ($time == $timeDzuLi) {
                    if ($day == '5') {
                        echo "5 Menit lagi Solat Jumat\n";
                        $this->pauseAudio();
                        exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                        //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                        exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                        exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                        exec("play {$this->serverDocRoot}/public/adzan/five/dzhuhur.mp3");
                        exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                        exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                        //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                        $this->resumeAudioDua();
                    } else {
                        echo "5 Menit lagi Adzan Duhur\n";
                        $this->pauseAudio();
                        exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                        //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                        exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                        exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                        exec("play {$this->serverDocRoot}/public/adzan/five/dzhuhur.mp3");
                        exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                        exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                        //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                        $this->resumeAudioDua();
                    }
                } else
                    if ($time == $getSchedule->dhuhur) {
                        $this->duhur($getSchedule->dhuhur);
                    } else
                        if ($time > $getSchedule->dhuhur) {
                            $this->resumeAudio($getSchedule->dhuhur);
                        }

            // =========== Ashar
            if ($time == $timeAshSep) {
                echo "10 Menit lagi Adzan Ashar";
                $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/ten/ashar.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                $this->resumeAudioDua();
            } else
                if ($time == $timeAshLi) {
                    echo "5 Menit lagi Adzan Ashar";
                    $this->pauseAudio();
                    exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                    //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                    exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/five/ashar.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                    //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                    $this->resumeAudioDua();
                } else
                    if ($time == $getSchedule->ashar) {
                        $this->ashar($getSchedule->ashar);
                    } else
                        if ($time > $getSchedule->ashar) {
                            $this->resumeAudio($getSchedule->ashar);
                        }

            // =========== Maghrib
            if ($time == $timeMaghSep) {
                echo "10 Menit lagi Adzan Maghrib\n";
                $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/ten/magrib.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                $this->resumeAudioDua();
            } else
                if ($time == $timeMaghLi) {
                    echo "5 Menit lagi Adzan Maghrib\n";
                    $this->pauseAudio();
                    exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                    //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                    exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/five/magrib.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                    //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                    $this->resumeAudioDua();
                } else
                    if ($time == $getSchedule->maghrib) {
                        $this->maghrib($getSchedule->maghrib);
                    }
            // else
            //     if ($time > $getSchedule->maghrib) {
            //         $this->resumeAudio($getSchedule->maghrib);
            //     }

            // =========== Isya
            if ($time == $timeIsyaSep) {
                echo "10 Menit lagi Adzan Isya\n";
                // $this->pauseAudio();
                // exec("play {$this->serverDocRoot}/public/adzan/ten/isya.mp3");
                // $this->resumeAudioDua();
                // $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/ten/isya.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                //$this->resumeAudioDua();
            } else
                if ($time == $timeIsyaLi) {
                    echo "5 Menit lagi Adzan Isya\n";
                    // $this->pauseAudio();
                    // exec("play {$this->serverDocRoot}/public/adzan/five/isya.mp3");
                    // $this->resumeAudioDua();
                    //$this->pauseAudio();
                    exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                    //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                    exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                    exec("play {$this->serverDocRoot}/public/adzan/five/isya.mp3");
                    exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                    //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
                    //$this->resumeAudioDua();
                } else
                    if ($time == $getSchedule->isya) {
                        $this->isya($getSchedule->isya);
                    } else
                        if ($time > $getSchedule->isya) {
                            $this->resumeAudio($getSchedule->isya);
                            // if($time == tambahWaktu($getSchedule->isya,15)){
                            //     exec('amixer sset PCM 80%'); // Volume turun
                            // }
                        }

        }
    }

    public function pauseAudio()
    {
        // $audio = Song::all();
        // foreach ($audio as $key => $getValue) {
        // $pid = exec("pidof sudo play {$this->serverDocRoot}/{$getValue->path}");
        $pid = exec("pidof play ");
        echo $pid;
        //$exp = explode(' ', $pid);
        if ($pid != '') {
            exec("kill -STOP $pid"); // PAUSE AUDIO with SIGNAL STOP
            // exec('sudo kill -STOP ' . $exp[2]); // PAUSE AUDIO with SIGNAL STOP
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            //exec('amixer sset PCM 100%'); // Volume naik karena akan adzan dan ada hadist play
        }
        // }
    }

    public function resumeAudio($whatSchedule)
    {
        $time = date('H:i'); // jam format 00:00
        $addSep = tambahWaktu($whatSchedule, 15);
        if ($time == $addSep) {
            // $audio = Song::all();
            // foreach ($audio as $key => $getValue) {
            //$pid = exec("pidof sudo play {$this->serverDocRoot}/{$getValue->path}");
            $pid = exec("pidof play ");
            //$exp = explode(' ', $pid);
            // RESUME PLAY AUDIO with SIGNAL CONTINUE
            shell_exec("kill -CONT $pid"); // prosess without sudo
            // shell_exec("sudo kill -CONT $exp[2]"); // process with sudo
            // }
        }
        // }
    }

    public function resumeAudioDua()
    {
        //exec('amixer sset PCM 100%'); // Volume turun jadi normal
        // $audio = Song::all();
        // foreach ($audio as $key => $getValue) {
        //$pid = exec("pidof sudo play {$this->serverDocRoot}/{$getValue->path}");
        $pid = exec("pidof play ");
        //$exp = explode(' ', $pid);
        if ($pid != '') {
            // PLAY AUDIO with SIGNAL CONTINUE
            shell_exec("kill -CONT $pid"); // prosess without sudo
            // shell_exec("sudo kill -CONT $exp[2]"); // process with sudo
        }
        // }
    }

    public function tahajud($getTimeSub)
    {
        $tahAdzan = Time::where('tahajud', $getTimeSub)->where('status', 1)->latest('created_at')->first();
        if ($tahAdzan) {
            echo "Waktunya adzan tahajud\n";
            $call = hadistNempel("Hadist Tahajud");
            $this->pauseAudio();
            exec("play {$this->serverDocRoot}/public/adzan/alert/alert.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/adzan/tahajud.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/{$call}");
        }
    }

    public function subuh($getTimeSub)
    {
        $volhad = SongVolume::where('name_volume', 'hadist')->first();
        $voladzan = SongVolume::where('name_volume', 'adzan')->first();
        $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
        $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
        $subAdzan = Time::where('subuh', $getTimeSub)->where('status', 1)->latest('created_at')->first();
        if ($subAdzan) {
            echo "Waktunya adzan subuh\n";
            $call = hadistNempel("Hadist Subuh");
            $this->pauseAudio();
            exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
            //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
            exec("play {$this->serverDocRoot}/public/adzan/alert/alert.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/adzan/fajr_128_44.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/{$call}");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
            //exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
            //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume turun karena akan adzan dan ada hadist play
        }
    }

    public function duhur($getTimeSub)
    {
        $volhad = SongVolume::where('name_volume', 'hadist')->first();
        $voladzan = SongVolume::where('name_volume', 'adzan')->first();
        $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
        $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
        $d = date('N');
        $dzuAdzan = Time::where('dhuhur', $getTimeSub)->where('status', 1)->latest('created_at')->first();
        if ($dzuAdzan) {
            $call = hadistNempel("Hadist Dzuhur");
            if ($d == '5') {
                echo "Waktunya Solat Jumat\n";
                $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/alert.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/adzan/mecca_56_22.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/{$call}");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume normal
            } else {
                echo "Waktunya adzan dzuhur\n";
                $this->pauseAudio();
                exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
                //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
                exec("play {$this->serverDocRoot}/public/adzan/alert/alert.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/adzan/mecca_56_22.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("play {$this->serverDocRoot}/{$call}");
                exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
                //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume normal
            }
        }
    }

    public function ashar($getTimeSub)
    {
        $volhad = SongVolume::where('name_volume', 'hadist')->first();
        $voladzan = SongVolume::where('name_volume', 'adzan')->first();
        $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
        $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
        $ashAdzan = Time::where('ashar', $getTimeSub)->where('status', 1)->latest('created_at')->first();
        if ($ashAdzan) {
            echo "Waktunya adzan ashar\n";
            $call = hadistNempel("Hadist Ashar");
            $this->pauseAudio();
            exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
            //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
            exec("play {$this->serverDocRoot}/public/adzan/alert/alert.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/adzan/mecca_56_22.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/{$call}");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
            //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume normal
        }
    }

    public function maghrib($getTimeSub)
    {
        $volhad = SongVolume::where('name_volume', 'hadist')->first();
        $voladzan = SongVolume::where('name_volume', 'adzan')->first();
        $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
        $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
        $maghAdzan = Time::where('maghrib', $getTimeSub)->where('status', 1)->latest('created_at')->first();
        if ($maghAdzan) {
            echo "Waktunya adzan maghrib\n";
            $call = hadistNempel("Hadist Maghrib");
            $this->pauseAudio();
            exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
            //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
            exec("play {$this->serverDocRoot}/public/adzan/alert/alert.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/adzan/mecca_56_22.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/{$call}");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("".env('SET_AMIXER')." {$volmsatu->presentage}%");
            //exec("amixer sset PCM {$volmsatu->presentage}%"); // Volume normal
        }
    }

    public function isya($getTimeSub)
    {
        $volhad = SongVolume::where('name_volume', 'hadist')->first();
        $voladzan = SongVolume::where('name_volume', 'adzan')->first();
        $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
        $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
        $isyAdzan = Time::where('isya', $getTimeSub)->where('status', 1)->latest('created_at')->first();
        if ($isyAdzan) {
            echo "Waktunya adzan isya\n";
            $call = hadistNempel("Hadist Isya");
            // $this->pauseAudio();
            exec("".env('SET_AMIXER')." {$voladzan->presentage}%");
            //exec("amixer sset PCM {$voladzan->presentage}%"); // Volume naik karena akan adzan dan ada hadist play
            exec("play {$this->serverDocRoot}/public/adzan/alert/alert.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/adzan/mecca_56_22.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/tingnung.mp3");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("play {$this->serverDocRoot}/{$call}");
            exec("play {$this->serverDocRoot}/public/adzan/alert/jeda2detik.mp3");
            exec("".env('SET_AMIXER')." {$volmdua->presentage}%");
            //exec("amixer sset PCM {$volmdua->presentage}%"); // Volume normal
        }
    }
}
