<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableListhadistrand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('list_hadistrand', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('song_id');
            $table->integer('playlist_id');
            $table->text('path');
            $table->string('time_to_play');
            $table->integer('status_play')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("list_hadistrand");
    }
}
