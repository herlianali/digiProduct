<template>
  <LoadingScreen />
  <div class="bg-[#0d0d0d] min-h-screen flex flex-col font-sans">

    <!-- ── HEADER ── -->
    <div class="bg-[#1a1a1a] rounded-b-2xl px-8 py-5">
      <div class="flex items-center justify-between">
        <h1 class="font-black text-white text-3xl tracking-widest uppercase">Supplaybox</h1>
        <NavbarFloating
          static-mode
          balance="$100"
          avatar-url=""
          @nav-click="(id) => console.log('navigated to', id)"
          @cart-click="() => console.log('cart clicked')"
          @cta-click="() => console.log('free sketch clicked')"
        />
      </div>

      <!-- ── PRODUCT DETAIL ── -->
      <main class="flex-1 px-10 py-10 max-w-[1200px] mx-auto w-full">
        <div class="grid grid-cols-[420px_1fr] gap-12 items-start">

          <!-- LEFT: Image + Thumbnail Tabs -->
          <div class="flex flex-col gap-0">
            <!-- Main Image -->
            <div class="rounded-t-xl overflow-hidden bg-[#111] aspect-square flex items-center justify-center">
              <img
                :src="activeImage"
                :alt="product.name"
                class="w-full h-full object-cover transition-all duration-300"
              />
            </div>

            <!-- Thumbnail Tabs — dari previews database -->
            <div
              v-if="product.previews && product.previews.length"
              :class="`grid grid-cols-${Math.min(product.previews.length, 4)} rounded-b-xl overflow-hidden border-t border-white/10`"
            >
              <button
                v-for="(preview, index) in product.previews.slice(0, 4)"
                :key="preview.id"
                @click="activeImage = preview.url; activeIndex = index"
                :class="[
                  'relative overflow-hidden py-0 px-0 text-center hover:brightness-110 transition-all',
                  activeIndex === index ? 'ring-2 ring-inset ring-[#4dfa03]' : '',
                  tabBg[index % tabBg.length],
                ]"
                style="aspect-ratio: 1/1;"
              >
                <!-- Thumbnail image -->
                <img
                  :src="preview.url"
                  :alt="preview.label || `Preview ${index + 1}`"
                  class="w-full h-full object-cover"
                />
                <!-- Label overlay -->
                <span
                  v-if="preview.label"
                  class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[9px] font-bold py-1 px-1 leading-tight text-center"
                >
                  {{ preview.label }}
                </span>
              </button>
            </div>

            <!-- Fallback tabs (jika tidak ada previews) -->
            <div v-else class="rounded-b-xl overflow-hidden border-t border-white/10 bg-[#111] py-4 text-center">
              <span class="text-white/30 text-xs">No preview images</span>
            </div>
          </div>

          <!-- RIGHT: Info -->
          <div class="flex flex-col gap-5 pt-2">
            <!-- Breadcrumb -->
            <p class="text-white/50 text-sm tracking-wide">
              Shop
              <span v-if="product.category"> / {{ product.category }}</span>
              <span v-if="product.style"> / {{ product.style }}</span>
            </p>

            <!-- Title -->
            <h2 class="text-white text-6xl font-black leading-tight tracking-tight">
              {{ product.name }}
            </h2>

            <!-- Price -->
            <p class="text-[#4dfa03] text-5xl font-black">
              {{ product.is_free ? 'Free' : product.price }}
            </p>

            <!-- Tags -->
            <div v-if="product.tags && product.tags.length" class="flex flex-wrap gap-2">
              <span
                v-for="tag in product.tags"
                :key="tag"
                class="text-[10px] font-bold uppercase tracking-widest border border-white/20 text-white/50 rounded-full px-3 py-1"
              >
                {{ tag }}
              </span>
            </div>

            <!-- Description -->
            <div class="text-white/70 text-sm leading-relaxed max-w-xl whitespace-pre-line">
              {{ product.description }}
            </div>

            <!-- File Formats — dari files database -->
            <div v-if="product.files && product.files.length" class="flex items-center gap-3 mt-1">
              <a
                v-for="file in product.files"
                :key="file.id"
                :href="file.url"
                target="_blank"
                class="flex flex-col items-center justify-center border border-white/30 rounded-md w-14 h-14 hover:border-[#4dfa03] transition-colors cursor-pointer"
                :title="`Download ${file.file_type}`"
              >
                <svg class="w-5 h-5 text-white/70 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <span class="text-white/60 text-[10px] font-bold">{{ file.file_type }}</span>
              </a>
            </div>

            <!-- Add to Cart -->
            <button
              class="mt-2 flex items-center gap-3 bg-[#f5d020] hover:bg-yellow-300 text-black font-black text-lg px-8 py-4 rounded-full w-fit transition-all active:scale-95 shadow-lg shadow-yellow-500/20"
            >
              {{ product.is_free ? 'Download Free' : 'Add to Cart' }}
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path
                  v-if="product.is_free"
                  stroke-linecap="round" stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"
                />
                <path
                  v-else
                  stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"
                />
              </svg>
            </button>
          </div>
        </div>
      </main>
    </div>

    <!-- ── FOOTER ── -->
    <FooterSection />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import NavbarFloating from '../ComponentsV2/NavbarFloating.vue'
import FooterSection from '../ComponentsV2/FooterSection.vue'
import LoadingScreen from '../ComponentsV2/LoadingScreen.vue'

const props = defineProps({
  product: {
    type: Object,
    default: () => ({
      name: '',
      description: '',
      price: '',
      is_free: false,
      style: null,
      category: null,
      tags: [],
      thumbnail: null,
      previews: [],
      files: [],
    }),
  },
})

const activeIndex = ref(0)

const activeImage = computed(() =>
  props.product?.previews?.length
    ? props.product.previews[activeIndex.value]?.url
    : (props.product?.thumbnail ?? 'https://placehold.co/420x420/111111/ffffff?text=No+Image')
)

const tabBg = ['bg-[#1a1a1a]', 'bg-[#1e1e1e]', 'bg-[#222]', 'bg-[#252525]']
</script>