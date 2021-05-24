<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $fillable = ['nama_file','ekstensi','lokasi','status','play_at','user_id'];

    public function audiouser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
}
