<template>
    <div v-if="show" 
         class="fixed top-4 right-4 z-50 animate-shake"
         @mouseenter="pauseAutoClose"
         @mouseleave="resumeAutoClose">
        
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-yellow-500 max-w-md overflow-hidden">
            <div class="flex items-start p-4">
                <div class="flex-shrink-0">
                    <div class="relative w-10 h-10">
                        <div class="absolute inset-0 bg-yellow-100 rounded-full animate-pulse opacity-75"></div>
                        
                        <div class="relative w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" 
                                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                      clip-rule="evenodd"
                                      :class="{'animate-bounce': iconBouncing}" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="ml-3 flex-1 pt-1">
                    <h3 class="text-lg font-semibold text-yellow-800">
                        {{ title }}
                    </h3>
                    <p class="mt-1 text-sm text-yellow-600">
                        {{ message }}
                    </p>
                    
                    <div v-if="actions" class="mt-3 flex space-x-3">
                        <button 
                            v-for="action in actions"
                            :key="action.label"
                            @click="action.handler"
                            :class="[
                                'px-3 py-1.5 text-sm font-medium rounded-md transition-colors',
                                action.primary 
                                    ? 'bg-yellow-500 text-white hover:bg-yellow-600' 
                                    : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                            ]">
                            {{ action.label }}
                        </button>
                    </div>
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

            <div v-if="duration && !actions" class="h-1 bg-yellow-200 overflow-hidden">
                <div class="h-full bg-yellow-500" 
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
        default: 'Warning!'
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 8000
    },
    autoClose: {
        type: Boolean,
        default: true
    },
    actions: {
        type: Array,
        default: () => [] 
    }
})

const emit = defineEmits(['close'])

const show = ref(true)
const iconBouncing = ref(false)
const showProgress = ref(false)
let closeTimeout
let bounceInterval

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
    if (props.autoClose && props.duration > 0 && !props.actions.length) {
        showProgress.value = true
        
        closeTimeout = setTimeout(() => {
            close()
        }, props.duration)
    }
}

const startBouncing = () => {
    bounceInterval = setInterval(() => {
        iconBouncing.value = true
        setTimeout(() => {
            iconBouncing.value = false
        }, 1000)
    }, 3000)
}

onMounted(() => {
    setTimeout(() => {
        iconBouncing.value = true
        setTimeout(() => {
            iconBouncing.value = false
        }, 1000)
        
        startBouncing()
    }, 300)

    if (props.autoClose && !props.actions.length) {
        setTimeout(() => {
            startAutoClose()
        }, 500)
    }
})

onBeforeUnmount(() => {
    clearTimeout(closeTimeout)
    clearInterval(bounceInterval)
})
</script>

<style scoped>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

.animate-bounce {
    animation: bounce 1s ease-in-out;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.75; }
    50% { opacity: 0.25; }
}
</style>