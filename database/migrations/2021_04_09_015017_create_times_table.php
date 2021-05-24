<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('location_id');
            $table->string('tahajud',5);
            $table->string('subuh',5);
            $table->string('syurooq',5);
            $table->string('duha',5);
            $table->string('dhuhur',5);
            $table->string('ashar',5);
            $table->string('maghrib',5);
            $table->string('isya',5);
            $table->tinyInteger('status')->default('0');
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
        Schema::dropIfExists('times');
    }
}
