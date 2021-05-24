<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModHadist extends Model
{
    //
    protected $table = 'list_hadistrand';
    protected $fillable = ['id', 'title', 'song_id', 'time_to_play', 'playlist_id', 'status_play', 'path', 'created_at'];
}
