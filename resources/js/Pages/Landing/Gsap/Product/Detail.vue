<script setup>
import NavbarFloating from '../ComponentsV2/NavbarFloating.vue'
import FooterSection from '../ComponentsV2/FooterSection.vue'
import LoadingScreen from '../ComponentsV2/LoadingScreen.vue'
</script>

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
                src="https://placehold.co/420x420/111111/ffffff?text=Artwork"
                alt="Autobiography artwork"
                class="w-full h-full object-cover"
              />
            </div>
  
            <!-- Thumbnail Tabs -->
            <div class="grid grid-cols-4 rounded-b-xl overflow-hidden border-t border-white/10">
              <button class="bg-[#f0f0f0] text-black text-xs font-semibold py-4 px-2 text-center hover:brightness-110 transition-all leading-tight">
                Mockup Real<br>Chat GPT
              </button>
              <button class="bg-[#c0392b] text-white text-xs font-semibold py-4 px-2 text-center hover:brightness-110 transition-all leading-tight">
                Mockup kaos<br>BG polos
              </button>
              <button class="bg-[#f1c40f] text-black text-xs font-semibold py-4 px-2 text-center hover:brightness-110 transition-all leading-tight">
                Depan<br>Belakang
              </button>
              <button class="bg-[#27ae60] text-white text-xs font-semibold py-4 px-2 text-center hover:brightness-110 transition-all leading-tight">
                Desain lain2
              </button>
            </div>
          </div>
  
          <!-- RIGHT: Info -->
          <div class="flex flex-col gap-5 pt-2">
            <!-- Breadcrumb -->
            <p class="text-white/50 text-sm tracking-wide">
              Shop / Artwork / Dark Art / Music
            </p>
  
            <!-- Title -->
            <h2 class="text-white text-6xl font-black leading-tight tracking-tight">
              Autobiography
            </h2>
  
            <!-- Price -->
            <p class="text-[#4dfa03] text-5xl font-black">$80</p>
  
            <!-- Description -->
            <p class="text-white/70 text-sm leading-relaxed max-w-xl">
              This artwork bursts with raw punk energy and rebellious street culture, featuring a chaotic crowd
              scene filled with bold characters, flames, and expressive movement. The powerful statement "All
              Criticism Is A Form of Autography" dominates the composition, transforming judgment into a symbol
              of individuality and self-expression. With graffiti-style typography, underground zine aesthetics,
              and iconic details like "Punk Not Dead" and the "Hello My Name Is Free!" sticker, the design
              captures the spirit of freedom, creativity, and counterculture—making it a striking statement piece
              for global apparel brands that celebrate bold identity and fearless attitude.
            </p>
  
            <!-- File Formats -->
            <div class="flex items-center gap-3 mt-1">
              <div v-for="fmt in ['PSD', 'JPEG', 'PNG']" :key="fmt"
                class="flex flex-col items-center justify-center border border-white/30 rounded-md w-14 h-14 hover:border-[#4dfa03] transition-colors cursor-pointer">
                <!-- simple file icon -->
                <svg class="w-5 h-5 text-white/70 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <span class="text-white/60 text-[10px] font-bold">{{ fmt }}</span>
              </div>
            </div>
  
            <!-- Add to Cart -->
            <button class="mt-2 flex items-center gap-3 bg-[#f5d020] hover:bg-yellow-300 text-black font-black text-lg px-8 py-4 rounded-full w-fit transition-all active:scale-95 shadow-lg shadow-yellow-500/20">
              Add to Cart
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
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

<script>
export default {
  data() {
    return {
      socialIcons: [
        {
          label: 'Instagram',
          svg: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>`
        },
        {
          label: 'TikTok',
          svg: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.73a8.28 8.28 0 004.85 1.56V6.84a4.85 4.85 0 01-1.08-.15z"/></svg>`
        },
        {
          label: 'Pinterest',
          svg: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>`
        },
        {
          label: 'Behance',
          svg: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 7h-7V5h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.964 1.782 5.375 4.426.078.506.109 1.188.095 2.14H15.97c.13 3.211 3.483 3.312 4.588 2.029H23.726zm-7.726-3h3.578c-.108-1.674-1.262-2.107-1.848-2.107-.78 0-1.56.467-1.73 2.107zM8 13.656c1.534.122 2.872.729 2.872 2.286 0 1.722-1.405 2.715-3.533 2.715H0V5h7.267c1.969 0 3.39.914 3.39 2.667 0 1.149-.576 1.957-1.657 2.378zM3 10.914h3.145c.97 0 1.667-.425 1.667-1.332 0-.862-.548-1.277-1.657-1.277H3v2.609zm0 4.91h3.445c.97 0 1.818-.423 1.818-1.441 0-1.018-.88-1.381-1.818-1.381H3v2.822z"/></svg>`
        },
        {
          label: 'X',
          svg: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.259 5.629L18.244 2.25zM17.083 20.027h1.833L7.084 4.126H5.117l11.966 15.901z"/></svg>`
        },
        {
          label: 'Threads',
          svg: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.284 3.272-.886 1.102-2.14 1.704-3.73 1.79-1.202.065-2.361-.218-3.259-.801-1.063-.689-1.685-1.74-1.752-2.964-.065-1.19.408-2.285 1.33-3.082.88-.76 2.119-1.207 3.583-1.291a13.853 13.853 0 012.087.068c-.188-.84-.187-1.55.002-2.1.198-.583.555-.99 1.12-1.252.533-.248 1.2-.356 2.033-.332.806.023 1.49.178 2.041.46.627.319 1.069.8 1.316 1.434.241.62.261 1.356.058 2.19-.203.832-.608 1.59-1.204 2.257-.595.667-1.38 1.226-2.332 1.664-.945.434-2.049.713-3.286.832-.388.037-.783.056-1.18.056h-.108c-.7 0-1.36-.09-1.953-.267a3.68 3.68 0 01-.72-.297c.02.233.052.463.097.686.207 1.02.68 1.897 1.37 2.526.685.625 1.597.986 2.712 1.073.117.01.236.014.354.014.847 0 1.666-.218 2.334-.617.748-.448 1.268-1.095 1.545-1.924l1.93.697c-.438 1.218-1.226 2.172-2.341 2.834C14.993 23.69 13.641 24 12.186 24zm-.235-8.986c.049 0 .099 0 .148-.002 1.028-.055 1.835-.337 2.397-.837.564-.503.869-1.218.908-2.127a9.773 9.773 0 00-1.772-.122c-.95.055-1.674.3-2.152.731-.434.39-.65.898-.616 1.433.044.814.553 1.297 1.087 1.297z"/></svg>`
        },
        {
          label: 'Facebook',
          svg: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>`
        }
      ]
    }
  }
}
</script>