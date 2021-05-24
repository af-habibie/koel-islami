<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $table = 'times';
    protected $fillable = ['user_id','location_id','tahajud','subuh','syurooq','duha','dhuhur','ashar','maghrib','isya','status'];
}
