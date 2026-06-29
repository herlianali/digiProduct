<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed, watch } from 'vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const works = ref([
  {
    id: 1,
    title: 'ATLENS',
    subtitle: 'CANNED ENERGY',
    tagline: 'BLOOD OF THE DRAGON',
    category: 'POSTER',
    location: 'OKEECHOBEE',
    background: 'bg-[#b41f19]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-1.png'
  },
  {
    id: 2,
    title: 'X-HAWN',
    subtitle: 'SKI SAINT BRUNO',
    category: 'POSTER',
    background: 'bg-[#e5f001]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-2.jpg'
  },
  {
    id: 3,
    title: 'MONSTER',
    subtitle: 'International Films Festival',
    location: 'Pigdon Street',
    category: 'POSTER',
    background: 'bg-[#8b27b7]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-3.jpg'
  },
  {
    id: 4,
    title: 'Cherry-King',
    subtitle: 'Brand Identity',
    category: 'FEATURED',
    background: 'bg-[#3d54a4]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-4.jpg'
  },
  {
    id: 5,
    title: 'OKEECHOBEE',
    subtitle: 'Canned Energy',
    category: 'POSTER',
    background: 'bg-[#b4f000]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-5.jpg'
  },
  {
    id: 6,
    title: 'X-HAWN',
    subtitle: 'SKI SAINT BRUNO',
    category: 'POSTER',
    background: 'bg-[#e5f001]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-2.jpg'
  },
  {
    id: 7,
    title: 'Cherry-King',
    subtitle: 'Brand Identity',
    category: 'FEATURED',
    background: 'bg-[#3d54a4]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-4.jpg'
  },
  {
    id: 8,
    title: 'Cherry-King',
    subtitle: 'Brand Identity',
    category: 'FEATURED',
    background: 'bg-[#3d54a4]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: '/assets/image/portfolio/porto-4.jpg'
  },
])

const sectionRef = ref(null)
const sliderContainerRef = ref(null)
const trackRef = ref(null)
const cardsRef = ref([])

const CARD_W = 320
const CARD_GAP = 30
const AUTOPLAY_DELAY = 4000

const currentIndex = ref(0)
const visibleCards = ref(5)
const isAnimating = ref(false)

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
    
    // Pastikan card di dalam visible range
    const isVisible = distance <= visibleCards.value
    
    // Scale - HANYA card tengah yang lebih besar
    // Card lain tetap scale 1 (tinggi sama)
    let scale = 1
    if (isCenter) {
      scale = 1.15 // Hanya card tengah yang membesar
    }
    
    // Opacity - card samping lebih transparan
    let opacity = 1
    if (isCenter) {
      opacity = 1
    } else if (distance <= visibleCards.value) {
      opacity = 1 - (distance / (visibleCards.value + 1)) * 0.4
      opacity = Math.max(0.5, opacity)
    } else {
      opacity = 0
    }
    
    // Z-index
    const zIndex = isCenter ? 50 : 30 - distance
    
    // Brightness - card samping lebih gelap
    const brightness = isCenter ? 1 : Math.max(0.5, 1 - distance * 0.1)
    
    // POSISI X - dengan spacing yang konsisten
    const xPosition = offset * (CARD_W + CARD_GAP)
    
    // Gunakan transform origin di center agar zoom dari tengah
    gsap.set(card, { transformOrigin: 'center center' })

    // Filter hanya brightness (tanpa blur)
    const filterValue = `brightness(${isVisible ? brightness : 0})`

    if (animate) {
      gsap.to(card, {
        x: xPosition,
        scale: scale,
        opacity: isVisible ? opacity : 0,
        zIndex: zIndex,
        duration: 0.7,
        ease: 'power3.inOut',
        filter: filterValue,
        overwrite: true,
        onComplete: () => {
          if (!isVisible) {
            gsap.set(card, { pointerEvents: 'none' })
          } else {
            gsap.set(card, { pointerEvents: 'auto' })
          }
        }
      })
    } else {
      gsap.set(card, {
        x: xPosition,
        scale: scale,
        opacity: isVisible ? opacity : 0,
        zIndex: zIndex,
        filter: filterValue,
        pointerEvents: isVisible ? 'auto' : 'none'
      })
    }

    // Animasi konten di dalam card - HANYA card tengah yang kontennya full
    const content = card.querySelector('.card-content')
    if (content) {
      if (animate) {
        gsap.to(content, {
          opacity: isCenter ? 1 : 0.5,
          y: isCenter ? 0 : 10,
          duration: 0.5,
          delay: isCenter ? 0.1 : 0,
          ease: 'power2.out',
          overwrite: true
        })
      } else {
        gsap.set(content, {
          opacity: isCenter ? 1 : 0.5,
          y: isCenter ? 0 : 10
        })
      }
    }
  })
}

