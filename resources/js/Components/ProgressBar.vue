<template>
  <transition name="splash-fade">
    <div v-if="visible" class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center">
      <!-- Your animated content (keep as is) -->
      <div class="relative mb-8">
        <div class="absolute inset-0 animate-ping">
          <div class="w-32 h-32 rounded-full bg-indigo-100 opacity-20"></div>
        </div>
        
        <div class="relative w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-xl transform transition-transform duration-500 hover:scale-105">
          <span class="text-white text-2xl font-bold tracking-tight">DP</span>
          
          <div class="absolute -top-2 -right-2 w-4 h-4 rounded-full bg-pink-500 animate-float" style="animation-delay: 0s"></div>
          <div class="absolute -bottom-2 -left-2 w-3 h-3 rounded-full bg-blue-500 animate-float" style="animation-delay: 0.2s"></div>
          <div class="absolute -top-2 -left-2 w-3 h-3 rounded-full bg-green-500 animate-float" style="animation-delay: 0.4s"></div>
          <div class="absolute -bottom-2 -right-2 w-4 h-4 rounded-full bg-yellow-500 animate-float" style="animation-delay: 0.6s"></div>
        </div>
        
        <div class="absolute -inset-4">
          <div class="w-32 h-32 rounded-full border-4 border-transparent border-t-indigo-400 border-r-purple-400 animate-spin-slow"></div>
        </div>
      </div>
      
      <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
          <span class="typewriter">{{ currentText }}</span>
          <span class="animate-pulse">|</span>
        </h2>
        <p class="text-gray-600 mb-6">Please wait while we prepare your dashboard</p>
        
        <div class="flex justify-center space-x-2">
          <div class="w-2 h-2 rounded-full bg-indigo-600 animate-bounce" style="animation-delay: 0ms"></div>
          <div class="w-2 h-2 rounded-full bg-purple-600 animate-bounce" style="animation-delay: 150ms"></div>
          <div class="w-2 h-2 rounded-full bg-pink-600 animate-bounce" style="animation-delay: 300ms"></div>
          <div class="w-2 h-2 rounded-full bg-blue-600 animate-bounce" style="animation-delay: 450ms"></div>
        </div>
        
        <div v-if="showPercentage" class="mt-6">
          <div class="w-48 bg-gray-200 rounded-full h-1.5 overflow-hidden mx-auto">
            <div 
              class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-300"
              :style="{ width: `${progress}%` }"
            ></div>
          </div>
          <p class="text-xs text-gray-500 mt-2">{{ Math.round(progress) }}%</p>
        </div>
      </div>
      
      <div class="absolute bottom-8 left-0 right-0 px-4">
        <div class="max-w-md mx-auto text-center">
          <p class="text-sm text-gray-500 italic">
            {{ tips[currentTip] }}
          </p>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const visible = ref(true) // Start as visible on page load
const progress = ref(0)
const currentText = ref('')
const showPercentage = ref(false)
const currentTip = ref(0)

const texts = ['Loading', 'Initializing', 'Preparing', 'Almost ready']
let textIndex = 0
let charIndex = 0
let typingInterval = null
let progressInterval = null
let tipInterval = null
let finishTimeout = null

// Fun loading tips
const tips = [
  'Pro tip: Use dark mode for better battery life',
  'Did you know? Keyboard shortcuts can save you time',
  'Your data is being secured with 256-bit encryption',
  'Optimizing your experience...',
  'Fetching the latest updates...',
  'Almost there! Preparing your workspace...'
]

const startTyping = () => {
  typingInterval = setInterval(() => {
    if (charIndex < texts[textIndex].length) {
      currentText.value += texts[textIndex].charAt(charIndex)
      charIndex++
    } else {
      clearInterval(typingInterval)
      setTimeout(() => {
        textIndex = (textIndex + 1) % texts.length
        charIndex = 0
        currentText.value = ''
        startTyping()
      }, 1000)
    }
  }, 100)
}

const startProgress = () => {
  progressInterval = setInterval(() => {
    if (progress.value < 85) {
      progress.value += Math.random() * 3
    }
  }, 200)
}

const rotateTips = () => {
  tipInterval = setInterval(() => {
    currentTip.value = (currentTip.value + 1) % tips.length
  }, 3000)
}

const start = () => {
  // Clear any existing intervals
  if (progressInterval) clearInterval(progressInterval)
  if (typingInterval) clearInterval(typingInterval)
  if (tipInterval) clearInterval(tipInterval)
  if (finishTimeout) clearTimeout(finishTimeout)
  
  // Reset values
  visible.value = true
  progress.value = 0
  currentText.value = ''
  textIndex = 0
  charIndex = 0
  currentTip.value = Math.floor(Math.random() * tips.length)
  showPercentage.value = false
  
  // Show percentage after 1.5 seconds if still loading
  setTimeout(() => {
    if (visible.value) {
      showPercentage.value = true
    }
  }, 1500)
  
  // Start animations
  startTyping()
  startProgress()
  rotateTips()
}

