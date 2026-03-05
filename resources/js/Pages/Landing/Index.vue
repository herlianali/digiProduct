<template>
  <!-- Loading Screen -->
  <transition name="rolling-door">
    <div v-if="isLoading" class="loading-screen">
      <div class="door-panel">
        <div class="flex flex-col items-center">
          <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 animate-bounce border-4 border-black overflow-hidden">
            <img src="/public/assets/image/logo-supplaybox.png" class="w-full h-full object-cover" alt="SupplayBox">
          </div>
          <div class="font-[1000] text-black text-4xl italic tracking-tighter uppercase">SUPPLAY<span class="opacity-50">BOX</span></div>
          <div class="w-48 h-1 bg-black/20 mt-4 overflow-hidden">
            <div class="h-full bg-black animate-progress"></div>
          </div>
        </div>
      </div>
    </div>
  </transition>

  <!-- Side Menu -->
  <transition name="slide-menu">
    <div v-if="isMenuOpen" class="fixed inset-0 z-[200] flex">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="isMenuOpen = false"></div>
      <div class="relative w-full max-w-lg bg-white h-full p-12 flex flex-col justify-between shadow-2xl border-r-8 border-[#FFEB00]">
        <button @click="isMenuOpen = false" class="self-start bg-black text-white px-6 py-2 font-black italic uppercase skew-x-[-10deg] hover:bg-[#FFEB00] hover:text-black transition-colors">
          CLOSE ✕
        </button>
        
        <div class="flex flex-col space-y-4">
          <a v-for="(item, i) in ['HOME', 'OUR WORK', 'ABOUT US', 'SHOP', 'HIRE US']" 
             :key="item" href="#" 
             class="text-5xl md:text-7xl font-[1000] italic tracking-tighter text-black hover:text-[#FFEB00] transition-all hover:translate-x-6 uppercase"
             :style="{ transitionDelay: `${i * 50}ms` }">
            {{ item }}
          </a>
        </div>

        <div class="mt-12 pt-8 border-t-4 border-black">
          <p class="text-xs font-black tracking-[0.3em] text-black uppercase mb-4 italic">Socials</p>
          <div class="flex space-x-8 text-black text-2xl">
            <i class="fab fa-instagram hover:text-[#FFEB00] cursor-pointer transition-colors"></i>
            <i class="fab fa-behance hover:text-[#FFEB00] cursor-pointer transition-colors"></i>
            <i class="fab fa-dribbble hover:text-[#FFEB00] cursor-pointer transition-colors"></i>
          </div>
        </div>
      </div>
    </div>
  </transition>

  <div class="portfolio-page bg-white text-black selection:bg-[#FFEB00] selection:text-black overflow-x-hidden">
    <!-- Grainy Texture Overlay -->
    <div class="fixed inset-0 opacity-[0.03] pointer-events-none z-[999] bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>

    <!-- Floating Buttons -->
    <transition name="fade">
      <div v-if="showScrollButton" 
           @click="scrollToTop" 
           class="fixed bottom-8 right-8 z-[999] w-16 h-16 bg-[#FFEB00] border-4 border-black rounded-full flex items-center justify-center cursor-pointer hover:scale-110 hover:shadow-[8px_8px_0px_rgba(0,0,0,1)] transition-all duration-300 group">
        <i class="fas fa-arrow-up text-3xl text-black group-hover:animate-bounce"></i>
      </div>
    </transition>

    <div @click="openOrderNow" 
         class="fixed bottom-8 left-8 z-[999] w-20 h-20 bg-white border-4 border-black rounded-full flex items-center justify-center cursor-pointer hover:scale-110 hover:shadow-[8px_8px_0px_rgba(255,235,0,1)] transition-all duration-300 overflow-hidden group">
      <img src="/public/assets/image/logo-supplaybox.png" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" alt="Order Now">
      <span class="absolute -top-2 -right-2 w-6 h-6 bg-[#FFEB00] border-2 border-black rounded-full flex items-center justify-center text-xs font-black animate-pulse">!</span>
    </div>

    <!-- Navbar -->
    <nav class="py-8 px-10 flex justify-between items-center bg-white border-b-4 border-black sticky top-0 z-50">
      <div class="logo font-black text-3xl tracking-tighter uppercase italic">
        SUPPLAY<span class="text-[#FFEB00] drop-shadow-[2px_2px_0px_rgba(0,0,0,1)]">BOX</span>
      </div>
      
      <div class="flex items-center space-x-8">
        <div class="relative cursor-pointer group text-black" @click="cartCount++">
          <i class="fas fa-shopping-cart text-xl"></i>
          <span class="absolute -top-3 -right-3 bg-[#FFEB00] text-black text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-black italic border-2 border-black shadow-[2px_2px_0px_rgba(0,0,0,1)]">{{ cartCount }}</span>
        </div>
        <button @click="isMenuOpen = true" class="group flex flex-col space-y-1.5 items-end cursor-pointer">
          <div class="w-10 h-1.5 bg-black group-hover:bg-[#FFEB00] transition-all"></div>
          <div class="w-6 h-1.5 bg-black group-hover:bg-[#FFEB00] transition-all"></div>
          <div class="w-12 h-1.5 bg-black group-hover:bg-[#FFEB00] transition-all"></div>
        </button>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-screen flex flex-col justify-center items-center relative px-6 reveal bg-[#F9F9F9]">
      <div class="text-center z-10 transition-all duration-1000 transform scale-90 opacity-0 active-reveal">
        <div class="inline-block bg-black text-[#FFEB00] px-4 py-1 font-black text-[10px] tracking-[0.3em] uppercase mb-8 skew-x-[-15deg]">
          Visual Supply & Design Co.
        </div>
        
        <div class="relative w-[320px] md:w-[600px] aspect-video mx-auto mb-10 border-[14px] border-black rounded-[2.5rem] overflow-hidden shadow-[20px_20px_0px_rgba(255,235,0,1)] skew-y-[-1deg]">
          <transition-group name="tv-switch">
            <div v-for="(img, i) in tvImages" :key="i" v-show="activeTvIndex === i" class="absolute inset-0">
              <img :src="img" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700">
              <div class="absolute inset-0 pointer-events-none bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.05)_50%)] opacity-30"></div>
            </div>
          </transition-group>
        </div>

        <h1 class="text-[12vw] md:text-[14vw] font-[1000] leading-[0.8] tracking-[-0.05em] italic uppercase text-black">
          DESIGN THAT<br><span class="text-[#FFEB00] drop-shadow-[6px_6px_0px_rgba(0,0,0,1)]">SMOKES.</span>
        </h1>
        <p class="mt-10 max-w-2xl mx-auto text-lg md:text-xl text-stone-600 font-medium italic">
          "Injecting heart in commercial art. We are creative allies for bold brands."
        </p>
      </div>
      
      <div class="absolute bottom-0 w-full bg-[#FFEB00] py-4 overflow-hidden border-y-4 border-black z-20">
        <div class="flex marquee-fast">
          <span v-for="n in 10" :key="n" class="text-black font-black italic uppercase px-12 whitespace-nowrap text-xl">
            Identity • Illustration • Packaging • Motion • SupplayBox Studio • 
          </span>
        </div>
      </div>
    </section>

    <!-- OUR FOCUS SECTION - DENGAN STACKING CARDS ALA BRETHREN -->
    <section ref="focusSection" class="relative bg-white focus-trigger-section">
      <!-- Sticky Container - MENGGUNAKAN CLASS KHUSUS agar tidak bentrok dengan navbar -->
      <div class="sticky-focus top-0 z-20 bg-white min-h-screen overflow-hidden">
        <div class="h-screen flex flex-col">
          <!-- Sticky Header -->
          <div class="flex-shrink-0 pt-16 pb-4 px-6">
            <div class="flex items-center space-x-4">
              <h2 class="text-5xl md:text-7xl font-black italic uppercase tracking-tighter">
                OUR <span class="bg-[#FFEB00] px-4">FOCUS</span>
              </h2>
              <div class="h-1 flex-grow bg-black"></div>
            </div>
          </div>

          <!-- Cards Container -->
          <div class="flex-grow relative mt-4 cards-container" ref="cardsContainer">
            <!-- Background Grid -->
            <div class="absolute inset-0 opacity-5 pointer-events-none" 
                style="background-image: radial-gradient(black 1px, transparent 1px); background-size: 30px 30px;">
            </div>

            <!-- Stacking Cards -->
            <div class="relative h-full px-6">
              <div v-for="(card, index) in focusCards" 
                  :key="index" 
                  class="stack-card absolute left-6 right-6 cursor-pointer"
                  :ref="el => setCardRef(el, index)"
                  :style="getBrethrenCardStyle(index)"
                  @mouseenter="hoveredCard = index"
                  @mouseleave="hoveredCard = null">
                
                <div class="card-content bg-white border-4 border-black rounded-2xl p-6 shadow-[15px_15px_0px_rgba(0,0,0,1)] transition-all duration-500"
                    :class="{ 
                      'shadow-[20px_20px_0px_rgba(255,235,0,1)] -translate-y-2': hoveredCard === index,
                      'card-depth-1': index === 0,
                      'card-depth-2': index === 1,
                      'card-depth-3': index === 2,
                      'card-depth-4': index === 3
                    }">
                  
                  <!-- Card Number Badge -->
                  <div class="absolute -top-4 -left-4 w-12 h-12 bg-[#FFEB00] border-4 border-black rounded-full flex items-center justify-center text-2xl font-black shadow-[3px_3px_0px_rgba(0,0,0,1)] transform -rotate-6">
                    {{ index + 1 }}
                  </div>
                  
                  <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-full md:w-1/2 space-y-4">
                      <h3 class="text-4xl md:text-6xl font-[1000] uppercase italic tracking-tighter border-b-4 border-black pb-3">
                        {{ card.title }}
                      </h3>
                      <ul class="space-y-2">
                        <li v-for="(point, i) in card.points" :key="i" 
                            class="text-base md:text-lg font-bold italic uppercase flex items-start">
                          <span class="w-5 text-center mr-2 text-[#FFEB00] font-black text-xl">
                            {{ card.icons ? card.icons[i] : '◇' }}
                          </span>
                          <span>{{ point }}</span>
                        </li>
                      </ul>
                    </div>
                    
                    <div class="w-full md:w-1/2 aspect-video rounded-xl overflow-hidden border-2 border-black">
                      <img :src="card.img" 
                           class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700"
                           :alt="card.title">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Progress Indicator -->
          <div class="flex-shrink-0 pb-6 text-center">
            <div class="flex space-x-2 justify-center mb-1">
              <div v-for="n in focusCards.length" :key="n" 
                  class="w-1.5 h-1.5 rounded-full transition-all duration-500"
                  :class="{
                    'bg-black scale-125': n <= visibleCards,
                    'bg-black/20': n > visibleCards
                  }"></div>
            </div>
            <div class="text-[10px] font-black italic tracking-widest text-black/30">
              {{ visibleCards < focusCards.length ? 'SCROLL TO REVEAL NEXT CARD' : 'ALL CARDS REVEALED' }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Products Section -->
    <section class="py-32 px-6 bg-white relative z-10">
      <div class="container mx-auto">
        <div class="flex items-center space-x-6 mb-20 reveal-left">
          <h2 class="text-6xl md:text-8xl font-black italic uppercase tracking-tighter text-black">
            THE <span class="bg-[#FFEB00] px-4">SUPPLY</span>
          </h2>
          <div class="h-2 flex-grow bg-black"></div>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
          <div v-for="(prod, i) in products" :key="i" 
               class="product-card reveal-up group relative bg-[#F3F3F3] border-4 border-black p-6 cursor-pointer overflow-hidden transition-all duration-500 shadow-[8px_8px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1"
               :style="{ transitionDelay: `${i * 150}ms` }"
               @click="cartCount++">
            <div class="aspect-square overflow-hidden mb-8 bg-black border-2 border-black">
              <img :src="prod.img" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700" :alt="prod.name">
            </div>
            <div class="flex justify-between items-end">
              <h3 class="text-2xl font-black italic uppercase tracking-tight text-black">{{ prod.name }}</h3>
              <span class="text-2xl font-bold bg-[#FFEB00] border-2 border-black px-2 shadow-[3px_3px_0px_rgba(0,0,0,1)]">{{ prod.price }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Marquee -->
    <section class="py-40 bg-black overflow-hidden reveal">
      <div class="flex marquee-testi">
        <div v-for="n in 2" :key="n" class="flex">
          <div v-for="(testi, i) in testimonials" :key="i" class="flex-shrink-0 w-[500px] md:w-[700px] px-16 border-r-2 border-white/20">
            <div class="text-[#FFEB00] font-black text-4xl md:text-6xl italic uppercase leading-[1.1] mb-8">"{{ testi.quote }}"</div>
            <div class="font-black text-white uppercase tracking-widest text-sm">— {{ testi.name }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Collaborations Section -->
    <section class="py-40 px-6 bg-white reveal-up">
      <div class="container mx-auto">
        <div class="flex items-center space-x-6 mb-20">
          <h2 class="text-6xl md:text-8xl font-black italic uppercase tracking-tighter text-black">
            COLLAB <span class="text-[#FFEB00] drop-shadow-[4px_4px_0px_rgba(0,0,0,1)]">DROPS</span>
          </h2>
          <div class="h-2 flex-grow bg-black"></div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
          <div v-for="(collab, i) in collabs" :key="i"
            class="group relative bg-white border-4 border-black p-8 overflow-hidden cursor-pointer transition-all duration-500 shadow-[12px_12px_0px_rgba(255,235,0,1)] hover:shadow-none hover:translate-x-2 hover:translate-y-2">
            
            <div class="absolute inset-0 bg-black translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>

            <div class="relative z-10">
              <div class="aspect-square bg-gray-100 mb-8 overflow-hidden border-2 border-black">
                <img :src="collab.img" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-700" :alt="collab.name">
              </div>

              <h3 class="text-3xl font-black italic uppercase mb-2 text-black group-hover:text-[#FFEB00]">
                {{ collab.name }}
              </h3>
              <p class="text-stone-500 group-hover:text-white italic mb-4">
                with {{ collab.partner }}
              </p>

              <span class="inline-block mt-4 bg-black text-[#FFEB00] px-6 py-2 text-sm font-black italic uppercase border-2 border-[#FFEB00] group-hover:bg-[#FFEB00] group-hover:text-black transition-colors">
                View Drop →
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Brands Marquee -->
    <section class="py-24 bg-[#F9F9F9] border-y-4 border-black overflow-hidden">
      <div class="flex marquee-brands">
        <div v-for="n in 2" :key="n" class="flex">
          <div v-for="(brand, i) in brands" :key="i"
            class="flex items-center justify-center w-[220px] h-[100px] mx-12 grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition-all">
            <img :src="brand" class="max-h-24 object-contain" :alt="'Brand ' + i">
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white text-black px-6 py-32 border-t-8 border-black">
      <div class="container mx-auto grid md:grid-cols-2 gap-20 items-center">
        
        <div>
          <h2 class="text-7xl md:text-9xl font-black italic uppercase tracking-tighter mb-6 leading-none">
            LET'S<br><span class="text-[#FFEB00] drop-shadow-[8px_8px_0px_rgba(0,0,0,1)]">WORK</span>
          </h2>
          <p class="text-stone-600 italic max-w-md mb-10 text-xl">
            Have a project, a vision, or just want to make noise together?
          </p>

          <button class="bg-black text-[#FFEB00] px-12 py-5 font-[1000] italic uppercase border-4 border-black hover:bg-[#FFEB00] hover:text-black transition-all shadow-[10px_10px_0px_rgba(0,0,0,0.2)] hover:shadow-none active:translate-y-2">
            Start a Project →
          </button>
        </div>

        <div class="space-y-6 text-right">
          <div class="text-4xl font-black italic uppercase">
            SUPPLAY<span class="text-[#FFEB00] drop-shadow-[2px_2px_0px_rgba(0,0,0,1)]">BOX</span>
          </div>
          <p class="text-sm uppercase tracking-[0.4em] font-bold text-black/40">
            Visual Supply & Design Co.
          </p>

          <div class="flex justify-end space-x-8 text-2xl pt-4">
            <i class="fab fa-instagram hover:text-[#FFEB00] hover:scale-125 transition-all cursor-pointer"></i>
            <i class="fab fa-behance hover:text-[#FFEB00] hover:scale-125 transition-all cursor-pointer"></i>
            <i class="fab fa-dribbble hover:text-[#FFEB00] hover:scale-125 transition-all cursor-pointer"></i>
          </div>

          <p class="text-xs font-black text-black/30 mt-10 tracking-widest">
            © {{ new Date().getFullYear() }} SUPPLAYBOX STUDIO — ALL RIGHTS RESERVED
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

// Reactive State
const cartCount = ref(1)
const isLoading = ref(true)
const isMenuOpen = ref(false)
const activeTvIndex = ref(0)
const hoveredCard = ref(null)
const visibleCards = ref(1)
const showScrollButton = ref(false)
const scrollProgress = ref(0)

// Refs untuk DOM elements
const focusSection = ref(null)
const cardsContainer = ref(null)
const cardRefs = ref([])

// Data Arrays
const tvImages = [
  'https://images.unsplash.com/photo-1558478551-1a378f63ad28?auto=format&fit=crop&q=80&w=1200',
  'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=1200',
  'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&q=80&w=1200'
]

const focusCards = [
  { 
    title: 'ILLUSTRATION', 
    points: ['All Custom', 'Sketch to Vector'],
    icons: ['◇', '◇'],
    img: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800' 
  },
  { 
    title: 'BRANDING', 
    points: [
      'Naming',
      'Brand + Creative Strategy',
      'Verbal Identity + Copy Writing',
      'Placemaking + Environmental'
    ],
    icons: ['◆', '◆', '◆', '◆'],
    img: 'https://images.unsplash.com/photo-1558478551-1a378f63ad28?w=800' 
  },
  { 
    title: 'TYPOGRAPHY', 
    points: [
      'Custom Type Design',
      'Variable Fonts',
      'Kinetic Typography',
      'Letterpress'
    ],
    icons: ['●', '●', '●', '●'],
    img: 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=800' 
  },
  { 
    title: 'PACKAGING', 
    points: [
      'All Strengths Combined',
      'Readability + Hierarchy',
      'Standout on Shelf',
      'Rules Respected',
      'Rules Bent',
      'Impulse to Own'
    ],
    icons: ['■', '■', '■', '■', '■', '■'],
    img: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=800' 
  }
]

const products = [
  { name: 'Industrial Type', price: '$29', img: 'https://images.unsplash.com/photo-1558478551-1a378f63ad28?auto=format&fit=crop&q=80&w=800' },
  { name: 'Vantage Texture', price: '$15', img: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800' },
  { name: 'Hazard Poster Kit', price: '$42', img: 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&q=80&w=800' }
]

const testimonials = [
  { name: 'Alex Rivera', quote: 'SupplayBox didn’t just design a logo, they gave us an identity that bites.' },
  { name: 'Marcus Volkov', quote: 'Original, gritty, and incredibly fast. They are our go-to for visual noise.' }
]

const collabs = [
  { name: 'Hazard Type Pack', partner: 'Ironclad Studio', img: 'https://images.unsplash.com/photo-1520975922284-8b456906c813?auto=format&fit=crop&q=80&w=800' },
  { name: 'Concrete Poster Kit', partner: 'Void Dept', img: 'https://images.unsplash.com/photo-1545235617-9465d2a55698?auto=format&fit=crop&q=80&w=800' },
  { name: 'Industrial UI Set', partner: 'Black Noise', img: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800' }
]

const brands = [
  'https://images.seeklogo.com/logo-png/9/1/nike-logo-png_seeklogo-99478.png',
  'https://i.pinimg.com/736x/fb/ec/07/fbec070d0a5339285b6e5c9f836b1a96.jpg',
  'https://images.seeklogo.com/logo-png/9/1/new-balance-logo-png_seeklogo-98723.png',
  'https://images.seeklogo.com/logo-png/28/1/quiksilver-logo-png_seeklogo-285560.png',
  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSjvMo6qv6VPAwn3cBV10Ct3rMrzZEtBMlf-A&s'
]

// Helper Functions
const setCardRef = (el, index) => {
  if (el) cardRefs.value[index] = el
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const openOrderNow = () => {
  window.location.href = 'https://wa.me/6281234567890?text=Halo%20SupplayBox%2C%20saya%20ingin%20order'
}

// Core Function: Brethren-style card positioning with top property
const getBrethrenCardStyle = (index) => {
  const baseOffset = 40 // Base offset per card (40px)
  
  // Card not yet visible
  if (index >= visibleCards.value) {
    return {
      top: '100%', // Position off-screen using percentage
      zIndex: 50 + index,
      opacity: 0,
      pointerEvents: 'none',
      transition: 'all 0.8s cubic-bezier(0.22, 1, 0.36, 1)'
    }
  }
  
  // Calculate top position with parallax effect
  let topPosition = index * baseOffset
  
  // Add parallax effect based on scroll progress
  if (cardsContainer.value && index > 0) {
    const containerRect = cardsContainer.value.getBoundingClientRect()
    const progress = Math.max(0, Math.min(1, -containerRect.top / (window.innerHeight * 2)))
    
    // Parallax speed varies by card depth
    const parallaxSpeed = 0.2 + (index * 0.3)
    const parallaxOffset = progress * 150 * parallaxSpeed
    
    topPosition = (index * baseOffset) + parallaxOffset
  }
  
  // Limit top position to prevent cards from going too far
  const maxTop = window.innerHeight * 0.6
  topPosition = Math.min(topPosition, maxTop)
  
  return {
    top: `${topPosition}px`,
    zIndex: 50 + index,
    transform: `scale(${1 + (index * 0.005)})`,
    opacity: 1,
    transition: 'top 0.4s cubic-bezier(0.33, 1, 0.68, 1), opacity 0.5s ease',
    pointerEvents: 'auto'
  }
}

// Scroll Handler - DIPERBARUI
const handleScroll = () => {
  const scrollY = window.scrollY
  showScrollButton.value = scrollY > 500
  
  if (focusSection.value) {
    const rect = focusSection.value.getBoundingClientRect()
    const sectionTop = rect.top
    const sectionHeight = focusSection.value.offsetHeight
    const windowHeight = window.innerHeight
    
    // Hitung progress scroll di dalam section (0 - 1)
    const progress = Math.max(0, Math.min(1, -sectionTop / (sectionHeight - windowHeight)))
    scrollProgress.value = progress
    
    // Threshold berbasis persentase viewport - DISESUAIKAN
    const threshold1 = windowHeight * 0.12  // Lebih ketat
    const threshold2 = windowHeight * 0.28  // Lebih ketat
    const threshold3 = windowHeight * 0.44  // Lebih ketat
    
    const scrollDistance = -sectionTop
    
    if (scrollDistance < threshold1) {
      visibleCards.value = 1
    } else if (scrollDistance < threshold2) {
      visibleCards.value = 2
    } else if (scrollDistance < threshold3) {
      visibleCards.value = 3
    } else {
      visibleCards.value = 4
    }
  }
}

// Lifecycle Hooks
onMounted(() => {
  // Loading screen
  setTimeout(() => { isLoading.value = false }, 2500)
  
  // TV Gallery rotation
  setInterval(() => { 
    activeTvIndex.value = (activeTvIndex.value + 1) % tvImages.length 
  }, 3500)

  // Intersection Observer for reveal animations
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) entry.target.classList.add('active-reveal')
    })
  }, { threshold: 0.15 })

  document.querySelectorAll('.reveal, .reveal-up, .reveal-left').forEach(el => observer.observe(el))
  
  // Scroll listener
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;900&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

.portfolio-page {
  font-family: 'Space Grotesk', sans-serif;
}

/* ===== TRANSITIONS & ANIMATIONS ===== */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scale(0.8);
}

.tv-switch-enter-active,
.tv-switch-leave-active {
  transition: all 0.6s cubic-bezier(0.8, 0, 0.2, 1);
}
.tv-switch-enter-from {
  opacity: 0;
  transform: scale(1.1) rotate(2deg);
  filter: brightness(1.5);
}
.tv-switch-leave-to {
  opacity: 0;
  transform: scale(0.9) rotate(-2deg);
}

.slide-menu-enter-active,
.slide-menu-leave-active {
  transition: transform 0.8s cubic-bezier(0.85, 0, 0.15, 1);
}
.slide-menu-enter-from,
.slide-menu-leave-to {
  transform: translateX(-100%);
}

/* ===== LOADING SCREEN ===== */
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
  background: #fff8ab;
  display: flex;
  justify-content: center;
  align-items: center;
}
.rolling-door-leave-active {
  transition: transform 1.2s cubic-bezier(0.85, 0, 0.15, 1);
}
.rolling-door-leave-to {
  transform: translateY(100%);
}
.animate-progress {
  width: 0%;
  animation: progress-fill 2s ease-in-out forwards;
}
@keyframes progress-fill {
  to { width: 100%; }
}

/* ===== SCROLL REVEALS ===== */
.reveal,
.reveal-up,
.reveal-left {
  opacity: 0;
  transition: all 1s cubic-bezier(0.2, 1, 0.3, 1);
}
.reveal {
  transform: scale(0.95);
}
.reveal-up {
  transform: translateY(60px);
}
.reveal-left {
  transform: translateX(-60px);
}
.active-reveal {
  opacity: 1 !important;
  transform: translate(0) scale(1) !important;
}

/* ===== MARQUEES ===== */
@keyframes scroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
.marquee-fast {
  animation: scroll 15s linear infinite;
}
.marquee-testi {
  animation: scroll 35s linear infinite;
}
.marquee-brands {
  animation: scroll 25s linear infinite;
}

/* ===== FOCUS SECTION ===== */
.focus-trigger-section {
  position: relative;
  height: 350vh; /* Turun dari 400vh */
  background-color: white;
  z-index: 10;
}

/* Sticky Container untuk Focus Section - PAKAI CLASS KHUSUS */
.sticky-focus {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
  /* height: 100vh; */
  z-index: 20;
  background-color: white;
  overflow: hidden;
}

/* Container untuk konten di dalam sticky-focus */
.sticky-focus > div {
  /* height: 100vh; */
  display: flex;
  flex-direction: column;
}

/* Cards Container */
.cards-container {
  position: relative;
  flex: 1;
  min-height: 0;
  height: calc(100vh - 200px);
  overflow: visible;
}

/* ===== STACKING CARDS ===== */
.stack-card {
  position: absolute;
  left: 1.5rem;
  right: 1.5rem;
  will-change: top, transform, opacity;
  max-height: 80vh;
}

.card-content {
  background: white;
  border: 4px solid black;
  border-radius: 1.5rem;
  padding: 2.5rem;
  box-shadow: 0px 10px 30px rgba(0,0,0,0.1), 15px 15px 0px rgba(0,0,0,1);
  max-height: 70vh;
  overflow-y: auto;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Custom scrollbar */
.card-content::-webkit-scrollbar {
  width: 6px;
}
.card-content::-webkit-scrollbar-track {
  background: #f0f0f0;
  border-radius: 10px;
}
.card-content::-webkit-scrollbar-thumb {
  background: #FFEB00;
  border-radius: 10px;
  border: 2px solid black;
}

/* Card depth variations */
.card-depth-1 {
  background: white;
}
.card-depth-2 {
  background: #fafafa;
}
.card-depth-3 {
  background: #f5f5f5;
}
.card-depth-4 {
  background: #f0f0f0;
}

/* Hover effect */
.stack-card:hover .card-content {
  transform: translateY(-5px);
  box-shadow: 0px 15px 40px rgba(0,0,0,0.15), 20px 20px 0px #FFEB00;
}

/* ===== PRODUCT CARDS ===== */
.product-card:hover {
  border-color: #FFEB00;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .focus-trigger-section {
    height: 500vh; /* Mobile tetap lebih besar karena layout berbeda */
  }
  
  .card-content {
    padding: 1rem;
    max-height: 65vh;
  }
  
  .stack-card {
    left: 1rem;
    right: 1rem;
  }
  
  .fixed.bottom-8.right-8,
  .fixed.bottom-8.left-8 {
    bottom: 1rem;
  }
  
  .fixed.bottom-8.right-8 {
    right: 1rem;
  }
  
  .fixed.bottom-8.left-8 {
    left: 1rem;
  }
  
  .stack-card .text-4xl {
    font-size: 1.5rem;
  }
  
  .stack-card .text-base {
    font-size: 0.875rem;
  }
}

/* ===== NAVBAR - TETAP SEPERTI ASLINYA ===== */
nav {
  position: sticky;
  top: 0;
  z-index: 50;
}
</style>