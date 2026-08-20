<!-- OurWorkSection -->
<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const works = ref([
  {
    id: 1,
    title: 'ATLENS',
    subtitle: 'CANNED ENERGY',
    tagline: 'BLOOD OF THE DRAGON',
    category: 'Poster',
    size: 'w-3 h-3',
    categoryIcon: '/assets/icons/poster-icon.svg',
    location: 'OKEECHOBEE',
    background: 'bg-[#b41f19]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-1.png'
  },
  {
    id: 2,
    title: 'X-HAWN',
    subtitle: 'SKI SAINT BRUNO',
    category: 'Poster',
    size: 'w-3 h-3',
    categoryIcon: '/assets/icons/poster-icon.svg',
    background: 'bg-[#e5f001]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-2.jpg'
  },
  {
    id: 3,
    title: 'MONSTER',
    subtitle: 'International Films Festival',
    location: 'Pigdon Street',
    category: 'Poster',
    size: 'w-3 h-3',
    categoryIcon: '/assets/icons/poster-icon.svg',
    background: 'bg-[#8b27b7]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-3.jpg'
  },
  {
    id: 4,
    title: 'Cherry-King',
    subtitle: 'Brand Identity',
    category: 'Packaging',
    size: 'w-4 h-4',
    categoryIcon: '/assets/icons/packaging-icon.svg',
    background: 'bg-[#3d54a4]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-4.jpg'
  },
  {
    id: 5,
    title: 'OKEECHOBEE',
    subtitle: 'Canned Energy',
    category: 'Poster',
    size: 'w-3 h-3',
    categoryIcon: '/assets/icons/poster-icon.svg',
    background: 'bg-[#b4f000]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-5.jpg'
  },
  {
    id: 6,
    title: 'X-HAWN',
    subtitle: 'SKI SAINT BRUNO',
    category: 'Poster',
    size: 'w-3 h-3',
    categoryIcon: '/assets/icons/poster-icon.svg',
    background: 'bg-[#e5f001]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-2.jpg'
  },
  {
    id: 7,
    title: 'Cherry-King',
    subtitle: 'Brand Identity',
    category: 'Packaging',
    size: 'w-4 h-4',
    categoryIcon: '/assets/icons/packaging-icon.svg',
    background: 'bg-[#3d54a4]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-4.jpg'
  },
  {
    id: 8,
    title: 'Cherry-King',
    subtitle: 'Brand Identity',
    category: 'Packaging',
    size: 'w-4 h-4',
    categoryIcon: '/assets/icons/packaging-icon.svg',
    background: 'bg-[#3d54a4]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-4.jpg'
  },
])

const sectionRef = ref(null)
const sliderContainerRef = ref(null)
const trackRef = ref(null)
const cardsRef = ref([])

const CARD_W = 290
const CARD_GAP = 20
const SLOT = CARD_W + CARD_GAP
const AUTOPLAY_DELAY = 4000

// Sederhana: ease default untuk transisi
const TRANSITION_DURATION = 0.6

const currentIndex = ref(0)
const visibleCards = ref(5)
const isAnimating = ref(false)
let entranceAnimation = null
let dragFrame = null
let pendingDragX = 0
let dragSetters = []

// ─── Hitung jumlah card yang terlihat ─────────────────────────────
const updateVisibleCards = () => {
  const width = window.innerWidth
  if (width < 640) {
    visibleCards.value = 1
  } else if (width < 768) {
    visibleCards.value = 2
  } else if (width < 1024) {
    visibleCards.value = 3
  } else if (width < 1280) {
    visibleCards.value = 4
  } else {
    visibleCards.value = 5
  }
}

// ─── Update posisi card dengan GSAP (CIRCULAR) ────────────────────
const updateCardsPosition = (animate = true) => {
  const total = works.value.length

  cardsRef.value.forEach((card, index) => {
    if (!card) return

    // OFFSET CIRCULAR - mencari jarak terpendek
    let offset = index - currentIndex.value
    if (offset > total / 2) offset -= total
    if (offset < -total / 2) offset += total

    const distance = Math.abs(offset)
    const isCenter = offset === 0
    const isVisible = distance <= visibleCards.value

    // Scale - HANYA card tengah yang lebih besar
    let scale = isCenter ? 1.15 : 1

    // Opacity - card di luar visible range disembunyikan
    let opacity = isVisible ? 1 : 0

    // Z-index
    const zIndex = isCenter ? 50 : 30 - distance

    // POSISI X - dengan spacing yang konsisten
    const xPosition = offset * SLOT

    if (animate) {
      gsap.to(card, {
        x: xPosition,
        scale: scale,
        opacity: opacity,
        zIndex: zIndex,
        duration: TRANSITION_DURATION,
        ease: 'power2.out',
        overwrite: true,
        onComplete: () => {
          gsap.set(card, { pointerEvents: isVisible ? 'auto' : 'none' })
        }
      })
    } else {
      gsap.set(card, {
        x: xPosition,
        scale: scale,
        opacity: opacity,
        zIndex: zIndex,
        pointerEvents: isVisible ? 'auto' : 'none'
      })
    }
  })
}

