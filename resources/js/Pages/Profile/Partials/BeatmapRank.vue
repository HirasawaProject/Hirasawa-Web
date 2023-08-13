<script setup>

defineProps({
  score: Object,
})

</script>

<template>
    <div class="flex bg-zinc-200 sm:rounded-lg gap-1 p-2">
        <img src="https://s.ppy.sh/images/S.png" class="w-8 h-auto" />
        <div class="flex flex-col text-sm">
            <p>{{ score.title }} by {{ score.artist }}</p>
            <div class="flex space-x-2">
                <span class="text-blue-500"> {{ score.difficulty }} </span>
                <span>5 years ago</span>
            </div>
        </div>
        <div class="ml-auto flex text-2xl items-center">
            <span>{{ score.pp }}pp</span>
        </div>
    </div>
</template>