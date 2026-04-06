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
    category: 'POSTER',
    location: 'OKEECHOBEE',
    background: 'bg-[#b41f19]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=600&h=800&fit=crop'
  },
  {
    id: 2,
    title: 'X-HAWN',
    subtitle: 'SKI SAINT BRUNO',
    category: 'POSTER',
    background: 'bg-[#e5f001]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=600&h=800&fit=crop'
  },
  {
    id: 3,
    title: 'MONSTER',
    subtitle: 'International Films Festival',
    location: 'Pigdon Street',
    category: 'POSTER',
    background: 'bg-[#8b27b7]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600&h=800&fit=crop'
  },
  {
    id: 4,
    title: 'Cherry-King',
    subtitle: 'Brand Identity',
    category: 'FEATURED',
    background: 'bg-[#3d54a4]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: 'https://images.unsplash.com/photo-1589119908995-c6837fa1483f?w=600&h=800&fit=crop'
  },
  {
    id: 5,
    title: 'OKEECHOBEE',
    subtitle: 'Canned Energy',
    category: 'POSTER',
    background: 'bg-[#b4f000]',
    description: 'We have collaborated with many individual and international corporate clients from many countries.',
    image: 'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600&h=800&fit=crop'
  }
])

const sectionRef = ref(null)
const sliderContainerRef = ref(null)
const cardsRef = ref([])
const currentIndex = ref(2)

const CARD_W = 320
const CARD_GAP = 30
const AUTOPLAY_DELAY = 5000

// ─── Satu sumber kebenaran untuk interval ───────────────────────────
let autoPlayInterval = null

const stopAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
    autoPlayInterval = null
  }
}

const startAutoPlay = () => {
  stopAutoPlay() // ✅ Selalu clear dulu — cegah interval menumpuk
  autoPlayInterval = setInterval(() => {
    goToIndex(
      currentIndex.value < works.value.length - 1
        ? currentIndex.value + 1
        : 0,
      true // fromAutoplay
    )
  }, AUTOPLAY_DELAY)
}
// ────────────────────────────────────────────────────────────────────

// ─── Navigasi terpusat ──────────────────────────────────────────────
const goToIndex = (newIndex, fromAutoplay = false) => {
  const isLoop = newIndex === 0 && currentIndex.value === works.value.length - 1

  if (isLoop) {
    // Loop balik: snap dulu tanpa animasi, lalu animate
    cardsRef.value.forEach(card => card && gsap.killTweensOf(card))
    currentIndex.value = 0
    snapCardsPosition()
    gsap.delayedCall(0.05, () => updateCardsPosition())
  } else {
    currentIndex.value = newIndex
    updateCardsPosition()
  }

  if (!fromAutoplay) startAutoPlay() // reset timer hanya jika interaksi manual
}

const slideLeft = () => goToIndex(
  currentIndex.value > 0
    ? currentIndex.value - 1
    : works.value.length - 1  // ✅ Loop ke terakhir
)

const slideRight = () => goToIndex(
  currentIndex.value < works.value.length - 1
    ? currentIndex.value + 1
    : 0  // ✅ Loop ke pertama
)
// ────────────────────────────────────────────────────────────────────

onMounted(() => {
  nextTick(() => {
    updateCardsPosition()
    addDragEvents()
    initAnimations()
    startAutoPlay()
  })
})

onUnmounted(() => {
  stopAutoPlay()
  ScrollTrigger.getAll().forEach(t => t.kill())
  removeDragEvents()
})

const updateCardsPosition = () => {
  cardsRef.value.forEach((card, index) => {
    if (!card) return

    const offset = index - currentIndex.value
    const absOffset = Math.abs(offset)
    const isCenter = offset === 0

    const compressionFactor = absOffset === 0 ? 0 : Math.pow(absOffset, 0.75)
    const xPosition = Math.sign(offset) * compressionFactor * (CARD_W + CARD_GAP)
    const scale = isCenter ? 1.15 : Math.max(0.72, 1 - absOffset * 0.14)
    const opacity = isCenter ? 1 : Math.max(0.45, 1 - absOffset * 0.22)
    const zIndex = isCenter ? 30 : 20 - absOffset
    const brightness = isCenter ? 1 : 0.58

    gsap.to(card, {
      x: xPosition, scale, opacity, zIndex,
      duration: 0.6,
      ease: 'back.out(0.3)',
      boxShadow: isCenter
        ? '0 35px 60px -15px rgba(0,0,0,0.6)'
        : '0 10px 20px -10px rgba(0,0,0,0.3)',
      filter: `brightness(${brightness})`,
      overwrite: true
    })

    const content = card.querySelector('.card-content')
    if (content) {
      gsap.to(content, {
        opacity: isCenter ? 1 : 0.55,
        y: isCenter ? 0 : 10,
        duration: isCenter ? 0.4 : 0.3,
        delay: isCenter ? 0.2 : 0,
        ease: 'power2.out',
        overwrite: true
      })
    }
  })
}

