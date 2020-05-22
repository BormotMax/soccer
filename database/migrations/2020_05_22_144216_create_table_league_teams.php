<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLeagueTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_teams', function (Blueprint $table) {
            $table->id();
            $table->integer('league_id')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->integer('points')->unsigned();
            $table->integer('played')->unsigned();
            $table->integer('won')->unsigned();
            $table->integer('draw')->unsigned();
            $table->integer('loss')->unsigned();
            $table->integer('diff')->unsigned();
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
        Schema::dropIfExists('league_teams');
    }
}
