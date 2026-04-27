<!-- LoadingScreen.vue -->
<template>
  <transition name="rolling-door">
    <div v-if="isLoading" class="loading-screen">
      <!-- Rolling Door Panels -->
      <div class="door-panel door-left">
        <div class="door-content">
          <div class="flex flex-col items-center">
            <!-- Animated Icon yang Berubah-ubah -->
            <div class="icon-rotator w-32 h-32 mb-8">
              <transition name="icon-switch" mode="out-in">
                <div :key="currentIcon" class="w-full h-full">
                  <!-- Icon 1: Rolling Door Animation -->
                  <svg v-if="currentIcon === 0" viewBox="0 0 100 100" class="w-full h-full">
                    <rect x="20" y="10" width="60" height="80" fill="none" stroke="currentColor" stroke-width="3" rx="4"/>
                    <rect x="30" y="20" width="40" height="60" fill="currentColor" opacity="0.3"/>
                    <line x1="30" y1="40" x2="70" y2="40" stroke="currentColor" stroke-width="2"/>
                    <line x1="30" y1="55" x2="70" y2="55" stroke="currentColor" stroke-width="2"/>
                    <circle cx="50" cy="75" r="4" fill="currentColor"/>
                    <rect x="45" y="10" width="10" height="6" rx="2" fill="currentColor">
                      <animate attributeName="y" values="10;15;10" dur="1s" repeatCount="indefinite"/>
                    </rect>
                  </svg>

                  <!-- Icon 2: Loading Spinner -->
                  <svg v-else-if="currentIcon === 1" viewBox="0 0 100 100" class="w-full h-full animate-spin">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="60 200" stroke-linecap="round"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="3" opacity="0.3"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor" opacity="0.5">
                      <animate attributeName="r" values="10;12;10" dur="1s" repeatCount="indefinite"/>
                    </circle>
                  </svg>

                  <!-- Icon 3: Pixel Art Loading -->
                  <svg v-else-if="currentIcon === 2" viewBox="0 0 100 100" class="w-full h-full">
                    <rect x="20" y="70" width="10" height="10" fill="currentColor" class="pixel-animate" style="animation-delay: 0s"/>
                    <rect x="35" y="50" width="10" height="10" fill="currentColor" class="pixel-animate" style="animation-delay: 0.1s"/>
                    <rect x="50" y="30" width="10" height="10" fill="currentColor" class="pixel-animate" style="animation-delay: 0.2s"/>
                    <rect x="65" y="50" width="10" height="10" fill="currentColor" class="pixel-animate" style="animation-delay: 0.3s"/>
                    <rect x="80" y="70" width="10" height="10" fill="currentColor" class="pixel-animate" style="animation-delay: 0.4s"/>
                    <rect x="20" y="85" width="10" height="10" fill="currentColor" class="pixel-wave" style="animation-delay: 0s"/>
                    <rect x="35" y="85" width="10" height="10" fill="currentColor" class="pixel-wave" style="animation-delay: 0.1s"/>
                    <rect x="50" y="85" width="10" height="10" fill="currentColor" class="pixel-wave" style="animation-delay: 0.2s"/>
                    <rect x="65" y="85" width="10" height="10" fill="currentColor" class="pixel-wave" style="animation-delay: 0.3s"/>
                    <rect x="80" y="85" width="10" height="10" fill="currentColor" class="pixel-wave" style="animation-delay: 0.4s"/>
                  </svg>

                  <!-- Icon 4: Gear/Process -->
                  <svg v-else-if="currentIcon === 3" viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="15" fill="none" stroke="currentColor" stroke-width="3"/>
                    <g class="gear-animate">
                      <line x1="50" y1="20" x2="50" y2="35" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                      <line x1="50" y1="65" x2="50" y2="80" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                      <line x1="20" y1="50" x2="35" y2="50" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                      <line x1="65" y1="50" x2="80" y2="50" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                      <line x1="29" y1="29" x2="40" y2="40" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                      <line x1="60" y1="60" x2="71" y2="71" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                      <line x1="71" y1="29" x2="60" y2="40" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                      <line x1="40" y1="60" x2="29" y2="71" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </g>
                    <circle cx="50" cy="50" r="6" fill="currentColor" opacity="0.4">
                      <animate attributeName="opacity" values="0.2;0.8;0.2" dur="2s" repeatCount="indefinite"/>
                    </circle>
                  </svg>

                  <!-- Icon 5: Wave Animation -->
                  <svg v-else viewBox="0 0 100 100" class="w-full h-full">
                    <path d="M10 70 Q 20 50 30 70 T 50 70 T 70 70 T 90 70" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                      <animate attributeName="d" 
                        values="M10 70 Q 20 50 30 70 T 50 70 T 70 70 T 90 70;
                                M10 70 Q 20 90 30 70 T 50 70 T 70 70 T 90 70;
                                M10 70 Q 20 50 30 70 T 50 70 T 70 70 T 90 70"
                        dur="2s" repeatCount="indefinite"/>
                    </path>
                    <path d="M10 85 Q 30 65 50 85 T 90 85" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5">
                      <animate attributeName="d" 
                        values="M10 85 Q 30 65 50 85 T 90 85;
                                M10 85 Q 30 105 50 85 T 90 85;
                                M10 85 Q 30 65 50 85 T 90 85"
                        dur="2.5s" repeatCount="indefinite"/>
                    </path>
                    <circle cx="25" cy="68" r="3" fill="currentColor">
                      <animate attributeName="cy" values="68;72;68" dur="2s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="50" cy="68" r="3" fill="currentColor">
                      <animate attributeName="cy" values="68;72;68" dur="2s" repeatCount="indefinite" begin="0.3s"/>
                    </circle>
                    <circle cx="75" cy="68" r="3" fill="currentColor">
                      <animate attributeName="cy" values="68;72;68" dur="2s" repeatCount="indefinite" begin="0.6s"/>
                    </circle>
                  </svg>
                </div>
              </transition>
            </div>

            <!-- Logo -->
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 border-4 border-black overflow-hidden shadow-[0_0_30px_rgba(255,235,0,0.5)]">
              <img src="/public/assets/image/logo-supplaybox.png" class="w-full h-full object-cover animate-pulse-gentle" alt="SupplayBox">
            </div>

            <!-- Text -->
            <div class="font-[1000] text-black text-4xl italic tracking-tighter uppercase mb-4">
              SUPPLAY<span class="opacity-50">BOX</span>
            </div>
            
            <!-- Loading Message yang Berubah -->
            <transition name="message-switch" mode="out-in">
              <div :key="currentMessage" class="text-sm font-bold uppercase tracking-wider text-black/60 mb-4">
                {{ loadingMessages[currentMessage] }}
              </div>
            </transition>

            <!-- Progress Bar -->
            <div class="w-64 h-1.5 bg-black/20 mt-4 overflow-hidden rounded-full">
              <div class="h-full bg-black rounded-full animate-progress-shimmer" 
                   :style="{ width: `${progress}%` }"></div>
            </div>

            <!-- Percentage -->
            <div class="text-xs font-black text-black/40 mt-3 font-mono">
              {{ Math.floor(progress) }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const isLoading = ref(true)
const progress = ref(0)
const currentIcon = ref(0)
const currentMessage = ref(0)

const loadingMessages = [
  "CREATING MAGIC...",
  "LOADING ASSETS...",
  "PREPARING THE SUPPLY...",
  "DESIGNING DREAMS...",
  "ALMOST THERE...",
  "FINAL TOUCHES..."
]

let iconInterval = null
let messageInterval = null
let progressInterval = null

// Simulate loading progress
const startLoading = () => {
  progressInterval = setInterval(() => {
    if (progress.value < 100) {
      // Increment progress with varying speed
      const increment = progress.value < 30 ? 2 : 
                       progress.value < 70 ? 1.5 : 
                       progress.value < 90 ? 1 : 0.5
      progress.value = Math.min(progress.value + increment, 100)
    } else {
      clearInterval(progressInterval)
      // Small delay before hiding loading screen
      setTimeout(() => {
        isLoading.value = false
      }, 500)
    }
  }, 30)
}

// Rotate through icons
const startIconRotation = () => {
  iconInterval = setInterval(() => {
    currentIcon.value = (currentIcon.value + 1) % 5
  }, 800)
}

// Rotate through messages
const startMessageRotation = () => {
  messageInterval = setInterval(() => {
    currentMessage.value = (currentMessage.value + 1) % loadingMessages.length
  }, 1200)
}

onMounted(() => {
  startLoading()
  startIconRotation()
  startMessageRotation()
})

onUnmounted(() => {
  if (iconInterval) clearInterval(iconInterval)
  if (messageInterval) clearInterval(messageInterval)
  if (progressInterval) clearInterval(progressInterval)
})
</script>

<style scoped>
/* Loading Screen */
.loading-screen {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #000;
}

.door-panel {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #fff8ab 0%, #ffe66d 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

.door-content {
  text-align: center;
  transform: translateY(0);
  animation: content-float 3s ease-in-out infinite;
}

@keyframes content-float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* Rolling Door Transition */
.rolling-door-enter-active {
  transition: transform 0s;
}

.rolling-door-leave-active {
  transition: transform 1.2s cubic-bezier(0.85, 0, 0.15, 1);
}

.rolling-door-leave-to {
  transform: translateY(100%);
}

/* Icon Switch Transition */
.icon-switch-enter-active,
.icon-switch-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.icon-switch-enter-from {
  opacity: 0;
  transform: scale(0.8) rotate(-30deg);
}

.icon-switch-leave-to {
  opacity: 0;
  transform: scale(1.2) rotate(30deg);
}

/* Message Switch Transition */
.message-switch-enter-active,
.message-switch-leave-active {
  transition: all 0.3s ease;
}

.message-switch-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.message-switch-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

/* Progress Bar Animation */
.animate-progress-shimmer {
  position: relative;
  overflow: hidden;
  transition: width 0.1s linear;
}

.animate-progress-shimmer::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(255, 235, 0, 0.4), 
    transparent);
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Gentle Pulse Animation */
.animate-pulse-gentle {
  animation: pulse-gentle 2s ease-in-out infinite;
}

@keyframes pulse-gentle {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.9; }
}

/* Pixel Animation */
.pixel-animate {
  animation: pixel-bounce 0.6s ease-in-out infinite alternate;
}

@keyframes pixel-bounce {
  from { transform: translateY(0); }
  to { transform: translateY(-15px); }
}

.pixel-wave {
  animation: pixel-wave 0.8s ease-in-out infinite alternate;
}

@keyframes pixel-wave {
  from { transform: translateY(0) scaleY(1); }
  to { transform: translateY(-8px) scaleY(0.8); }
}

/* Gear Animation */
.gear-animate {
  animation: spin-slow 3s linear infinite;
  transform-origin: 50px 50px;
}

@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Icon Rotator Container */
.icon-rotator {
  filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
}

/* Responsive */
@media (max-width: 768px) {
  .icon-rotator {
    width: 24vw;
    height: 24vw;
  }
  
  .door-panel .font-\[1000\] {
    font-size: 2rem;
  }
  
  .w-24.h-24 {
    width: 80px;
    height: 80px;
  }
}
</style>