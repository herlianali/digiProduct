<script setup>
import { ref, onMounted } from 'vue'
import gsap from '@/plugins/gsap'

const cards = ref([
  { id: 1, title: 'FREE SKETCH!', description: 'Get your free sketch today', icon: '🎨', color: 'from-pink-500 to-rose-500' },
  { id: 2, title: 'Starter Pack', description: 'Canva/Shutterstock/Iconscout', icon: '📦', color: 'from-purple-500 to-indigo-500' },
  { id: 3, title: '800+ Projects', description: 'Project Finished', icon: '✨', color: 'from-blue-500 to-cyan-500' },
  { id: 4, title: 'Unlimited Prints', description: 'Design Service', icon: '🖨️', color: 'from-green-500 to-emerald-500' },
  { id: 5, title: 'Brand Identity', description: 'Logo, Poster, Packaging', icon: '🎯', color: 'from-orange-500 to-red-500' },
  { id: 6, title: 'Illustration', description: '2D, Character, Game', icon: '🎨', color: 'from-teal-500 to-cyan-500' },
])

const cardRefs = ref([])
const bannerRef = ref(null)

onMounted(() => {
  // Animasi banner masuk
  gsap.from(bannerRef.value, {
    scrollTrigger: {
      trigger: bannerRef.value,
      start: 'top 80%',
    },
    opacity: 0,
    y: 50,
    duration: 1
  })

  // Animasi cards masuk dengan stagger
  gsap.from(cardRefs.value, {
    scrollTrigger: {
      trigger: bannerRef.value,
      start: 'top 70%',
    },
    scale: 0.8,
    opacity: 0,
    y: 50,
    stagger: 0.1,
    duration: 0.6,
    ease: "back.out(1.2)"
  })

  // Animasi floating untuk cards
  cardRefs.value.forEach((card, index) => {
    gsap.to(card, {
      y: -10,
      duration: 2,
      repeat: -1,
      yoyo: true,
      ease: "power1.inOut",
      delay: index * 0.2
    })
  })
})

const handleMouseEnter = (index) => {
  gsap.to(cardRefs.value[index], {
    scale: 1.05,
    y: -15,
    duration: 0.3,
    ease: "power2.out",
    boxShadow: "0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)"
  })
}

const handleMouseLeave = (index) => {
  gsap.to(cardRefs.value[index], {
    scale: 1,
    y: -10,
    duration: 0.3,
    ease: "power2.out",
    boxShadow: "0 10px 15px -3px rgba(0, 0, 0, 0.1)"
  })
}
</script>

<template>
  <section id="services" ref="bannerRef" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="section-title text-3xl font-bold text-center mb-12">Our Services & Features</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(card, index) in cards"
          :key="card.id"
          :ref="el => cardRefs[index] = el"
          class="relative group cursor-pointer"
          @mouseenter="handleMouseEnter(index)"
          @mouseleave="handleMouseLeave(index)"
        >
          <div :class="`bg-gradient-to-br ${card.color} rounded-2xl p-6 text-white shadow-lg h-full transform transition-all`">
            <div class="text-4xl mb-4">{{ card.icon }}</div>
            <h3 class="text-xl font-semibold mb-2">{{ card.title }}</h3>
            <p class="text-white/90">{{ card.description }}</p>

            <!-- Hover effect overlay -->
            <div class="absolute inset-0 bg-white/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </div>
        </div>
      </div>

      <!-- Stats Section -->
      <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="text-center">
          <div class="text-3xl font-bold text-indigo-600">800+</div>
          <div class="text-gray-600">Projects Finished</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-indigo-600">24/7</div>
          <div class="text-gray-600">Support</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-indigo-600">100+</div>
          <div class="text-gray-600">Happy Clients</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-indigo-600">15+</div>
          <div class="text-gray-600">Design Awards</div>
        </div>
      </div>
    </div>
  </section>
</template>
