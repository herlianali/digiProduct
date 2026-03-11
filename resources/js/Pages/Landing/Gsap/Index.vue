<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import BannerCards from './Components/BannerCards.vue'
import TestimonialSection from './Components/TestimonialSection.vue'
import OurWorkSection from './Components/OurWorkSection.vue'
import ProductSection from './Components/ProductSection.vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ShoppingCartIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
// import { ChevronRightIcon } from '@heroicons/vue/28/outline'

gsap.registerPlugin(ScrollTrigger)

const bannerRef           = ref(null)
const navbarRef           = ref(null)
const navbarContainerRef  = ref(null)
const isNavbarSticky      = ref(false)
let scrollTriggerInstance = null

onMounted(() => {
  initNavbarAnimation()
  initSectionAnimations()
})

onUnmounted(() => {
  if (scrollTriggerInstance) scrollTriggerInstance.kill()
  ScrollTrigger.getAll().forEach(t => t.kill())
})

const initNavbarAnimation = () => {
  scrollTriggerInstance = ScrollTrigger.create({
    trigger: document.body,
    start: 'top top',
    end: 'bottom bottom',
    onUpdate: (self) => {
      const scrollY = self.scroll()

      if (scrollY > 100) {
        if (!isNavbarSticky.value) {
          isNavbarSticky.value = true

          gsap.set(navbarContainerRef.value, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            left: 'auto',
            // PENTING: width auto agar konten menentukan lebar
            width: 'auto',
            maxWidth: 'calc(100vw - 40px)',
            overflow: 'visible',
          })

          gsap.to(navbarContainerRef.value, {
            borderRadius: '50px',
            boxShadow: '0 10px 30px -5px rgba(0, 0, 0, 0.2)',
            backgroundColor: 'rgba(255, 255, 255, 0.98)',
            backdropFilter: 'blur(10px)',
            padding: '0 20px',
            border: '1px solid rgba(255, 255, 255, 0.2)',
            duration: 0.4,
            ease: 'power2.out',
          })

          gsap.to('.nav-button', { scale: 0.9, duration: 0.3 })
        }
      } else {
        if (isNavbarSticky.value) {
          gsap.to(navbarContainerRef.value, {
            position: 'relative',
            top: 'auto',
            right: 'auto',
            left: 0,
            width: '100%',
            maxWidth: '100%',
            borderRadius: '0',
            boxShadow: 'none',
            backgroundColor: 'white',
            backdropFilter: 'none',
            padding: '0',
            border: 'none',
            overflow: 'visible',
            duration: 0.4,
            ease: 'power2.out',
            onComplete: () => {
              isNavbarSticky.value = false
              gsap.set(navbarContainerRef.value, { clearProps: 'all' })
            }
          })

          gsap.to('.nav-button', { scale: 1, duration: 0.3 })
        }
      }
    }
  })
}

const initSectionAnimations = () => {
  gsap.from('.section-title', {
    scrollTrigger: {
      trigger: '.section-title',
      start: 'top 80%',
      toggleActions: 'play none none reverse'
    },
    y: 50,
    opacity: 0,
    duration: 1,
    stagger: 0.2
  })
}

const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    const offset = isNavbarSticky.value ? 100 : 0
    const elementPosition = element.getBoundingClientRect().top + window.scrollY
    window.scrollTo({ top: elementPosition - offset, behavior: 'smooth' })
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">

    <section class="relative bg-gradient-to-r from-indigo-500 to-purple-600 text-white w-full">

      <!-- Navbar Container -->
      <div ref="navbarContainerRef" class="navbar-container">
        <nav ref="navbarRef" class="w-full">
          <div class="navbar-content">

            <!-- Navigation Links -->
            <div class="nav-links">
              <a href="#" class="nav-item" @click.prevent="scrollToSection('about-us')">About Us</a>
              <a href="#" class="nav-item" @click.prevent="scrollToSection('platform')">Platform</a>
              <a href="#" class="nav-item" @click.prevent="scrollToSection('shop')">Shop</a>
              <a href="#" class="nav-item" @click.prevent="scrollToSection('contact')">Contact</a>
            </div>

            <!-- Right Side -->
            <div class="nav-right">
                <button class="bg-green-500 hover:bg-green-700 font-bold text-black px-4 py-1 rounded-full nav-button">
                    FREE SKETCH!
                    <ChevronRightIcon class="w-5 h-5 font-bold text-black inline-block ml-2" />
                </button>
                <ShoppingCartIcon class="w-6 h-6 text-green-700 cursor-pointer" />
                <span class="navbar-price border border-gray-600 rounded-full px-6 py-1">$100</span>

              <button class="mobile-menu-btn md:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
            </div>

          </div>
        </nav>
      </div>

      <!-- Hero Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20">
        <h1 class="text-5xl md:text-6xl font-bold mb-4">Welcome to SUPPLAYBOX</h1>
        <p class="text-xl text-indigo-100 mb-8">Hi Robert,</p>
        <p class="text-lg max-w-2xl mx-auto">
          Most people freeze at the starting line. Don't hesitate — just give it a shot.
        </p>
        <button class="mt-8 bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold
                       hover:bg-indigo-50 transition transform hover:scale-105">
          Get Started
        </button>
      </div>
    </section>

    <div ref="bannerRef"><BannerCards /></div>

    <main>
      <TestimonialSection />
      <OurWorkSection />
      <ProductSection />
    </main>

    <footer class="bg-gray-900 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-xl font-bold mb-4">SUPLAYBOX</h3>
            <p class="text-gray-400">Creative design and illustration services for your brand.</p>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Quick Links</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white transition">About Us</a></li>
              <li><a href="#" class="hover:text-white transition">Platform</a></li>
              <li><a href="#" class="hover:text-white transition">Shop</a></li>
              <li><a href="#" class="hover:text-white transition">Contact</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Services</h4>
            <ul class="space-y-2 text-gray-400">
              <li>Brand Identity</li><li>Logo Design</li>
              <li>Illustration</li><li>Packaging Design</li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Connect</h4>
            <p class="text-gray-400 mb-4">Let's CONNECT on Pinterest</p>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
              Follow Us
            </button>
          </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2024 SUPPLAYBOX. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.nav-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: 0.75rem;
    flex-shrink: 0;
}
</style>