// ─── Navigasi ──────────────────────────────────────────────────────
const goToIndex = (newIndex, fromAutoplay = false) => {
  if (isAnimating.value) return

  const total = works.value.length
  let targetIndex = newIndex
  if (targetIndex < 0) targetIndex = total - 1
  if (targetIndex >= total) targetIndex = 0

  isAnimating.value = true
  currentIndex.value = targetIndex
  updateCardsPosition(true)

  setTimeout(() => {
    isAnimating.value = false
  }, TRANSITION_DURATION * 1000 + 50)

  if (!fromAutoplay) {
    stopAutoPlay()
    startAutoPlay()
  }
}

const slideLeft = () => {
  goToIndex(currentIndex.value - 1)
}

const slideRight = () => {
  goToIndex(currentIndex.value + 1)
}

// ─── Auto Play ──────────────────────────────────────────────────────
let autoPlayInterval = null

const stopAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
    autoPlayInterval = null
  }
}

const startAutoPlay = () => {
  stopAutoPlay()
  autoPlayInterval = setInterval(() => {
    slideRight()
  }, AUTOPLAY_DELAY)
}

// ─── Pause autoplay saat section di luar viewport ──────────────────
let visibilityObserver = null

const initVisibilityObserver = () => {
  if (!sectionRef.value) return
  visibilityObserver = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        startAutoPlay()
      } else {
        stopAutoPlay()
      }
    },
    { threshold: 0.1 }
  )
  visibilityObserver.observe(sectionRef.value)
}

// ─── Touch & Drag ──────────────────────────────────────────────────
let touchStartX = 0
let touchStartY = 0
let touchDeltaX = 0
let isSwiping = false
let isHorizontalSwipe = null

const onTouchStart = (e) => {
  touchStartX = e.touches[0].clientX
  touchStartY = e.touches[0].clientY
  touchDeltaX = 0
  isSwiping = true
  isHorizontalSwipe = null
  stopAutoPlay()
}

const onTouchMove = (e) => {
  if (!isSwiping) return
  const deltaX = e.touches[0].clientX - touchStartX
  const deltaY = e.touches[0].clientY - touchStartY

  if (isHorizontalSwipe === null && (Math.abs(deltaX) > 10 || Math.abs(deltaY) > 10)) {
    isHorizontalSwipe = Math.abs(deltaX) > Math.abs(deltaY)
  }

  if (isHorizontalSwipe) {
    e.preventDefault()
    touchDeltaX = deltaX
    
    scheduleDragPosition(touchDeltaX)
  }
}

const onTouchEnd = () => {
  if (!isSwiping) return
  isSwiping = false

  if (isHorizontalSwipe) {
    const steps = Math.round(-touchDeltaX / SLOT)
    if (steps !== 0) {
      goToIndex(currentIndex.value + steps)
    } else {
      updateCardsPosition(true)
      startAutoPlay()
    }
  } else {
    startAutoPlay()
  }
  isHorizontalSwipe = null
  touchDeltaX = 0
}

const scheduleDragPosition = (deltaX) => {
  pendingDragX = deltaX
  if (dragFrame) return

  dragFrame = requestAnimationFrame(() => {
    dragFrame = null
    const total = works.value.length
    cardsRef.value.forEach((card, index) => {
      if (!card) return
      let offset = index - currentIndex.value
      if (offset > total / 2) offset -= total
      if (offset < -total / 2) offset += total

      if (!dragSetters[index]) {
        dragSetters[index] = {
          x: gsap.quickSetter(card, 'x', 'px'),
          opacity: gsap.quickSetter(card, 'opacity'),
          scale: gsap.quickSetter(card, 'scale'),
        }
      }
      dragSetters[index].x(offset * SLOT + pendingDragX)
      dragSetters[index].opacity(Math.abs(offset) <= visibleCards.value ? 1 : 0)
      dragSetters[index].scale(offset === 0 ? 1.15 : 1)
    })
  })
}

