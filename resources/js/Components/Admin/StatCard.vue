<template>
    <div :class="['p-4 rounded-lg border', containerClass]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">{{ title }}</p>
                <p class="text-2xl font-bold mt-1">{{ value }}</p>
            </div>
            <div class="text-2xl">
                {{ icon }}
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    containerClass: {
        type: String,
        default: 'bg-white'
    }
})
</script>