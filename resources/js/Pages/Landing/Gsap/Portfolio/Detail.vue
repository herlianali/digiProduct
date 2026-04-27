<script setup>
import NavbarFloating from '../ComponentsV2/NavbarFloating.vue'
import FooterSection from '../ComponentsV2/FooterSection.vue'
import LoadingScreen from '../ComponentsV2/LoadingScreen.vue'
</script>

<template>
  <LoadingScreen />

  <div class="bg-white min-h-screen flex flex-col font-sans">

    <!-- ── HEADER / NAVBAR ── -->
    <div class="bg-white border-b border-black/10 px-8 py-4 sticky top-0 z-50">
      <div class="flex items-center justify-between max-w-[1200px] mx-auto">

        <!-- Logo kiri -->
        <div class="flex items-center gap-3">
          <div class="w-full rounded-full border-2 border-black flex items-center justify-center shrink-0">
            <img src="/public/assets/image/our-work.svg" alt="">
          </div>
        </div>

        <NavbarFloating
          static-mode
          balance="$100"
          avatar-url=""
          @nav-click="(id) => console.log('navigated to', id)"
          @cart-click="() => console.log('cart clicked')"
          @cta-click="() => console.log('free sketch clicked')"
        />
      </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <main class="flex-1 max-w-[1200px] mx-auto w-full px-8 py-10">

      <!-- Breadcrumb -->
      <p class="text-black/40 text-xs mb-6 tracking-wide">
        <span v-for="(crumb, i) in breadcrumbs" :key="i">
          <span
            class="hover:text-black cursor-pointer transition-colors"
            @click="console.log('nav to', crumb)"
          >{{ crumb }}</span>
          <span v-if="i < breadcrumbs.length - 1" class="mx-1">/</span>
        </span>
      </p>

      <!-- Big Headline -->
      <h1 class="text-black font-black text-5xl leading-tight max-w-2xl mb-10">
        {{ project.headline }}
      </h1>

      <!-- Sub heading + 3 col description -->
      <div class="mb-10">
        <h3 class="text-black font-black text-base mb-2">{{ project.subheading }}</h3>
        <p class="text-black/70 text-sm leading-relaxed max-w-xl mb-6">
          {{ project.subDesc }}
        </p>
        <div class="grid grid-cols-3 gap-8">
          <p v-for="(col, i) in project.columns" :key="i" class="text-black/60 text-xs leading-relaxed">
            {{ col }}
          </p>
        </div>
      </div>

      <!-- ── IMAGE GRID 2 col ── -->
      <div class="grid grid-cols-2 gap-0 mb-16">
        <div
          v-for="(img, i) in project.images"
          :key="i"
          class="overflow-hidden aspect-square bg-gray-100 cursor-pointer group"
          @click="openLightbox(i)"
        >
          <img
            :src="img.src"
            :alt="img.alt"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
        </div>
      </div>

      <!-- ── FEATURED SECTION ── -->
      <section class="mb-16">
        <h3 class="text-black font-black text-lg mb-6">Featured</h3>

        <div class="relative">
          <!-- Prev -->
          <button
            class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-5 z-10 w-9 h-9 rounded-full bg-black text-white flex items-center justify-center hover:bg-black/70 transition-colors"
            @click="prevFeatured"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="15 18 9 12 15 6"/>
            </svg>
          </button>

          <!-- Cards -->
          <div class="grid grid-cols-3 gap-6 px-6">
            <div
              v-for="(item, i) in visibleFeatured"
              :key="i"
              class="flex flex-col cursor-pointer group"
            >
              <div class="aspect-[4/3] overflow-hidden rounded-lg bg-gray-100 mb-3">
                <img
                  :src="item.src"
                  :alt="item.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
              </div>
              <p class="text-black font-black text-sm">{{ item.title }}</p>
            </div>
          </div>

          <!-- Next -->
          <button
            class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-5 z-10 w-9 h-9 rounded-full bg-black text-white flex items-center justify-center hover:bg-black/70 transition-colors"
            @click="nextFeatured"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </button>
        </div>
      </section>

    </main>

    <!-- ── FOOTER ── -->
    <FooterSection />

    <!-- ── LIGHTBOX ── -->
    <transition name="fade">
      <div
        v-if="lightboxIndex !== null"
        class="fixed inset-0 bg-black/90 z-[999] flex items-center justify-center"
        @click.self="closeLightbox"
      >
        <button
          class="absolute top-5 right-6 text-white text-3xl font-black hover:text-white/60"
          @click="closeLightbox"
        >✕</button>

        <button
          class="absolute left-6 text-white text-3xl font-black hover:text-white/60"
          @click="prevLight"
        >‹</button>

        <img
          :src="project.images[lightboxIndex].src"
          :alt="project.images[lightboxIndex].alt"
          class="max-h-[85vh] max-w-[85vw] object-contain rounded-xl"
        />

        <button
          class="absolute right-6 text-white text-3xl font-black hover:text-white/60"
          @click="nextLight"
        >›</button>
      </div>
    </transition>

  </div>
</template>

<script>
export default {
  name: 'PortfolioDetail',

  data() {
    return {
      lightboxIndex: null,
      featuredOffset: 0,

      breadcrumbs: ['Logo', 'Lettering', 'Illustration', 'Packaging', 'Poster'],

      project: {
        headline: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        subheading: 'Lorem ipsum dolor',
        subDesc: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        columns: [
          'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.',
          'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.',
          'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.',
        ],
        images: [
          { src: '/assets/image/portfolio/img_01.png', alt: 'Pizza Punch artwork' },
          { src: '/assets/image/portfolio/img_02.png', alt: 'Happy Christmas Eve' },
          { src: '/assets/image/portfolio/img_03.png', alt: 'Screen Printing' },
          { src: '/assets/image/portfolio/img_04.png', alt: 'Christmas chaos' },
          { src: '/assets/image/portfolio/img_05.png', alt: 'Pizza Punch full' },
          { src: '/assets/image/portfolio/img_06.png', alt: 'Pizza Punch yellow' },
        ],
      },

      featuredItems: [
        { src: '/assets/image/portfolio/featured_01.png', title: 'Lorem ipsum dolor' },
        { src: '/assets/image/portfolio/featured_02.png', title: 'Lorem ipsum dolor' },
        { src: '/assets/image/portfolio/featured_03.png', title: 'Lorem ipsum dolor' },
        { src: '/assets/image/portfolio/featured_04.png', title: 'Lorem ipsum dolor' },
        { src: '/assets/image/portfolio/featured_05.png', title: 'Lorem ipsum dolor' },
      ],
    }
  },

  computed: {
    visibleFeatured() {
      const items = this.featuredItems
      const len = items.length
      return [0, 1, 2].map(i => items[(this.featuredOffset + i) % len])
    },
  },

  methods: {
    openLightbox(i) {
      this.lightboxIndex = i
    },
    closeLightbox() {
      this.lightboxIndex = null
    },
    prevLight() {
      const len = this.project.images.length
      this.lightboxIndex = (this.lightboxIndex - 1 + len) % len
    },
    nextLight() {
      const len = this.project.images.length
      this.lightboxIndex = (this.lightboxIndex + 1) % len
    },
    prevFeatured() {
      const len = this.featuredItems.length
      this.featuredOffset = (this.featuredOffset - 1 + len) % len
    },
    nextFeatured() {
      this.featuredOffset = (this.featuredOffset + 1) % this.featuredItems.length
    },
  },
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>