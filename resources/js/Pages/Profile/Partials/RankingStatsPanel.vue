<script setup>
import { computed } from 'vue';
import RankingGraph from './RankingGraph.vue';

const props = defineProps({
  user: Object,
  mode: Number,
});

const emits = defineEmits([
    'modeChanged'
])

const generateRandomChart = function() {
    var lastValue = 100;
    var data = [];
    for (var i = 0; i < 90; i++) {
        var value = lastValue + Math.floor(Math.random() * (10 - (-10) + 1) + (-10));
        lastValue = value;
        data.push(value);
    }
    return data;
}
const data = {
  labels: generateRandomChart(),
  datasets: [
    {
      borderColor: '#0000ff',
      data: generateRandomChart(),
      pointStyle: false
    }
  ]
}
const processedRanks = computed(() => {
    let ranks = [];
    props.user.rank_history.forEach(element => {
        if (element.mode == props.mode) {
            ranks.push(element.rank);
        }
    });
    return ranks;
});
function modeChanged(id) {
    emits('modeChanged', id);
}
</script>

<template>
    <div class="pb-2 pt-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex">
                    <img src="https://avatars.githubusercontent.com/u/1824804?v=4" alt="avatar" class="rounded-full w-32 h-32 relative bottom-20 -mb-20" />
                    <div class="ml-8">
                        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                            <span>{{ user.username }}</span>
                            <img :src="`http://purecatamphetamine.github.io/country-flag-icons/3x2/${user.country.toUpperCase()}.svg`" class="rounded-md h-6 ml-2 align-middle"/>
                        </h1>
                    </div>
                    <div class="ml-auto relative bottom-5">
                        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                            <li class="me-2" v-for="text, index in ['osu!', 'taiko', 'ctb', 'mania']" :key="index">
                                <a v-if="index == mode" href="#" class="inline-block px-4 py-3 text-white bg-blue-600 rounded-lg selected">{{ text }}</a>
                                <a v-else href="#" class="inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white" @click.prevent="modeChanged(index)">{{ text }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-2/3">
                        <div class="grid grid-rows-2 grid-flow-col gab-4">
                            <div id='ranking-details'>
                                <div class="flex space-x-4">
                                    <div class="flex-initial">
                                        <p class="text-sm font-semibold dark:text-gray-200">Global Ranking</p>
                                        <p class="text-2xl dark:text-gray-200">#{{ user.stats[mode].rank }}</p>
                                    </div>

                                    <div class="flex-initial">
                                        <p class="text-sm font-semibold dark:text-gray-200">Country Ranking</p>
                                        <p class="text-2xl dark:text-gray-200">#{{ user.stats[mode].country_rank }}</p>
                                    </div>
                                </div>
                            </div>
                            <div id='ranking-graph' class="w-full h-20">
                                <RankingGraph :ranks="processedRanks" />
                            </div>
                        </div>
                    </div>
                    <div class="w-1/3">
                        <div class="grid grid-flow-row grid-cols-2 text-sm dark:text-gray-200">
                            <p>Ranked Score</p>
                            <p>{{ user.stats[mode].ranked_score }}</p>

                            <p>Hit Accuracy</p>
                            <p>{{ user.stats[mode].accuracy }}%</p>

                            <p>Play Count</p>
                            <p>{{ user.stats[mode].play_count }}</p>

                            <p>Total Score</p>
                            <p>{{ user.stats[mode].total_score }}</p>

                            <p>Total Hits</p>
                            <p>{{ user.stats[mode].total_hits }}</p>

                            <p>Maximum Combo</p>
                            <p>{{ user.stats[mode].max_combo }}</p>

                            <p>Replays Watched by Others</p>
                            <p>{{ user.stats[mode].replays_watched }}</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>