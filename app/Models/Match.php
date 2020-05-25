<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\League;

class Match extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'league_id',
        'week',
        'team_a_id',
        'team_b_id',
        'goals_a',
        'goals_b',
    ];

    /**
     * current League
     * @return League
     */
    public function league()
    {
        return $this->belongsTo(League::class);
    }
}
