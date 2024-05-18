<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import RankingStatsPanel from './Partials/RankingStatsPanel.vue';
import ActivityPanel from './Partials/ActivityPanel.vue';
import BestRanksPanel from './Partials/BestRanksPanel.vue';
import HirasawaLayout from '@/Layouts/HirasawaLayout.vue';

defineProps({
    user: {
        type: Object,
    }
});
const selectedMode = ref(0);
const sections = [
    ActivityPanel,
    BestRanksPanel,
];

</script>

<template>
    <Head title="Profile" />

    <HirasawaLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">User Profile</h2>
        </template>

        <RankingStatsPanel :user="user" :mode="selectedMode" @mode-changed="selectedMode = $event"/>
        <component :is="section" v-for="section, index in sections" :user="user" :mode="selectedMode" :key="index"/>

    </HirasawaLayout>
</template>
