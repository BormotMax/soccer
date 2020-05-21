<?php

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = [
            [
                'name' => 'Chelsea',
                'power' => 1
            ],
            [
                'name' => 'Arsenal',
                'power' => 2
            ],
            [
                'name' => 'Manchester City',
                'power' => 3
            ],
            [
                'name' => 'Liverpool',
                'power' => 4
            ],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }
}
