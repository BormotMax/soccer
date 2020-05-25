<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{JsonResponse, Request};
use App\Models\{Team, League, Match};
use App\Services\MatchService;

class MatchController extends Controller
{
    /**
     * Play current week
     * @return JsonResponse
     */
    public function playOne()
    {
        $league = League::firstOrCreate(['name' => League::MAIN_LEAGUE]);
        $matchService = new MatchService($league);

        if ($league->current_week === $league->total_weeks) {
            $teams = Team::all();
            $matchService->startChampionship($teams);
        }

        $weekResults = $matchService->nextPlay();
        $league->refresh();

        return response()->json([
            'league' => $league,
            'matches' => $weekResults,
        ]);
    }

    /**
     * Play All weeks
     */
    public function playAll()
    {
        $league = League::firstOrCreate(['name' => League::MAIN_LEAGUE]);
        $matchService = new MatchService($league);

        $teams = Team::all();
        $matchService->startChampionship($teams);

        for ($i = 0; $i < $league->total_weeks; $i++) { 
            $weekResults = $matchService->nextPlay();
        }
        
        $league->refresh();

        return response()->json([
            'league' => $league,
            'matches' => $weekResults,
        ]);
    }
}
