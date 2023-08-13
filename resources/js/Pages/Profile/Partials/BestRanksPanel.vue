<script setup>

import { ref } from 'vue'
import BeatmapRank from './BeatmapRank.vue'

defineProps({
  user: Object,
})

const ranks = ref([
{
        'title': 'Diamond',
        'artist': 'Rihanna',
        'difficulty': 'Insane',
        'grade': 'SS',
        'score': '1000000',
        'accuracy': 100,
        'mods': 'HDHR',
        'pp': 1000,
        'date': '2021-01-01',
        'mapset_id': 1,
        'score_id': 1,
    },
    {
        'title': 'All the lights in the sky',
        'artist': 'Area 11',
        'difficulty': 'Insane',
        'grade': 'SS',
        'score': '1000000',
        'accuracy': 100,
        'mods': 'HDHR',
        'pp': 1000,
        'date': '2021-01-01',
        'mapset_id': 2,
        'score_id': 2,
    },
])
</script>

<template>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h1>Best Ranks</h1>
                <BeatmapRank v-for="rank in ranks" :key="rank.score_id" :score="rank" class="my-2" />
            </div>
        </div>
    </div>
</template>