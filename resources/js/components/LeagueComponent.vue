<template>
    <div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">PTS</th>
                            <th class="px-4 py-2">P</th>
                            <th class="px-4 py-2">W</th>
                            <th class="px-4 py-2">D</th>
                            <th class="px-4 py-2">L</th>
                            <th class="px-4 py-2">GD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="team in league" :key="team.id">
                            <td class="border px-4 py-2"> {{ team.name }}</td>
                            <td class="border px-4 py-2"> {{ team.played }}</td>
                            <td class="border px-4 py-2"> {{ team.points }}</td>
                            <td class="border px-4 py-2"> {{ team.won }}</td>
                            <td class="border px-4 py-2"> {{ team.draw }}</td>
                            <td class="border px-4 py-2"> {{ team.loss }}</td>
                            <td class="border px-4 py-2"> {{ team.diff }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex flex-1">
                    <button class="flex-auto bg-transparent hover:bg-blue-500 text-blue-700 hover:text-white rounded">Play all</button>
                    <button class="flex-auto bg-transparent hover:bg-blue-500 text-blue-700 hover:text-white rounded">Next week</button>
                </div>
            </div>
            <div>
                <h3 class="text-center">Results</h3>
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
            }
        },
        computed: {
            apiHeaders: () => {
                return {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            }
        },
        mounted() {
            this.fetchTeams();
        },
        methods: {
            fetchTeams() {
                const url = process.env.MIX_API + '/teams';
                axios.get(url, this.apiHeaders).then((res) => {
                    console.log('result', res.data.data.teams);
                    this.league = res.data.data.teams.map(team => {
                        return {
                            points: 0,
                            won: 0,
                            loss: 0,
                            draw: 0,
                            played: 0,
                            diff: 0,
                            ...team
                        }
                    });
                }).catch((err) => {
                    console.log('ERROR', err);
                });
            },
            generateWeeks(teams) {

            },
            playMatch() {

            }
        }
    }
</script>
