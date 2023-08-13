<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import RankingStatsPanel from './Partials/RankingStatsPanel.vue';
import ActivityPanel from './Partials/ActivityPanel.vue';
import BestRanksPanel from './Partials/BestRanksPanel.vue';

defineProps({
    user: {
        type: Object,
    }
});
const selectedMode = ref(0);
const sections = ref([
    RankingStatsPanel,
    ActivityPanel,
    BestRanksPanel,
]);

</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">User Profile</h2>
        </template>

        <component :is="section" v-for="section in sections" :user="user" :mode="selectedMode"/>

    </AuthenticatedLayout>
</template>
