<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{JsonResponse, Request};
use App\Models\Team;

class MatchController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function play(Request $request)
    {
        $request->validate([
            'teams' => 'required|array|min:2|max:2',
            'teams.*' => 'required|exists:teams,id'
        ]);

        $teams = Team::find($request->teams);

        $goals1 = $this->calculateGoals($teams[0]->power, $teams[1]->power);
        $goals2 = $this->calculateGoals($teams[1]->power, $teams[0]->power);

        $result = ['teams' => 
            [
                [
                    'id' => $teams[0]->id,
                    'goals' => $goals1,
                    'points' => $this->calculatePoints($goals1, $goals2),
                    'out' => $goals2,
                ],
                [
                    'id' => $teams[1]->id,
                    'goals' => $goals2,
                    'points' => $this->calculatePoints($goals2, $goals1),
                    'out' => $goals2,
                ],
            ],
            'winner' => null
        ];

        if ($goals1 > $goals2) {
            $result['winner'] = $teams[0]->id;
        } elseif ($goals1 < $goals2) {
            $result['winner'] = $teams[1]->id;
        }

        return response()->json([
            'data' => $result
        ]);
    }

    /**
     * @param int $teamPower
     * @param int $opponentPower
     * 
     * @return int $goals
     */
    private function calculateGoals(int $teamPower, int $opponentPower): int
    {
        return random_int(0, round($teamPower / $opponentPower) * 5);
    }

    /**
     * @param int $goals
     * @param int $opponentGoals
     * 
     * @return int $points
     */
    private function calculatePoints(int $goals, int $opponentGoals): int
    {
        $points = $goals * 5 - $opponentGoals;
        return $points > 0 ? $points : 0;
    }
}