<script setup>
import { ref, onMounted } from 'vue'
import gsap from '@/plugins/gsap'

const testimonials = ref([
  {
    id: 1,
    name: 'Sarah Johnson',
    role: 'Creative Director',
    content: 'SUPLAYBOX transformed our brand identity completely. Their attention to detail is amazing!',
    avatar: '👩‍🎨',
    rating: 5
  },
  {
    id: 2,
    name: 'Michael Chen',
    role: 'Startup Founder',
    content: 'The illustration service exceeded our expectations. Highly recommended!',
    avatar: '👨‍💼',
    rating: 5
  },
  {
    id: 3,
    name: 'Emily Rodriguez',
    role: 'Marketing Manager',
    content: 'Professional, creative, and always deliver on time. Best design service ever!',
    avatar: '👩‍💻',
    rating: 5
  },
  {
    id: 4,
    name: 'David Kim',
    role: 'Product Designer',
    content: 'Their packaging design helped our product stand out on shelves.',
    avatar: '👨‍🎨',
    rating: 5
  }
])

const testimonialRefs = ref([])
const sectionRef = ref(null)

onMounted(() => {
  // Animasi section masuk
  gsap.from(sectionRef.value, {
    scrollTrigger: {
      trigger: sectionRef.value,
      start: 'top 70%',
    },
    opacity: 0,
    y: 50,
    duration: 1
  })

  // Animasi cards dengan efek bergantian
  gsap.from(testimonialRefs.value, {
    scrollTrigger: {
      trigger: sectionRef.value,
      start: 'top 60%',
    },
    opacity: 0,
    x: (index) => index % 2 === 0 ? -50 : 50,
    rotation: (index) => index % 2 === 0 ? -5 : 5,
    stagger: 0.2,
    duration: 0.8,
    ease: "power3.out"
  })

  // Animasi floating untuk setiap testimonial
  testimonialRefs.value.forEach((card, index) => {
    gsap.to(card, {
      y: -5,
      rotation: 1,
      duration: 3,
      repeat: -1,
      yoyo: true,
      ease: "sine.inOut",
      delay: index * 0.3
    })
  })
})

const handleCardHover = (index, isEnter) => {
  gsap.to(testimonialRefs.value[index], {
    scale: isEnter ? 1.02 : 1,
    boxShadow: isEnter ? "0 25px 50px -12px rgba(0, 0, 0, 0.25)" : "0 10px 15px -3px rgba(0, 0, 0, 0.1)",
    duration: 0.3
  })
}
</script>

<template>
  <section id="testimonials" ref="sectionRef" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="section-title text-3xl font-bold text-center mb-4">What Our Clients Say</h2>
      <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
        Don't just take our word for it - hear from some of our amazing clients
      </p>

      <div class="grid md:grid-cols-2 gap-6">
        <div
          v-for="(testimonial, index) in testimonials"
          :key="testimonial.id"
          :ref="el => testimonialRefs[index] = el"
          class="bg-gray-50 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all cursor-pointer"
          @mouseenter="handleCardHover(index, true)"
          @mouseleave="handleCardHover(index, false)"
        >
          <div class="flex items-center mb-4">
            <div class="text-4xl mr-4">{{ testimonial.avatar }}</div>
            <div>
              <h3 class="font-semibold text-lg">{{ testimonial.name }}</h3>
              <p class="text-gray-600 text-sm">{{ testimonial.role }}</p>
            </div>
          </div>

          <p class="text-gray-700 mb-4">"{{ testimonial.content }}"</p>

          <div class="flex text-yellow-400">
            <span v-for="star in testimonial.rating" :key="star">★</span>
          </div>

          <!-- Decorative quote mark -->
          <div class="text-6xl text-indigo-200 absolute bottom-4 right-4 opacity-50">"</div>
        </div>
      </div>

      <!-- Connection Section -->
      <div class="mt-16 text-center">
        <p class="text-lg text-gray-700 mb-4">Perceptively And Connection.</p>
        <div class="flex justify-center space-x-4">
          <button class="bg-pink-600 text-white px-6 py-3 rounded-full hover:bg-pink-700 transition transform hover:scale-105">
            Connect on Pinterest
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
