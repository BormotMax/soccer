<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'goals_a_id',
        'goals_b_id',
    ];
}
