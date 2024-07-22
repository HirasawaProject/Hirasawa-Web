<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import RankingStatsPanel from './Partials/RankingStatsPanel.vue';
import ActivityPanel from './Partials/ActivityPanel.vue';
import BestRanksPanel from './Partials/BestRanksPanel.vue';
import HirasawaLayout from '@/Layouts/HirasawaLayout.vue';

const props = defineProps({
    user: {
        type: Object,
    },
    mode: {
        type: String
    }
});
const selectedMode = computed(() => {
    switch (props.mode) {
        case "osu": return 0;
        case "taiko": return 1;
        case "ctb": return 2;
        case "mania": return 3;
        default: return 0;
    }
});
const sections = [
    RankingStatsPanel,
    ActivityPanel,
    BestRanksPanel,
];

</script>

<template>
    <Head :title="`${user.username}'s Profile`" />

    <HirasawaLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">User Profile</h2>
        </template>

        <component :is="section" v-for="section, index in sections" :user="user" :mode="selectedMode" :key="index"/>

    </HirasawaLayout>
</template>
