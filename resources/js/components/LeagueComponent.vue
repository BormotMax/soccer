<template>
    <div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <h3 class="text-center">League <b>{{league.name}}</b> Table</h3>
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th class="px-3 py-1">Teams</th>
                            <th class="px-2 py-1">PTS</th>
                            <th class="px-2 py-1">P</th>
                            <th class="px-2 py-1">W</th>
                            <th class="px-2 py-1">D</th>
                            <th class="px-2 py-1">L</th>
                            <th class="px-2 py-1">GD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="team in teams" :key="team.id">
                            <td class="border px-4 py-2"> {{ team.name }}</td>
                            <td class="border px-4 py-2"> {{ team.points }}</td>
                            <td class="border px-4 py-2"> {{ team.played }}</td>
                            <td class="border px-4 py-2"> {{ team.won }}</td>
                            <td class="border px-4 py-2"> {{ team.draw }}</td>
                            <td class="border px-4 py-2"> {{ team.loss }}</td>
                            <td class="border px-4 py-2"> {{ team.diff }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex flex-1">
                    <button
                        @click.prevent="handlePlayAllClick"
                        class="flex-auto bg-transparent hover:bg-blue-500 text-blue-700 hover:text-white rounded"
                    >
                        Play all
                    </button>
                    <button
                        @click.prevent="handleNextClick"
                        class="flex-auto bg-transparent hover:bg-blue-500 text-blue-700 hover:text-white rounded"
                    >
                        Next week
                    </button>
                </div>
            </div>
            <div class="box-content border-2 border-gray-400 p-4">
                <h3 class="text-center">Match Results</h3>
                <h5 v-if="currentWeek" class="text-center">
                    {{currentWeek}}<sup>th</sup> Week Match Result
                </h5>
                <div>
                    <div v-for="result in results" :key="result.teamA" class="flex flex-wrap">
                        <span class="w-1/3">{{ result.teamA }}</span>
                        <span class="w-1/3 text-center">{{ result.goalsA }} - {{ result.goalsB }}</span>
                        <span class="w-1/3 text-right">{{ result.teamB }}</span>
                    </div>
                </div>
            </div>
            <div class="box-content border-2 border-gray-400 p-4">
                <h5 v-if="currentWeek" class="text-center">
                    {{currentWeek}}<sup>th</sup> of Championship (total {{totalWeeks}} weeks)
                </h5>
                <div>
                    <div v-for="team in totalTable" :key="team.id" class="grid grid-cols-2">
                        <span> {{ team.name }}</span>
                        <span class="text-right"> {{ team.points }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                weeks: [],
                teams: [],
                league: {
                    name: ''
                },
                results: [],
                currentWeek: 0,
                totalWeeks: 0
            }
        },
        computed: {
            apiHeaders: () => {
                return {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            },
            totalTable: function() {
                return this.teams.sort( (a, b) => b.points - a.points);
            }
        },
        mounted() {
            this.fetchTeams();
        },
        methods: {
            fetchTeams() {
                const url = process.env.MIX_API + '/teams';
                axios.get(url, this.apiHeaders).then((res) => {
                    if (!res.data) {
                        return;
                    }
                    this.teams = res.data.data.teams;
                }).catch((err) => {});
            },
            handleNextClick() {
                const url = process.env.MIX_API + '/playone';
                axios.post(url, {}, this.apiHeaders).then((res) => {
                    if (!res.data) {
                        return;
                    }
                    this.updateLeague(res.data);
                }).catch((err) => {});
            },
            handlePlayAllClick() {
                const url = process.env.MIX_API + '/playall';
                axios.post(url, {}, this.apiHeaders).then((res) => {
                    if (!res.data) {
                        return;
                    }
                    this.updateLeague(res.data);
                }).catch((err) => {});
            },
            updateLeague(results) {
                this.league.name = results.league.name;
                this.currentWeek = results.league.current_week;
                this.totalWeeks = results.league.total_weeks;
                this.results = results.matches;

                this.teams = results.league.teams.map(team => ({
                    id: team.team.id,
                    name: team.team.name,
                    ...team
                }));
            },
        }
    }
</script>
