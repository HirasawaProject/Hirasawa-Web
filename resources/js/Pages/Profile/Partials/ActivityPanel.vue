<script setup>

defineProps({
  user: Object,
})
</script>

<template>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h1>Activity</h1>
                <div v-for="activity in [
                    user.username + ' achieved rank #1 in some random map [Insane] (osu!)',
                    user.username + ' achieved rank #2 in some random map [Insane] (osu!)',
                    user.username + ' achieved rank #3 in some random map [Insane] (osu!)',
                    user.username + ' unlocked the \'Complete weeb\' medal!'
                ]" class="flex">
                    <p class="text-gray-500 dark:text-gray-200">
                        {{ activity }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-200 ml-auto">a few hours ago</p>
                </div>
            </div>
        </div>
    </div>
</template>