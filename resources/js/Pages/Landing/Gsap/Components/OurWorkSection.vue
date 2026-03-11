<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from '@/plugins/gsap'

const works = ref([
  {
    id: 1,
    title: 'Brand Identity',
    category: 'Design',
    image: 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=400&h=600&fit=crop',
    color: 'from-indigo-500 to-purple-600'
  },
  {
    id: 2,
    title: 'Illustration',
    category: 'Art',
    image: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=600&fit=crop',
    color: 'from-pink-500 to-rose-600'
  },
  {
    id: 3,
    title: 'Packaging Design',
    category: 'Product',
    image: 'https://images.unsplash.com/photo-1589119908995-c6837fa1483f?w=400&h=600&fit=crop',
    color: 'from-green-500 to-emerald-600'
  },
  {
    id: 4,
    title: 'Character Design',
    category: 'Illustration',
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=600&fit=crop',
    color: 'from-blue-500 to-cyan-600'
  },
  {
    id: 5,
    title: 'Social Media',
    category: 'Marketing',
    image: 'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=400&h=600&fit=crop',
    color: 'from-orange-500 to-red-600'
  }
])

const currentIndex = ref(2) // Mulai dari card tengah
const cardRefs = ref([])
const sectionRef = ref(null)
const sliderRef = ref(null)
let scrollTrigger = null
let animation = null

onMounted(() => {
  initSlider()
  setupScrollTrigger()
})

onUnmounted(() => {
  if (scrollTrigger) scrollTrigger.kill()
  if (animation) animation.kill()
})

const initSlider = () => {
  // Posisikan cards
  updateCardsPosition()
}

const updateCardsPosition = () => {
  cardRefs.value.forEach((card, index) => {
    if (!card) return

    const offset = index - currentIndex.value
    const isCenter = offset === 0

    gsap.to(card, {
      scale: isCenter ? 1.2 : 0.8,
      opacity: isCenter ? 1 : 0.6,
      x: offset * 120,
      zIndex: isCenter ? 10 : 5 - Math.abs(offset),
      rotationY: offset * -15,
      duration: 0.5,
      ease: "power2.out",
      boxShadow: isCenter ? "0 30px 40px -15px rgba(0,0,0,0.3)" : "0 10px 20px -5px rgba(0,0,0,0.1)"
    })
  })
}

const slideLeft = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
    updateCardsPosition()

    // Animasi panah
    gsap.fromTo('.arrow-left',
      { x: 0, opacity: 1 },
      { x: -10, opacity: 0.7, duration: 0.2, yoyo: true, repeat: 1 }
    )
  }
}

const slideRight = () => {
  if (currentIndex.value < works.value.length - 1) {
    currentIndex.value++
    updateCardsPosition()

    // Animasi panah
    gsap.fromTo('.arrow-right',
      { x: 0, opacity: 1 },
      { x: 10, opacity: 0.7, duration: 0.2, yoyo: true, repeat: 1 }
    )
  }
}

const setupScrollTrigger = () => {
  scrollTrigger = gsap.timeline({
    scrollTrigger: {
      trigger: sectionRef.value,
      start: 'top 70%',
      toggleActions: 'play none none reverse'
    }
  })

  scrollTrigger.from(sectionRef.value, {
    opacity: 0,
    y: 50,
    duration: 1
  })
  .from('.work-title', {
    opacity: 0,
    y: 30,
    duration: 0.6
  }, '-=0.5')
}

const handleCardHover = (index, isEnter) => {
  if (index === currentIndex.value) {
    gsap.to(cardRefs.value[index], {
      scale: isEnter ? 1.25 : 1.2,
      duration: 0.3,
      ease: "power2.out"
    })
  }
}
</script>

<template>
  <section ref="sectionRef" class="py-20 bg-gradient-to-b from-gray-50 to-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="work-title text-3xl font-bold text-center mb-4">Our Work</h2>
      <p class="text-gray-600 text-center mb-12">Explore our latest projects and creative works</p>

      <div ref="sliderRef" class="relative h-[500px] flex items-center justify-center perspective-1000">
        <!-- Cards Container -->
        <div class="relative w-full h-full flex items-center justify-center">
          <div
            v-for="(work, index) in works"
            :key="work.id"
            :ref="el => cardRefs[index] = el"
            class="absolute cursor-pointer transition-all duration-300"
            :style="{
              transformStyle: 'preserve-3d',
              transform: `translateX(${(index - currentIndex) * 120}px) translateZ(${index === currentIndex ? 100 : 0}px)`
            }"
            @mouseenter="handleCardHover(index, true)"
            @mouseleave="handleCardHover(index, false)"
            @click="currentIndex = index"
          >
            <div class="relative w-64 h-96 rounded-2xl overflow-hidden shadow-xl">
              <!-- Image -->
              <img
                :src="work.image"
                :alt="work.title"
                class="w-full h-full object-cover"
              />

              <!-- Overlay -->
              <div :class="`absolute inset-0 bg-gradient-to-t ${work.color} opacity-75`"></div>

              <!-- Content -->
              <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <p class="text-sm opacity-90 mb-1">{{ work.category }}</p>
                <h3 class="text-xl font-bold">{{ work.title }}</h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Arrows -->
        <button
          class="arrow-left absolute left-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition z-20"
          @click="slideLeft"
          :disabled="currentIndex === 0"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          class="arrow-right absolute right-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition z-20"
          @click="slideRight"
          :disabled="currentIndex === works.length - 1"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- Dots Indicator -->
      <div class="flex justify-center mt-8 space-x-2">
        <button
          v-for="(work, index) in works"
          :key="index"
          @click="currentIndex = index"
          :class="[
            'w-2 h-2 rounded-full transition-all duration-300',
            index === currentIndex ? 'w-8 bg-indigo-600' : 'bg-gray-300 hover:bg-gray-400'
          ]"
        ></button>
      </div>
    </div>
  </section>
</template>

<style scoped>
.perspective-1000 {
  perspective: 1000px;
}
</style>
