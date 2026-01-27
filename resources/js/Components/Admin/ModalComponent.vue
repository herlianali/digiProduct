<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="close"></div>

        <!-- Modal panel -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div 
                :class="[
                    'relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full',
                    maxWidthClass
                ]"
                @click.stop
            >
                <!-- Close button -->
                <button 
                    v-if="showClose" 
                    @click="close"
                    class="absolute right-4 top-4 text-gray-400 hover:text-gray-500 focus:outline-none"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Slot content -->
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    maxWidth: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(value)
    },
    showClose: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['close'])

const close = () => {
    emit('close')
}

const maxWidthClass = computed(() => {
    const classes = {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        'full': 'sm:max-w-full'
    }
    return classes[props.maxWidth] || classes['md']
})

// Handle ESC key
const handleEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close()
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleEscape)
})
</script>