<template>
    <div v-if="show" 
         class="fixed top-4 right-4 z-50 animate-fade-in-down"
         @mouseenter="pauseAutoClose"
         @mouseleave="resumeAutoClose">
        
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-green-500 max-w-md overflow-hidden">
            <div class="flex items-start p-4">
                <div class="flex-shrink-0">
                    <div class="relative w-10 h-10">
                        <div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-75"></div>
                        
                        <div class="relative w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" 
                                      d="M5 13l4 4L19 7" 
                                      :class="{'animate-draw-check': iconAnimated}" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="ml-3 flex-1 pt-1">
                    <h3 class="text-lg font-semibold text-green-800">
                        {{ title }}
                    </h3>
                    <p class="mt-1 text-sm text-green-600">
                        {{ message }}
                    </p>
                </div>

                <button 
                    @click="close"
                    class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" 
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 0 010-1.414z" 
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div v-if="duration" class="h-1 bg-green-200 overflow-hidden">
                <div class="h-full bg-green-500" 
                     :class="{'animate-progress-bar': showProgress}"
                     :style="{ animationDuration: `${duration}ms` }"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    title: {
        type: String,
        default: 'Success!'
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 5000
    },
    autoClose: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['close'])

const show = ref(true)
const iconAnimated = ref(false)
const showProgress = ref(false)
let closeTimeout
let progressTimeout

const close = () => {
    show.value = false
    emit('close')
}

const pauseAutoClose = () => {
    if (props.autoClose) {
        clearTimeout(closeTimeout)
        clearTimeout(progressTimeout)
        showProgress.value = false
    }
}

const resumeAutoClose = () => {
    if (props.autoClose) {
        startAutoClose()
    }
}

const startAutoClose = () => {
    if (props.autoClose && props.duration > 0) {
        showProgress.value = true
        
        closeTimeout = setTimeout(() => {
            close()
        }, props.duration)
    }
}

onMounted(() => {
    setTimeout(() => {
        iconAnimated.value = true
    }, 300)

    if (props.autoClose) {
        setTimeout(() => {
            startAutoClose()
        }, 500)
    }
})

onBeforeUnmount(() => {
    clearTimeout(closeTimeout)
    clearTimeout(progressTimeout)
})
</script>

<style scoped>
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes drawCheck {
    0% {
        stroke-dasharray: 24;
        stroke-dashoffset: 24;
    }
    100% {
        stroke-dasharray: 24;
        stroke-dashoffset: 0;
    }
}

@keyframes progressBar {
    0% {
        width: 100%;
    }
    100% {
        width: 0%;
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.3s ease-out;
}

.animate-draw-check {
    animation: drawCheck 0.5s ease-in-out forwards;
}

.animate-progress-bar {
    animation: progressBar linear forwards;
    animation-play-state: running;
}
</style>