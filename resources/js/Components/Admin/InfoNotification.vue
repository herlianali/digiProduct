<template>
    <div v-if="show" 
         class="fixed top-4 right-4 z-50 animate-slide-in"
         @mouseenter="pauseAutoClose"
         @mouseleave="resumeAutoClose">
        
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-blue-500 max-w-md overflow-hidden">
            <div class="flex items-start p-4">
                <div class="flex-shrink-0">
                    <div class="relative w-10 h-10">
                        <div class="absolute inset-0 bg-blue-100 rounded-full animate-pulse-slow opacity-75"></div>
                        
                        <div class="relative w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" 
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                      clip-rule="evenodd"
                                      :class="{'animate-float': iconFloating}" />
                            </svg>
                            
                            <div class="absolute -top-1 -right-1 w-3 h-3">
                                <div class="w-full h-full bg-blue-300 rounded-full animate-ping-slow"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ml-3 flex-1 pt-1">
                    <h3 class="text-lg font-semibold text-blue-800">
                        {{ title }}
                    </h3>
                    <p class="mt-1 text-sm text-blue-600">
                        {{ message }}
                    </p>
                    
                    <a v-if="link"
                       :href="link.url"
                       class="mt-2 inline-flex items-center text-sm font-medium text-blue-700 hover:text-blue-800 transition-colors">
                        <span>{{ link.text }}</span>
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" 
                                  d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" 
                                  clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>

                <button 
                    @click="close"
                    class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500 focus:outline-none transition-colors">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" 
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 0 010-1.414z" 
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div v-if="duration" class="h-1 bg-blue-200 overflow-hidden">
                <div class="h-full bg-blue-500" 
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
        default: 'Info'
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 6000
    },
    autoClose: {
        type: Boolean,
        default: true
    },
    link: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['close'])

const show = ref(true)
const iconFloating = ref(false)
const showProgress = ref(false)
let closeTimeout
let floatInterval

const close = () => {
    show.value = false
    emit('close')
}

const pauseAutoClose = () => {
    if (props.autoClose) {
        clearTimeout(closeTimeout)
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

const startFloating = () => {
    floatInterval = setInterval(() => {
        iconFloating.value = true
        setTimeout(() => {
            iconFloating.value = false
        }, 2000)
    }, 4000)
}

onMounted(() => {
    setTimeout(() => {
        iconFloating.value = true
        setTimeout(() => {
            iconFloating.value = false
        }, 2000)
        
        startFloating()
    }, 500)

    if (props.autoClose) {
        setTimeout(() => {
            startAutoClose()
        }, 1000)
    }
})

onBeforeUnmount(() => {
    clearTimeout(closeTimeout)
    clearInterval(floatInterval)
})
</script>

<style scoped>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}

@keyframes pulseSlow {
    0%, 100% { opacity: 0.75; }
    50% { opacity: 0.3; }
}

@keyframes pingSlow {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(2);
        opacity: 0;
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

.animate-slide-in {
    animation: slideIn 0.3s ease-out;
}

.animate-float {
    animation: float 2s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulseSlow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-ping-slow {
    animation: pingSlow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-progress-bar {
    animation: progressBar linear forwards;
    animation-play-state: running;
}
</style>