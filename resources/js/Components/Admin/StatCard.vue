<template>
    <div
        class="
            relative
            flex items-center gap-4 p-4 rounded-xl
            transition-all duration-300 ease-out
            transform-gpu
            hover:-translate-y-1 hover:scale-[1.01]
            shadow-md hover:shadow-xl
            border border-white/40
        "
        :class="containerClass"
    >
        <!-- highlight layer (fake light) -->
        <div
            class="absolute inset-0 rounded-xl pointer-events-none
                   bg-gradient-to-br from-white/40 via-transparent to-black/5">
        </div>

        <!-- Icon -->
        <div
            class="
                relative p-3 rounded-lg
                bg-white/80
                shadow-inner
                backdrop-blur-sm
            "
        >
            <component
                :is="icon"
                class="w-6 h-6 text-brand-700"
            />
        </div>

        <!-- Content -->
        <div class="relative">
            <p class="text-sm text-gray-500">
                {{ title }}
            </p>
            <p class="text-2xl font-semibold text-gray-900">
                {{ value }}
            </p>
        </div>
    </div>
</template>


<script setup>
    defineProps({
        title: String,
        value: [String, Number],
        icon: Object,
        containerClass: {
            type: String,
            default: 'bg-gray-50'
        }
    })
</script>
