<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['nama_tempat','user_id','status'];

    public function userid()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