const snapCardsPosition = () => {
  cardsRef.value.forEach((card, index) => {
    if (!card) return
    const offset = index - currentIndex.value
    const absOffset = Math.abs(offset)
    const compressionFactor = absOffset === 0 ? 0 : Math.pow(absOffset, 0.75)
    const xPosition = Math.sign(offset) * compressionFactor * (CARD_W + CARD_GAP)

    gsap.set(card, {
      x: xPosition,
      scale: offset === 0 ? 1.15 : Math.max(0.72, 1 - absOffset * 0.14),
      opacity: offset === 0 ? 1 : Math.max(0.45, 1 - absOffset * 0.22),
      zIndex: offset === 0 ? 30 : 20 - absOffset,
      filter: `brightness(${offset === 0 ? 1 : 0.58})`,
    })
  })
}

// ─── Touch & Drag ───────────────────────────────────────────────────
let touchStartX = 0
let touchStartY = 0
let isSwiping = false
let isHorizontalSwipe = null // null = belum ditentukan

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

  // Tentukan arah swipe di 10px pertama
  if (isHorizontalSwipe === null && (Math.abs(deltaX) > 10 || Math.abs(deltaY) > 10)) {
    isHorizontalSwipe = Math.abs(deltaX) > Math.abs(deltaY)
  }

  // Jika horizontal, block scroll vertikal browser
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
    startAutoPlay() // tidak jadi swipe, lanjut autoplay
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
  // ✅ touchmove TIDAK passive agar bisa preventDefault (cegah scroll saat swipe horizontal)
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
// ────────────────────────────────────────────────────────────────────

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

const handleCardClick = (index) => {
  if (index !== currentIndex.value) {
    goToIndex(index)
  }
}
</script>

<template>
  <section ref="sectionRef" class="py-20 md:py-32 bg-black overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Slider Wrapper — grup untuk trigger hover -->
      <div class="slider-wrapper relative">

        <!-- Slider Container -->
        <div
          ref="sliderContainerRef"
          class="relative h-[620px] md:h-[680px] flex items-center justify-center cursor-grab"
        >
          <!-- Cards -->
          <div class="relative w-full h-full flex items-center justify-center">
            <div
              v-for="(work, index) in works"
              :key="work.id"
              :ref="el => cardsRef[index] = el"
              class="absolute w-[280px] md:w-[320px] cursor-pointer"
              @click="handleCardClick(index)"
            >
              <div class="relative bg-white rounded-3xl overflow-hidden shadow-xl border-0">
                <!-- Image — tinggi card ditambah -->
                <div class="relative h-[480px] md:h-[630px] overflow-hidden">
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
                    <span class="font-semibold tracking-wider text-white bg-black px-2 py-1 rounded rounded-lg">{{ work.category }}</span>
                  </div>

                  <h3 class="text-2xl font-bold mb-1 text-start">{{ work.title }}</h3>

                  <p class=" text-black mt-3 line-clamp-2 text-start">
                    {{ work.description }}
                  </p>

                  <div class="flex justify-end">
                    <a
                      href="#"
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

        <button
          class="nav-arrow-left nav-btn absolute top-1/2 -translate-y-1/2 z-40
                 bg-white backdrop-blur-sm rounded-full p-3
                 hover:bg-white/30 hover:scale-110
                 border border-white/25 transition-all duration-200
                 disabled:opacity-25 disabled:cursor-not-allowed"
          style="left: calc(10% - 400px)"
          @click="slideLeft"
          :disabled="currentIndex === 0"
        >
          <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          class="nav-arrow-right nav-btn absolute top-1/2 -translate-y-1/2 z-40
                 bg-white backdrop-blur-sm rounded-full p-3
                 hover:bg-white/30 hover:scale-110
                 border border-white/25 transition-all duration-200
                 disabled:opacity-25 disabled:cursor-not-allowed"
          style="right: calc(10% - 400px)"
          @click="slideRight"
          :disabled="currentIndex === works.length - 1"
        >
          <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
          </svg>
        </button>

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

/* Tetap sembunyikan tombol disabled meski hover */
.slider-wrapper:hover .nav-btn:disabled {
  opacity: 0.2;
}

/* Rounded corners */
.rounded-3xl {
  border-radius: 1.5rem;
}

/* Hapus border dari semua card */
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

@media (max-width: 768px) {
  /* Di layar kecil, tempel lebih ke tepi card tengah */
  .nav-arrow-left  { left:  calc(50% - 160px) !important; }
  .nav-arrow-right { right: calc(50% - 160px) !important; }
}
</style>