<template>
    <div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <h3 class="text-center">League Table</h3>
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
                        <tr v-for="team in league" :key="team.id">
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
                    {{currentWeek}}<sup>th</sup> of Championship
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
                league: [],
                results: [],
                currentWeek: 0,
                matchesInDay: 2,
                matchesInCurrentDay: 0,
                matchPlayed: true,
                matchPlaying: 0,
                isPlayAll: false,
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
                return this.league.sort( (a, b) => b.points - a.points);
            }
        },
        mounted() {
            this.fetchTeams();
            this.$on('matchPlayed', this.handleMatchPlayed);
        },
        beforeDestroy() {
            this.$off('matchPlayed');
        },
        methods: {
            fetchTeams() {
                const url = process.env.MIX_API + '/teams';
                axios.get(url, this.apiHeaders).then((res) => {
                    const teams = res.data.data.teams;
                    this.league = teams.map(team => {
                        return {
                            points: 0,
                            won: 0,
                            loss: 0,
                            draw: 0,
                            played: 0,
                            diff: 0,
                            goals: 0,
                            out: 0,
                            ...team
                        }
                    });
                    this.generateWeeks(teams);
                }).catch((err) => {});
            },
            generateWeeks(teams) {
                let weeks = [];
                for (let i = 0; i < teams.length; i++) {
                    const idA = teams[i].id;
                    for (let j = 0; j < teams.length; j++) {
                        const idB = teams[j].id;
                        let matchFound = false;
                        let isPushed = false;
                        for (let k = 0; k < weeks.length; k++) {
                            const day = weeks[k];
                            let teamFound = false;
                            for (let m = 0; m < day.length; m++) {
                                const match = day[m];
                                if (match.includes(idA) && match.includes(idB)) {
                                    matchFound = true;
                                }
                                if (match.includes(idA) || match.includes(idB)) {
                                    teamFound = true;
                                }
                            }
                            if (day.length === 1 && !teamFound && idA !== idB) {
                                weeks[k].push([idA, idB]);
                                isPushed = true;
                            }
                        }
                        if (idA !== idB && !matchFound && !isPushed) {
                            weeks.push([[idA, idB]]);
                        }
                    }
                }
                this.weeks = weeks;
            },
            handleNextClick() {
                const day = this.weeks[this.currentWeek];
                this.playDay(day);
            },
            playDay(day) {
                this.matchesInCurrentDay = day.length;
                this.matchPlaying = true;
                this.matchPlayed = 0;
                if (this.currentWeek >= this.weeks.length && !this.checkAddWeek()) {
                    return;
                }
                this.results = [];
                this.currentWeek++;

                for (let i = 0; i < day.length; i++) {
                    this.playMatch(day[i]);
                }
            },
            playMatch(teams) {
                const url = process.env.MIX_API + '/play';
                const data = {
                    teams
                };
                axios.post(url, data, this.apiHeaders).then((res) => {
                    this.updateLeague(res.data.data);
                    this.matchPlayed++;
                    if (this.matchPlayed === this.matchesInCurrentDay) {
                        this.matchPlaying = false;
                        this.$emit('matchPlayed');
                    }
                }).catch((err) => {});
            },
            checkAddWeek() {
                let addNewMatch = false;
                for (let i = 0; i < this.league.length; i++) {
                    const idA = this.league[i].id;
                    for (let j = 0; j < this.league.length; j++) {
                        const idB = this.league[j].id;
                        if (idA !== idB && this.league[i].points === this.league[j].points) {
                            this.addToWeek([idA, idB]);
                            addNewMatch = true;
                        }
                    }
                }
                return addNewMatch;
            },
            addToWeek(match) {
                const weekLength = this.weeks.length;
                const lastWeek = weekLength === 0 ? [] : this.weeks[weekLength - 1];
                if (lastWeek.length > 1) {
                    this.weeks[weekLength].push(match);
                } else {
                    this.weeks.push([match]);
                }
            },
            handlePlayAllClick() {
                this.isPlayAll = true;
                const day = this.weeks[this.currentWeek];
                this.playDay(day);
            },
            updateLeague(results) {
                const teamsRes = results.teams;
                const winner = results.winner;
                let teamA;
                let teamB;
                for (let i = 0; i < teamsRes.length; i++) {
                    const res = teamsRes[i];
                    for (let t = 0; t < this.league.length; t++) {
                        if (this.league[t].id === res.id) {
                            this.league[t].points += res.points;
                            this.league[t].played++;
                            this.league[t].goals += res.goals;
                            this.league[t].out += res.out;
                            this.league[t].diff += this.league[t].goals - this.league[t].out;
                            if (winner === this.league[t].id) {
                                this.league[t].won++;
                            } else if (winner) {
                                this.league[t].loss++;
                            } else {
                                this.league[t].draw++;
                            }
                            if (!teamA) {
                                teamA = this.league[t];
                            } else {
                                teamB = this.league[t];
                            }
                        }
                    }
                }
                this.results.push({
                    teamA: teamA.name,
                    goalsA: teamA.goals,
                    teamB: teamB.name,
                    goalsB: teamB.goals,
                });
            },
            handleMatchPlayed() {
                if (!this.isPlayAll) {
                    return;
                }
                if (this.currentWeek >= this.weeks.length && !this.checkAddWeek()) {
                    this.isPlayAll = false;
                    return;
                }
                const day = this.weeks[this.currentWeek];
                this.playDay(day);
            }
        }
    }
</script>
