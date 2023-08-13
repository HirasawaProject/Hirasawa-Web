<script setup>
import RankingGraph from './RankingGraph.vue';

defineProps({
  user: Object,
  mode: Number,
})

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
</script>

<template>
    <div class="pb-2 pt-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex">
                    <img src="https://avatars.githubusercontent.com/u/1824804?v=4" alt="avatar" class="rounded-full w-32 h-32 relative bottom-20 -mb-20" />
                    <div class="ml-8">
                        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200">{{ user.username }}</h1>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-2/3">
                        <div class="grid grid-rows-2 grid-flow-col gab-4">
                            <div id='ranking-details'>
                                <div class="flex space-x-4">
                                    <div class="flex-initial">
                                        <p class="text-sm font-semibold">Global Ranking</p>
                                        <p class="text-2xl">#{{ user.stats[mode].rank }}</p>
                                    </div>

                                    <div class="flex-initial">
                                        <p class="text-sm font-semibold">Country Ranking</p>
                                        <p class="text-2xl">#???</p>
                                    </div>

                                </div>
                            </div>
                            <div id='ranking-graph' class="w-full h-20">
                                <RankingGraph :ranks="generateRandomChart()" />
                            </div>
                        </div>
                    </div>
                    <div class="w-1/3">
                        <div class="grid grid-flow-row grid-cols-2 text-sm">
                            <p>Ranked Score</p>
                            <p>{{ user.stats[mode].ranked_score }}</p>

                            <p>Hit Accuracy</p>
                            <p>{{ user.stats[mode].accuracy }}%</p>

                            <p>Play Count</p>
                            <p>0</p>

                            <p>Total Score</p>
                            <p>{{ user.stats[mode].total_score }}</p>

                            <p>Total Hits</p>
                            <p>0</p>

                            <p>Maximum Combo</p>
                            <p>0</p>

                            <p>Replays Watched by Others</p>
                            <p>0</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>