// ─── Navigasi ──────────────────────────────────────────────────────
const goToIndex = (newIndex, fromAutoplay = false) => {
  if (isAnimating.value) return
  
  // Wrap index untuk circular
  const total = works.value.length
  let targetIndex = newIndex
  if (targetIndex < 0) targetIndex = total - 1
  if (targetIndex >= total) targetIndex = 0
  
  isAnimating.value = true
  currentIndex.value = targetIndex
  updateCardsPosition(true)
  
  setTimeout(() => {
    isAnimating.value = false
  }, 800)

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

// ─── Touch & Drag ──────────────────────────────────────────────────
let touchStartX = 0
let touchStartY = 0
let isSwiping = false
let isHorizontalSwipe = null

const onTouchStart = (e) => {
  touchStartX = e.touches[0].clientX
  touchStartY = e.touches[0].clientY
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
  }
}

const onTouchEnd = (e) => {
  if (!isSwiping) return
  isSwiping = false
  const deltaX = e.changedTouches[0].clientX - touchStartX

  if (isHorizontalSwipe && Math.abs(deltaX) > 40) {
    if (deltaX > 0) slideLeft()
    else slideRight()
  } else {
    startAutoPlay()
  }
  isHorizontalSwipe = null
}

// Mouse drag
let mouseStartX = 0
let isDragging = false

const onMouseDown = (e) => {
  isDragging = true
  mouseStartX = e.clientX
  stopAutoPlay()
  gsap.set(sliderContainerRef.value, { cursor: 'grabbing' })
}

const onMouseMove = (e) => {
  if (!isDragging) return
  const delta = e.clientX - mouseStartX
  if (Math.abs(delta) > 50) {
    if (delta > 0) slideLeft()
    else slideRight()
    isDragging = false
  }
}

const onMouseUp = () => {
  if (!isDragging) return
  isDragging = false
  startAutoPlay()
  gsap.set(sliderContainerRef.value, { cursor: 'grab' })
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
  if (index !== currentIndex.value) {
    goToIndex(index)
  }
}

const readmoreLink = (id) => {
  return window.location.origin + `/portfolio/detail/${id}`
}

// ─── Animasi Scroll ────────────────────────────────────────────────
const initAnimations = () => {
  gsap.fromTo(sectionRef.value,
    { opacity: 0, y: 50 },
    {
      opacity: 1, y: 0, duration: 1,
      scrollTrigger: {
        trigger: sectionRef.value,
        start: 'top 80%',
        toggleActions: 'play none none reverse'
      }
    }
  )
}

// ─── Lifecycle ─────────────────────────────────────────────────────
onMounted(() => {
  nextTick(() => {
    updateVisibleCards()
    updateCardsPosition(false)
    addDragEvents()
    initAnimations()
    startAutoPlay()
    
    window.addEventListener('resize', () => {
      updateVisibleCards()
      updateCardsPosition(false)
    })
  })
})

onUnmounted(() => {
  stopAutoPlay()
  ScrollTrigger.getAll().forEach(t => t.kill())
  removeDragEvents()
  window.removeEventListener('resize', updateVisibleCards)
})

// Watch currentIndex untuk update posisi
watch(currentIndex, () => {
  updateCardsPosition(true)
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
              style="width: 320px; height: 580px;"
              @click="handleCardClick(index)"
            >
              <div class="relative bg-white rounded-3xl overflow-hidden shadow-xl border-0 w-full h-full">
                <!-- Image -->
                <div class="relative w-full h-full overflow-hidden rounded-3xl">
                  <img
                    :src="work.image"
                    :alt="work.title"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-transparent rounded-3xl"></div>
                </div>

                <!-- Content -->
                <div :class="[work.background, 'card-content absolute bottom-0 left-0 right-0 p-5 text-white rounded-3xl']">
                  <div class="flex items-start gap-2 text-xs mb-2">
                    <span class="font-semibold tracking-wider text-white bg-black px-2 py-1 rounded-lg">
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
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-40 flex gap-2 pb-4">
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
        </div>

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
}

.absolute.cursor-pointer > div {
  border-radius: 1.5rem !important;
  overflow: hidden !important;
}
</style>