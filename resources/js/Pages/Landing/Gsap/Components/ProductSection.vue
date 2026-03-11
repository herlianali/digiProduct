<script setup>
import { ref, onMounted } from 'vue'
import gsap from '@/plugins/gsap'

const products = ref([
  {
    id: 1,
    name: 'Design Starter Pack',
    price: '$49',
    description: 'Canva, Shutterstock, Iconscout templates',
    image: 'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=400&h=300&fit=crop',
    badge: 'Popular'
  },
  {
    id: 2,
    name: 'Brand Identity Pro',
    price: '$129',
    description: 'Complete brand identity package',
    image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=300&fit=crop',
    badge: 'Best Seller'
  },
  {
    id: 3,
    name: 'Illustration Bundle',
    price: '$89',
    description: '2D, character, and mascot illustrations',
    image: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=300&fit=crop',
    badge: 'New'
  },
  {
    id: 4,
    name: 'Unlimited Prints',
    price: '$199/mo',
    description: 'Unlimited design requests & prints',
    image: 'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=400&h=300&fit=crop',
    badge: 'Premium'
  }
])

const productRefs = ref([])
const sectionRef = ref(null)

onMounted(() => {
  // Animasi section
  gsap.from(sectionRef.value, {
    scrollTrigger: {
      trigger: sectionRef.value,
      start: 'top 70%',
    },
    opacity: 0,
    y: 50,
    duration: 1
  })

  // Animasi products masuk
  gsap.from(productRefs.value, {
    scrollTrigger: {
      trigger: sectionRef.value,
      start: 'top 60%',
    },
    opacity: 0,
    y: 100,
    rotation: 5,
    stagger: 0.15,
    duration: 0.8,
    ease: "back.out(1.2)"
  })

  // Animasi hover untuk setiap product
  productRefs.value.forEach((product, index) => {
    // Animasi floating
    gsap.to(product, {
      y: -8,
      duration: 2.5,
      repeat: -1,
      yoyo: true,
      ease: "sine.inOut",
      delay: index * 0.2
    })
  })
})

const handleProductHover = (index, isEnter) => {
  const tl = gsap.timeline()

  tl.to(productRefs.value[index], {
    scale: isEnter ? 1.05 : 1,
    y: isEnter ? -15 : -8,
    duration: 0.3,
    ease: "power2.out"
  })

  // Animasi badge
  const badge = productRefs.value[index]?.querySelector('.product-badge')
  if (badge) {
    gsap.to(badge, {
      scale: isEnter ? 1.1 : 1,
      duration: 0.2
    })
  }
}

const handleBuyClick = (product) => {
  gsap.timeline()
    .to('.cart-icon', {
      scale: 1.5,
      rotation: 10,
      duration: 0.2,
      yoyo: true,
      repeat: 1
    })
    .to('.cart-icon', {
      rotation: -10,
      duration: 0.1
    })

  alert(`Added ${product.name} to cart!`)
}
</script>

<template>
  <section id="products" ref="sectionRef" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="section-title text-3xl font-bold mb-4">Our Products</h2>
        <p class="text-gray-600">Choose the perfect package for your needs</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="(product, index) in products"
          :key="product.id"
          :ref="el => productRefs[index] = el"
          class="relative bg-white rounded-xl shadow-lg overflow-hidden group cursor-pointer"
          @mouseenter="handleProductHover(index, true)"
          @mouseleave="handleProductHover(index, false)"
        >
          <!-- Badge -->
          <div class="product-badge absolute top-4 right-4 z-10">
            <span :class="[
              'px-3 py-1 rounded-full text-xs font-semibold',
              product.badge === 'Popular' ? 'bg-green-500 text-white' :
              product.badge === 'Best Seller' ? 'bg-yellow-500 text-white' :
              product.badge === 'New' ? 'bg-blue-500 text-white' :
              'bg-purple-500 text-white'
            ]">
              {{ product.badge }}
            </span>
          </div>

          <!-- Image -->
          <div class="relative h-48 overflow-hidden">
            <img
              :src="product.image"
              :alt="product.name"
              class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
            />
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-2">{{ product.name }}</h3>
            <p class="text-gray-600 text-sm mb-4">{{ product.description }}</p>

            <div class="flex items-center justify-between">
              <span class="text-2xl font-bold text-indigo-600">{{ product.price }}</span>
              <button
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition transform hover:scale-105"
                @click="handleBuyClick(product)"
              >
                Buy Now
              </button>
            </div>
          </div>

          <!-- Hover effect overlay -->
          <div class="absolute inset-0 border-2 border-indigo-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="mt-16 text-center">
        <div class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8">
          <h3 class="text-2xl font-bold mb-2">Ready to start your project?</h3>
          <p class="mb-6">Get your FREE SKETCH today!</p>
          <button class="bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition transform hover:scale-105">
            Claim Your Free Sketch
          </button>
        </div>
      </div>

      <!-- Cart Icon Animation -->
      <div class="cart-icon fixed bottom-8 right-8 bg-indigo-600 text-white p-4 rounded-full shadow-lg cursor-pointer hover:bg-indigo-700 transition z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
    </div>
  </section>
</template>
