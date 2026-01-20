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
                                <path fill-rule="evenodd" 
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
                        <path fill-rule="evenodd" 
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 0 010-1.414z" 
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
// Implementation similar to WarningNotification
// Just change colors and icon
</script>

<style scoped>
@keyframes spinOnce {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin-once {
    animation: spinOnce 0.5s ease-in-out;
}
</style>