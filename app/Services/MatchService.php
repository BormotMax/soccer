<?php

namespace App\Services;

use App\Models\{Match, League, Team, LeagueTeam};
use Carbon\Carbon;

class MatchService
{

    /**
     * @var League $league
     */
    private $league;

    /**
     * @var array $weeks
     */
    private $weeks;

    /**
     * @param League $leagus
     */
    public function __construct($league)
    {
        $this->league = $league;
        $this->weeks = [];
    }

    /**
     * Create new Champonship
     * @param $teams
     */
    public function startChampionship($teams)
    {
        $this->league->teams()->delete();
        $this->league->matches()->delete();
        $this->league->update(['current_week' => 0]);
        $this->createSchedule($teams);
        $this->writeSchedule();
    }

    /**
     * Create initial Shedule
     * @param $teams
     */
    private function createSchedule($teams)
    {
        foreach ($teams as $teamA) {
            $teamAId = $teamA->id;
            $leagueTeam = LeagueTeam::create([
                'league_id' => $this->league->id,
                'team_id' => $teamA->id,
            ]);
            foreach ($teams as $teamB) {
                $teamBId = $teamB->id;
                if (
                    $teamAId !== $teamBId
                    && !$this->isMatchInSchedule($teamAId, $teamBId)
                ) {
                    $weeks = $this->addMatch($teamAId, $teamBId);
                }
            }
        }
    }

    /** 
     * Check if Team in currentWeek
     * @param int $teamId
     * @param array $week
     * 
     * @return bool
    */
    private function isTeamInCurrentWeek(int $teamId, array $week): bool
    {
        $isInWeek = false;
        foreach ($week as $match) {
            if (in_array($teamId, $match)) {
                $isInWeek = true;
            }
        }

        return $isInWeek;
    }

    /** 
     * Check if both teams in Match Schedule
     * @param int $teamAId
     * @param int $teamBId
     * 
     * @return bool
    */
    private function isMatchInSchedule(int $teamAId, int $teamBId): bool
    {
        $isInSchedule = false;
        foreach ($this->weeks as $week) {
            foreach ($week as $match) {
                if (in_array($teamAId, $match) && in_array($teamBId, $match)) {
                    $isInSchedule = true;
                }
            }
        }

        return $isInSchedule;
    }

    /**
     * @param int $teamAId
     * @param int $teamBId
     */
    private function addMatch(int $teamAId, int $teamBId)
    {
        $isMatchAdded = false;
        $matchesInWeek = config('league.matches_in_week');
        for ($i=0; $i < count($this->weeks); $i++) { 
            if (
                !$this->isTeamInCurrentWeek($teamAId, $this->weeks[$i])
                && !$this->isTeamInCurrentWeek($teamBId, $this->weeks[$i])
                && count($this->weeks[$i]) < $matchesInWeek
            ) {
                $this->weeks[$i][] = [$teamAId, $teamBId];
                $isMatchAdded = true;
            }
        }
        if (!$isMatchAdded) {
            $this->weeks[] = [[$teamAId, $teamBId]];
        }
        $week = $this->getCurrentWeek();
    }

    /**
     * Write initial Shedule
     */
    private function writeSchedule()
    {
        $createData = [];
        for ($i = 0; $i < count($this->weeks); $i++) {
            $week = $this->weeks[$i];
            foreach ($week as $match) {
                $createData[] = [
                    'league_id' => $this->league->id,
                    'week' => $i +1,
                    'team_a_id' => $match[0],
                    'team_b_id' => $match[1],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        $this->league->update(['total_weeks' => $i]);

        Match::insert($createData);
    }

    /**
     * @return array $week
     */
    private function getCurrentWeek(): array
    {
        $weeksCount = count($this->weeks);
        return $weeksCount > 0 ? $this->weeks[$weeksCount - 1] : [];
    }

    /**
     * @return array $results
     */
    public function nextPlay(): array
    {
        $weekNumber = $this->league->current_week + 1;
        $this->league->update(['current_week' => $weekNumber]);
        $week = $this->league->matches()->where('week', $weekNumber)->get();
        $results = [];
        foreach ($week as $match) {
            $teamA = Team::find($match->team_a_id);
            $teamB = Team::find($match->team_b_id);
            $goalsA = $this->calculateGoals($teamA->power, $teamB->power);
            $goalsB = $this->calculateGoals($teamB->power, $teamA->power);
            $teamAStatistic = $this->calculateStatistic($goalsA, $goalsB);
            $teamBStatistic = $this->calculateStatistic($goalsB, $goalsA);
            $this->updateStatistic($teamA->id, $teamAStatistic);
            $this->updateStatistic($teamB->id, $teamBStatistic);

            $match->update([
                'goals_a' => $goalsA,
                'goals_b' => $goalsB
            ]);


            $results[] = [
                'teamA' => $teamA->name,
                'teamB' => $teamB->name,
                'goalsA' => $goalsA,
                'goalsB' => $goalsB,
            ];
        }

        return $results;
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

    /**
     * @param int $goalsA
     * @param int $goalsB
     * 
     * @return array statistic
     */
    private function calculateStatistic(int $goalsA, int $goalsB): array
    {
        return [
            'points' => $this->calculatePoints($goalsA, $goalsB),
            'won' => $goalsA > $goalsB,
            'loss' => $goalsA < $goalsB,
            'draw' => $goalsA === $goalsB,
            'diff' => $goalsA - $goalsB,
            'goals' => $goalsA,
            'out' => $goalsB
        ];
    }

    /**
     * Update team statistic in League
     * @param $teamId
     */
    private function updateStatistic(int $teamId, array $statistic)
    {
        // $currentStatistic = LeagueTeam
        $currentStatistic = $this->league->teams()->where('team_id', $teamId)->first();
        $currentStatistic->points += $statistic['points'];
        $currentStatistic->won += $statistic['won'];
        $currentStatistic->loss += $statistic['loss'];
        $currentStatistic->draw += $statistic['draw'];
        $currentStatistic->diff += $statistic['diff'];
        $currentStatistic->goals += $statistic['goals'];
        $currentStatistic->out += $statistic['out'];
        $currentStatistic->save();

        return $currentStatistic;
    }
}