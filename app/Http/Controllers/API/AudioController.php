<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Auth;
use File;
use App\Models\Audio;
use App\Models\Time;
use Illuminate\Support\Facades\App;

class AudioController extends Controller
{
    /**
     * To bind document root folder so that it can be changed later on
     *
     * @var config helper
     */
    private $serverDocRoot;

    public function __construct()
    {
        $this->serverDocRoot = config('app.server_documentroot');
    }

    public function index()
    {
        $data = Audio::orderBy('nama_file', 'ASC')->paginate(10);
        return response()->json($data);
    }

    public function NotifiMurrotal(){
        $id = exec("pidof play");
        $sh = exec("ps -o cmd fp {$id}");
        // print_r($sh);
        $exp = explode('murrotal/', $sh);
        $surat = $exp[1];
        return response()->json(['notif' => $surat], 202);
    }

    public function togglePlayPause()
    {
        $id = exec("pidof play");
        if($id){
            echo "Pause murrotal";
            exec("kill -STOP $id");
            return response()->json(['status' => 'pause'], 200);
        } else {
            echo "Play murrotal";
            exec("kill -CONT $id");
            return response()->json(['status' => 'play'], 200);
        }
    }

    // ====== Method untuk mp3 Adzan dan Alert
    public function mp3AdzanAlert()
    {
        $time = date('H:i');
        // $time = '04:31';
        $adzan = Time::where('status', 1)->get();

        foreach ($adzan as $key => $getValue) {
            $tahajud = $getValue->tahajud;
            $fajr = $getValue->subuh;
            $syuruq = $getValue->syurooq;
            $dzuhur = $getValue->dhuhur;
            $ashar = $getValue->ashar;
            $maghrib = $getValue->maghrib;
            $isya = $getValue->isya;

            // ===== 10 menit sebelum adzan
            if ($time == waktu($fajr)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/ten/subuh.mp3')]);
            }
            if ($time == waktu($dzuhur)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/ten/dzhuhur.mp3')]);
            }
            if ($time == waktu($ashar)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/ten/ashar.mp3')]);
            }
            if ($time == waktu($maghrib)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/ten/magrib.mp3')]);
            }
            if ($time == waktu($isya)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/ten/isya.mp3')]);
            }
            // =====================================

            // ===== 5 menit sebelum adzan
            if ($time == waktuLima($fajr)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/five/subuh.mp3')]);
            }
            if ($time == waktuLima($dzuhur)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/five/dzhuhur.mp3')]);
            }
            if ($time == waktuLima($ashar)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/five/ashar.mp3')]);
            }
            if ($time == waktuLima($maghrib)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/five/magrib.mp3')]);
            }
            if ($time == waktuLima($isya)) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/five/isya.mp3')]);
            }
            // ====================================

            // ========== MP3 Adzan ==========
            if ($time == $fajr) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/adzan/fajr_128_44.mp3')]);
            }
            if ($time == $tahajud ||
                $time == $dzuhur ||
                $time == $ashar ||
                $time == $maghrib ||
                $time == $isya) {
                return response()->json(['mp3' => App::staticUrl('public/adzan/adzan/mecca_56_22.mp3')]);
            }
            // ====================================

        }
    }

    // ====== UPLOAD FILE untuk audio
    public function store(Request $request)
    {
        date_default_timezone_set(env('APP_TMZ', 'Asia/Jakarta'));
        $file = $request->file('nama_file');
        $name = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $size = $file->getSize();
        $type = explode('/', $file->getClientMimeType());
        $filetype = $type[1];
        $random_name = rand(12345, 6789) . "_" . $name;

        if ($size < '100000000') { // max 100mb
            if ($ext == 'mp3') {
                $file->move('audio/', $random_name);
                $save = Audio::create([
                    'user_id' => 1,
                    'nama_file' => $request->nm_file,
                    'ekstensi' => $ext,
                    'lokasi' => $random_name,
                    'status' => '0',
                    // 'play_at' => $cek,
                ]);

                return response()->json(['status' => 'ok'], 200);
            } else {
                return response()->json(['status' => 'wrong eks'], 403);
            }
        } else {
            return response()->json(['status' => 'kegedean filenya'], 403);
        }
    }

    // ====== UPLOAD FILE UNTUK ADZAN dan ALERT
    public function saveAdzanAlert(Request $request)
    {
        date_default_timezone_set(env('APP_TMZ', 'Asia/Jakarta'));
        $file = $request->file('nama_file');
        $pilih = $request->type; // pilihan berupa dropdown adzan, alert 10 menit, alert 5 menit, tahajud, alert hadist
        $name = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $size = $file->getSize();
        $type = explode('/', $file->getClientMimeType());
        $filetype = $type[1];
        $random_name = rand(12345, 6789) . "_" . $name;

        if ($size < '20000000') { // max 20mb
            if ($ext == 'mp3') {
                if ($pilih == 'adzan' || $pilih == 'tahajud') {
                    $file->move('adzan/adzan/', $random_name);
                }
                if ($pilih == 'alert10') {
                    $file->move('adzan/ten/', $random_name);
                }
                if ($pilih == 'alert5') {
                    $file->move('adzan/five/', $random_name);
                }
                if ($pilih == 'alerthadist') {
                    $file->move('adzan/alert/', $random_name);
                }
                $save = Audio::create([
                    'user_id' => 1,
                    'nama_file' => $request->nm_file,
                    'ekstensi' => $ext,
                    'lokasi' => $random_name,
                    'status' => '0',
                    // 'play_at' => $cek,
                ]);

                return response()->json(['status' => 'ok'], 200);
            } else {
                return response()->json(['status' => 'wrong eks'], 403);
            }
        } else {
            return response()->json(['status' => 'kegedean filenya'], 403);
        }
    }

    // ==============================

    public function nonaktif($id)
    {
        // $data = \App\Audio::where('id',$id)->first();

        Audio::where('id', $id)->update(['status' => 0]);

        return response()->json(['status' => 'status off'], 200);
    }

    public function ubah($id)
    {
        $upd = Audio::where('id', $id)->update(['status' => 1]);
        // \App\Audio::where('id','!=',$id)->update(['status' => 0]);

        return response()->json(['status' => 'ok'], 200);
    }

    public function destroy($id)
    {
        $dapat = Audio::find($id);
        File::delete('audio/' . $dapat->lokasi);
        $dapat->delete();

        return response()->json(['status' => 'ok'], 200);
    }

    public function edit($id)
    {
        $edits = Audio::find($id);
        $ex = explode(':', $edits->play_at);
        return response()->json(['edit' => $edits, 'pisah' => $ex]);
    }

    public function update(Request $request, $id)
    {
        $edits = Audio::find($id);
        $sp1 = sprintf('%02d', $request->jam);
        $sp2 = sprintf('%02d', $request->menit);
        $im = $sp1 . ':' . $sp2;
        Audio::where('id', $id)->update(['play_at' => $im]);

        return response()->json(['status' => 'ok']);
    }

    public function apiUpdate(Request $request, $id)
    {
        $edits = Audio::find($id);
        $sp1 = sprintf('%02d', $request->jam);
        $sp2 = sprintf('%02d', $request->menit);
        $im = $sp1 . ':' . $sp2;
        Audio::where('id', $id)->update(['play_at' => $im]);

        return response()->json(['status' => 'ok']);
    }

    public function playAudio($id)
    {
        $play = Audio::find($id);
        $pid = shell_exec("pidof sudo play {$this->serverDocRoot}/public/audio/{$play->lokasi}");
        echo $pid;
        $exp = explode(' ', $pid);
        if ($pid == '') {
            shell_exec("sudo play {$this->serverDocRoot}/public/audio/{$play->lokasi}");
        } else {
            shell_exec("sudo kill -CONT $exp[0]"); // prosess without sudo
            shell_exec("sudo kill -CONT $exp[1]"); // process with sudo
        }
        return response()->json(['status' => 'ok']);
    }

    public function pauseAudio($id)
    {
        $play = Audio::find($id);
        $pid = shell_exec("pidof play {$this->serverDocRoot}/public/audio/{$play->lokasi}");
        exec('sudo kill -STOP ' . $pid);
        // posix_kill($pid, SIGSTOP); // PAUSE AUDIO with SIGNAL STOP
        return response()->json(['status' => 'ok']);
    }

    public function stopAudio($id)
    {
        $play = Audio::find($id);
        $pid = shell_exec("pidof play {$this->serverDocRoot}/public/audio/{$play->lokasi}");

        exec('sudo kill -9 ' . $pid);
        return response()->json(['status' => 'ok']);
    }
}