// Mouse drag
let mouseStartX = 0
let mouseDeltaX = 0
let isDragging = false
let hasDraggedEnough = false

const onMouseDown = (e) => {
  isDragging = true
  hasDraggedEnough = false
  mouseStartX = e.clientX
  mouseDeltaX = 0
  stopAutoPlay()
  gsap.set(sliderContainerRef.value, { cursor: 'grabbing' })
}

const onMouseMove = (e) => {
  if (!isDragging) return
  mouseDeltaX = e.clientX - mouseStartX
  if (Math.abs(mouseDeltaX) > 3) hasDraggedEnough = true
  
  scheduleDragPosition(mouseDeltaX)
}

const onMouseUp = () => {
  if (!isDragging) return
  isDragging = false
  gsap.set(sliderContainerRef.value, { cursor: 'grab' })

  if (hasDraggedEnough) {
    const steps = Math.round(-mouseDeltaX / SLOT)
    if (steps !== 0) {
      goToIndex(currentIndex.value + steps)
    } else {
      updateCardsPosition(true)
      startAutoPlay()
    }
  } else {
    startAutoPlay()
  }
  mouseDeltaX = 0
  hasDraggedEnough = false
}

const addDragEvents = () => {
  const el = sliderContainerRef.value
  if (!el) return
  el.addEventListener('mousedown', onMouseDown)
  el.addEventListener('mousemove', onMouseMove)
  el.addEventListener('mouseup', onMouseUp)
  el.addEventListener('mouseleave', onMouseUp)
  el.addEventListener('touchstart', onTouchStart, { passive: true })
  el.addEventListener('touchmove', onTouchMove, { passive: false })
  el.addEventListener('touchend', onTouchEnd)
}

const removeDragEvents = () => {
  const el = sliderContainerRef.value
  if (!el) return
  el.removeEventListener('mousedown', onMouseDown)
  el.removeEventListener('mousemove', onMouseMove)
  el.removeEventListener('mouseup', onMouseUp)
  el.removeEventListener('mouseleave', onMouseUp)
  el.removeEventListener('touchstart', onTouchStart)
  el.removeEventListener('touchmove', onTouchMove)
  el.removeEventListener('touchend', onTouchEnd)
}

// ─── Click Handler ─────────────────────────────────────────────────
const handleCardClick = (index) => {
  if (hasDraggedEnough) return
  if (index !== currentIndex.value) {
    goToIndex(index)
  }
}

const readmoreLink = (id) => {
  return window.location.origin + `/portfolio/detail/${id}`
}

// ─── Animasi Scroll ────────────────────────────────────────────────
const initAnimations = () => {
  entranceAnimation = gsap.fromTo(sectionRef.value,
    { opacity: 0, y: 50 },
    {
      opacity: 1, y: 0, duration: 0.7, ease: 'power2.out',
      scrollTrigger: {
        trigger: sectionRef.value,
        start: 'top 85%',
        toggleActions: 'play none none reverse'
      }
    }
  )
}

// ─── Resize handler ──────────────────────────────────────────────
let resizeTimeout = null
const handleResize = () => {
  clearTimeout(resizeTimeout)
  resizeTimeout = setTimeout(() => {
    updateVisibleCards()
    updateCardsPosition(false)
  }, 150)
}

// ─── Lifecycle ─────────────────────────────────────────────────────
onMounted(() => {
  nextTick(() => {
    updateVisibleCards()
    updateCardsPosition(false)
    addDragEvents()
    requestAnimationFrame(() => initAnimations())
    initVisibilityObserver()

    window.addEventListener('resize', handleResize)
  })
})

onUnmounted(() => {
  stopAutoPlay()
  entranceAnimation?.kill()
  if (dragFrame) cancelAnimationFrame(dragFrame)
  dragSetters = []
  removeDragEvents()
  window.removeEventListener('resize', handleResize)
  clearTimeout(resizeTimeout)
  visibilityObserver?.disconnect()
})
</script>

