<!-- @/Components/Admin/ErrorNotification.vue -->
<template>
    <div v-if="show" 
         class="fixed top-4 right-4 z-50 animate-shake"
         @mouseenter="pauseAutoClose"
         @mouseleave="resumeAutoClose">
        
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-red-500 max-w-md overflow-hidden">
            <div class="flex items-start p-4">
                <!-- Animated Error Icon -->
                <div class="flex-shrink-0">
                    <div class="relative w-10 h-10">
                        <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-75"></div>
                        <div class="relative w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <!-- PERBAIKAN PATH DI SINI -->
                                <path 
                                    fill-rule="evenodd" 
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                    :class="{'animate-spin-once': iconSpinning}" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="ml-3 flex-1 pt-1">
                    <h3 class="text-lg font-semibold text-red-800">
                        {{ title }}
                    </h3>
                    <p class="mt-1 text-sm text-red-600">
                        {{ message }}
                    </p>
                </div>

                <!-- Close Button -->
                <button 
                    @click="close"
                    class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <!-- PERBAIKAN PATH CLOSE BUTTON -->
                        <path 
                            fill-rule="evenodd" 
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                            clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <!-- Progress Bar -->
            <div v-if="duration" class="h-1 bg-red-200 overflow-hidden">
                <div class="h-full bg-red-500" 
                     :class="{'animate-progress-bar': showProgress}"
                     :style="{ animationDuration: `${duration}ms` }"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onUnmounted } from 'vue'

    // Pastikan prop show didefinisikan
    const props = defineProps({
        show: {
            type: Boolean,
            default: true
        },
        title: {
            type: String,
            default: 'Error'
        },
        message: {
            type: String,
            default: ''
        },
        duration: {
            type: Number,
            default: 5000
        }
    })

    const emit = defineEmits(['close'])

    const close = () => {
        emit('close')
    }

    // Logic untuk auto-close
    const iconSpinning = ref(false)
    const showProgress = ref(true)
    let timeoutId = null
    let remainingTime = props.duration
    let startTime = Date.now()

    const pauseAutoClose = () => {
        if (timeoutId) {
            clearTimeout(timeoutId)
            timeoutId = null
            showProgress.value = false
            remainingTime -= Date.now() - startTime
        }
    }

    const resumeAutoClose = () => {
        if (remainingTime > 0) {
            showProgress.value = true
            startTime = Date.now()
            timeoutId = setTimeout(() => {
                close()
            }, remainingTime)
        }
    }

    onMounted(() => {
        if (props.duration > 0) {
            startTime = Date.now()
            timeoutId = setTimeout(() => {
                close()
            }, props.duration)
        }
        
        // Animate icon on mount
        iconSpinning.value = true
        setTimeout(() => {
            iconSpinning.value = false
        }, 500)
    })

    onUnmounted(() => {
        if (timeoutId) {
            clearTimeout(timeoutId)
        }
    })
</script>

<style scoped>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes spinOnce {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes progressBar {
        0% { width: 100%; }
        100% { width: 0%; }
    }

    .animate-spin-once {
        animation: spinOnce 0.5s ease-in-out;
    }

    .animate-progress-bar {
        animation: progressBar linear forwards;
    }
</style>