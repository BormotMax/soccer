<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Team, Match, LeagueTeam};

class League extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['current_week', 'name', 'total_weeks'];

    public $with = ['teams'];

    /**
     * Default league
     *
     */
    const MAIN_LEAGUE = 'sim_league';

    /**
     * League teams
     * @return Team Collection
     */
    public function teams()
    {
        return $this->hasMany(LeagueTeam::class);
    }

    /**
     * League matches
     */
    public function matches()
    {
        return $this->hasMany(Match::class);
    }

}