<template>
  <section ref="sectionRef" class="py-10 bg-black overflow-hidden">
    <div class="w-full">
      <div class="slider-wrapper relative overflow-hidden">

        <div
          ref="sliderContainerRef"
          class="relative h-[620px] md:h-[680px] w-full cursor-grab overflow-visible"
        >
          <!-- Track untuk sliding dengan GSAP -->
          <div
            ref="trackRef"
            class="relative flex items-center justify-center h-full"
            style="will-change: transform;"
          >
            <!-- Cards -->
            <div
              v-for="(work, index) in works"
              :key="work.id"
              :ref="el => cardsRef[index] = el"
              class="absolute cursor-pointer rounded-3xl overflow-hidden"
              style="width: 280px; height: 580px;"
              @click="handleCardClick(index)"
            >
              <div class="relative bg-white rounded-3xl overflow-hidden shadow-xl border-0 w-full h-full">
                <!-- Image -->
                <div class="relative w-full h-full overflow-hidden rounded-3xl">
                  <img
                    :src="work.image"
                    :alt="work.title"
                    :loading="index === currentIndex ? 'eager' : 'lazy'"
                    decoding="async"
                    :fetchpriority="index === currentIndex ? 'high' : 'low'"
                    class="w-full h-full object-cover"
                    draggable="false"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-transparent rounded-3xl"></div>
                </div>

                <!-- Content - Tanpa opacity, selalu solid -->
                <div :class="[work.background, 'absolute bottom-0 left-0 right-0 p-5 text-white rounded-3xl']">
                  <div class="flex items-start gap-2 text-xs mb-2">
                    <span class="font-semibold tracking-wider text-white bg-black px-2 py-1 rounded-xl">
                      <img :src="work.categoryIcon" :alt="work.category" loading="lazy" decoding="async" :class="work.size" class="text-white inline-block">
                      {{ work.category }}
                    </span>
                  </div>

                  <h3 class="text-2xl font-bold mb-1 text-start">{{ work.title }}</h3>

                  <p class="text-black mt-3 line-clamp-2 text-start text-sm">
                    {{ work.description }}
                  </p>

                  <div class="flex justify-end">
                    <a
                      :href="readmoreLink(work.id)"
                      class="bg-black rounded-full hover:bg-[#0cb7ff] px-4 py-2 flex items-center gap-1 text-md font-semibold text-white mt-3 transition-colors"
                      @click.stop
                    >
                      READ MORE
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <button
          class="nav-arrow-left nav-btn absolute top-1/2 -translate-y-1/2 z-40
                 bg-white backdrop-blur-sm rounded-full p-3
                 hover:bg-white/30 hover:scale-110
                 border border-white/25 transition-all duration-200"
          style="left: 20px;"
          @click="slideLeft"
        >
          <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          class="nav-arrow-right nav-btn absolute top-1/2 -translate-y-1/2 z-40
                 bg-white backdrop-blur-sm rounded-full p-3
                 hover:bg-white/30 hover:scale-110
                 border border-white/25 transition-all duration-200"
          style="right: 20px;"
          @click="slideRight"
        >
          <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <!-- Indicator dots -->
        <!-- <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-40 flex gap-2 pb-4">
          <button
            v-for="i in works.length"
            :key="i"
            class="w-2 h-2 rounded-full transition-all duration-300"
            :class="[
              i - 1 === currentIndex
                ? 'bg-white w-6'
                : 'bg-white/40 hover:bg-white/60'
            ]"
            @click="goToIndex(i - 1)"
          />
        </div> -->

      </div>
    </div>
  </section>
</template>

<style scoped>
.nav-btn {
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease, transform 0.2s ease, background-color 0.2s ease;
}

.slider-wrapper:hover .nav-btn {
  opacity: 1;
  pointer-events: auto;
}

.nav-btn:disabled {
  opacity: 0.2 !important;
  pointer-events: none;
}

.rounded-3xl {
  border-radius: 1.5rem !important;
}

div[class*="rounded-3xl"] {
  border: none !important;
  outline: none !important;
}

.cursor-grab  { cursor: grab; }
.cursor-grabbing { cursor: grabbing; }

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Pastikan card tetap rounded */
.absolute.cursor-pointer {
  border-radius: 1.5rem !important;
  overflow: hidden !important;
  transform: translateZ(0);
  will-change: transform, opacity;
  backface-visibility: hidden;
}

.absolute.cursor-pointer > div {
  border-radius: 1.5rem !important;
  overflow: hidden !important;
}
</style>