const finish = () => {
  // Complete progress
  progress.value = 100
  
  // Clear intervals
  if (progressInterval) clearInterval(progressInterval)
  if (typingInterval) clearInterval(typingInterval)
  if (tipInterval) clearInterval(tipInterval)
  
  // Hide after short delay
  finishTimeout = setTimeout(() => {
    visible.value = false
    setTimeout(() => {
      progress.value = 0
    }, 300)
  }, 500)
}

// Show splash immediately on page load
const showOnPageLoad = () => {
  start()
  
  // Auto-hide after 2 seconds as fallback
  setTimeout(() => {
    if (visible.value) {
      finish()
    }
  }, 2000)
}

// Monitor fetch requests for Inertia
const setupInertiaMonitoring = () => {
  const originalFetch = window.fetch
  
  window.fetch = function(...args) {
    const options = args[1] || {}
    
    // Check if it's an Inertia request
    const isInertiaRequest = 
      options.headers && 
      (options.headers['X-Inertia'] === true || 
       options.headers['X-Inertia'] === 'true' ||
       (typeof options.headers.get === 'function' && options.headers.get('X-Inertia') === 'true'))
    
    if (isInertiaRequest && !visible.value) {
      start()
      
      return originalFetch.apply(this, args)
        .then(response => {
          finish()
          return response
        })
        .catch(error => {
          finish()
          throw error
        })
    }
    
    return originalFetch.apply(this, args)
  }
}

// Monitor link clicks
const setupLinkMonitoring = () => {
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a')
    
    // Check if it's an Inertia link
    if (link && link.href) {
      const url = new URL(link.href)
      const currentUrl = new URL(window.location.href)
      
      // Only intercept same-origin links
      if (url.origin === currentUrl.origin && !link.target && !link.download) {
        const isSignificantNavigation = 
          !link.href.includes('/#') && 
          !link.closest('[data-no-splash]')
        
        if (isSignificantNavigation && !visible.value) {
          start()
        }
      }
    }
  })
}

// Monitor form submissions
const setupFormMonitoring = () => {
  document.addEventListener('submit', (e) => {
    const form = e.target
    if ((form.method === 'post' || form.method === 'POST') && !visible.value) {
      start()
    }
  })
}

onMounted(() => {
  // Show splash on initial load
  showOnPageLoad()
  
  // Setup monitoring for future navigations
  setupInertiaMonitoring()
  setupLinkMonitoring()
  setupFormMonitoring()
  
  // Show splash when browser back/forward buttons are used
  window.addEventListener('popstate', () => {
    if (!visible.value) start()
  })
  
  // Hide when page is fully loaded
  window.addEventListener('load', finish)
  
  // Also hide when Inertia app is ready
  if (window.Inertia) {
    window.Inertia.on('finish', finish)
  }
})

onUnmounted(() => {
  if (progressInterval) clearInterval(progressInterval)
  if (typingInterval) clearInterval(typingInterval)
  if (tipInterval) clearInterval(tipInterval)
  if (finishTimeout) clearTimeout(finishTimeout)
  
  // Restore original fetch
  window.fetch = window.fetch?.__originalFetch || window.fetch
  
  window.removeEventListener('popstate', start)
  window.removeEventListener('load', finish)
  
  if (window.Inertia) {
    window.Inertia.off('finish', finish)
  }
})
</script>

<style scoped>
/* Keep all your existing styles */
.splash-fade-enter-active,
.splash-fade-leave-active {
  transition: opacity 0.5s ease;
}

.splash-fade-enter-from,
.splash-fade-leave-to {
  opacity: 0;
}

.animate-ping {
  animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-spin-slow {
  animation: spin 3s linear infinite;
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-bounce {
  animation: bounce 1.4s infinite;
}

.animate-pulse {
  animation: pulse 1s infinite;
}

.typewriter {
  overflow: hidden;
  border-right: 2px solid transparent;
  white-space: nowrap;
  display: inline-block;
}

@keyframes ping {
  75%, 100% {
    transform: scale(2);
    opacity: 0;
  }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.typewriter {
  border-right: 2px solid #4f46e5;
  animation: blink 1s step-end infinite;
}

@keyframes blink {
  0%, 100% { border-color: #4f46e5; }
  50% { border-color: transparent; }
}
</style>