<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Model\Team;

class LeagueTeam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'league_id',
        'team_id',
        'points',
        'played',
        'won',
        'draw',
        'loss',
        'diff',
    ];

    /**
     * Team
     * @return Team $team
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

}
