<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Models\Time;
use App\Models\SongVolume;
use Illuminate\Console\Command;

class ComAudio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play:audio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     *
     * Memainkan murrotal tanpa menggunakan exec repeat, kelemahannya mungkin akan menunggu skitar kurang dari
     * 1 menit jika playlist nya habis dan mengulang kembali dari awal dan random
     */
    public function handle()
    {
        echo "Artisan Play Audio\n";
        
        $audio = Song::whereHas('playlists', function ($query) {
            $query->where('playlists.name', 'Murrotal');
        })->get()->shuffle();

        $pid = exec("pidof play");
        $adzan_play = exec("ps -A | grep -i 'play' | grep -v grep", $output);
        $var =0;
        $expl = explode(" ", $pid);
        $hour = date('H');
        //dd((count($expl) >= 2) ? "ADZAN PLAY":"NO ADZAN");
        if(count($expl) >= 2){
            
            $var = 1;
            
            $volmadzan = SongVolume::where('name_volume', 'adzan')->first();
            exec("".env('SET_AMIXER')." {$volmadzan->presentage}%");
        } else{
            //dd("else");
            $var = 2;
            $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
            $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
            if($hour >= 5 && $hour < 19){
                
                echo "SESI 1 JAM 5 - 19";
                exec("".env('SET_AMIXER')." {$volmsatu->presentage}%"); //PAGI-SAMPAI MALAM JAM 7
            }elseif($hour >= 19 && $hour <= 23){
                //dd("Masuk sini");
                echo "SESI 2 JAM 19 - 23";
                exec("".env('SET_AMIXER')); // MALEM JAM 7 SAMPAI JAM 5 PAGI
                
            }elseif($hour >= 1 && $hour <=5){
                echo "TENGAH MALAM JAM 1 - 5 SUBUH";
                exec("".env('SET_AMIXER')." {$volmdua->presentage}%", $out);
            }
        }
        
        //dd($var);
        //dd($var);
        if (!$pid) {             // jika jumlah pid nya cuma 1 maka play random murrotal
            
            $time = date('H:i'); // jam format 00:00
            
            
            $adzan = Time::where('status', 1)->first();
            // === atur volume saat mulai play murotal 
            // ================= Jaga-Jaga, Jangan dihapus dulu line ini buat Fitur Baru ====================
            // if($time >= waktu($adzan->subuh)){
            //     exec('amixer sset PCM 85%'); // Volume turun jadi normal
            // }
            // if($time >= tambahWaktu($adzan->isya, 15)){
            //     exec('amixer sset PCM 75%'); // Volume turun jadi normal
            // }
            // ============================  Sampai Sini =============================

            if ($time < $adzan->maghrib || $time > tambahWaktu($adzan->isya, 15)) {
                echo "eksekusi play";
                foreach ($audio as $key => $getAudio) {
                    echo "\n";
                    echo "File : " . $getAudio->title . "\n";
                    echo "Audio is playing .... \n";
		    //echo "$this->serverDocRoot/$getAudio->path";
                    exec("play {$this->serverDocRoot}/{$getAudio->path}");
                }
            } else {
                echo "Sedang diwaktu antara maghrib dan isya";
            }
        }
        // else{
        //     dd(count($expl));
        //     if(count($expl) >= 2){
        //         $var = 1;
                
        //         $volmadzan = SongVolume::where('name_volume', 'adzan')->first();
        //         exec("".env('SET_AMIXER')." {$volmadzan->presentage}%");
        //     } else{
        //         //dd("else");
        //         $var = 2;
        //         $volmsatu = SongVolume::where('name_volume', 'murotalsesisatu')->first();
        //         $volmdua = SongVolume::where('name_volume', 'murotalsesidua')->first();
        //         if($hour >= 5 && $hour < 19){
        //             exec("".env('SET_AMIXER')." {$volmsatu->presentage}%"); //PAGI-SAMPAI MALAM JAM 7
        //         }elseif($hour >= 19 && $hour <= 05){
        //             //dd("Masuk sini");
        //             exec("".env('SET_AMIXER')." {$volmdua->presentage}%"); // MALEM JAM 7 SAMPAI JAM 5 PAGI
        //         }
        //     }
        //     //echo "".env('SET_AMIXER')." 80%";
        // }
    }
}
