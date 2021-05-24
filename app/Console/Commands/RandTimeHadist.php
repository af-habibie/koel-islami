<?php

namespace App\Console\Commands;

use App\Models\ModHadist;
use App\Models\Song;
use App\Services\RandTimeHadistService;
use Illuminate\Console\Command;

class RandTimeHadist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hadist:rand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Inject the RandTimeHadist Service Class.
     *
     * @uses \App\Services\RandTimeHadistService
     */
    protected $randTimeService;

    /**
     * Contains clone of Song-><-Hadist
     *
     * @var
     */
    protected $songHadistInstance;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->randTimeService = new RandTimeHadistService();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $getHadistSong = Song::whereHas('playlists', function ($query) {
            $query->where('playlists.name', 'Hadist');
        })->get();

        $this->songHadistInstance = clone $getHadistSong;

        $akan_datang = ModHadist::pluck('song_id');
        $saat_ini = $getHadistSong->pluck('id');
        $dif = $saat_ini->diff($akan_datang);

        if ($saat_ini->isDifference($akan_datang)) {
            $this->randTimeService->insert($dif);
            //$this->randTimeService->updateIfDupe();
            dd($dif);
        }

        dd($dif);
    }
}
