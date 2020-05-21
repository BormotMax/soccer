<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Team;

class TeamsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        $teams = Team::all();

        return response()->json([
            'data' => [
                'teams' => $teams,
            ]
        ]);
    }
}