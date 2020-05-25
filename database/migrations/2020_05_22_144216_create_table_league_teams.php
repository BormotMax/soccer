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
            $table->integer('points')->unsigned()->default(0);
            $table->integer('played')->unsigned()->default(0);
            $table->integer('won')->unsigned()->default(0);
            $table->integer('draw')->unsigned()->default(0);
            $table->integer('loss')->unsigned()->default(0);
            $table->integer('diff')->default(0);
            $table->integer('goals')->unsigned()->default(0);
            $table->integer('out')->unsigned()->default(0);
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
