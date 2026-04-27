<script setup>
import NavbarFloating from '../ComponentsV2/NavbarFloating.vue'
import FooterSection from '../ComponentsV2/FooterSection.vue'
import LoadingScreen from '../ComponentsV2/LoadingScreen.vue'
</script>

<template>
  <LoadingScreen />

  <div class="bg-[#0d0d0d] min-h-screen flex flex-col font-sans">

    <!-- ── HEADER / NAVBAR ── -->
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

      <!-- ── MAIN CONTENT ── -->
      <main class="flex-1 mx-auto w-full px-10 py-14">

        <!-- Heading -->
        <h2 class="text-white font-black text-5xl tracking-widest uppercase mb-4">
          Creative Team
        </h2>

        <!-- Description -->
        <p class="text-white/60 text-sm leading-relaxed max-w-4xl mb-12">
          We are full service Brand Design Agency specializing in Sports, Lifestyle, Outdoor and Entertainment industries.
          Working closely with world class brands like Hot Wheels, Tony Hawk, Nike, Universal Pictures, Monster Energy and
          Hasbro to bring to market visual solutions to elevate their brands. Headquarters in the Pacific Northwest we do
          things different. We live and breathe the markets we work in, we create indoors and outdoors, we create on the
          road and in the studio. We even create for ourselves, building our own brand, so we can better help build yours.
          – We are Lincoln Design Co.
        </p>

        <!-- ── STATS ROW ── -->
        <!--
          Pakai grid 4 kolom + border pada wrapper supaya
          setiap sel otomatis sama tingginya (grid stretch default).
          border-r pada 3 kolom pertama = divider vertikal.
        -->
        <div class="grid grid-cols-4 mb-14 rounded-2xl overflow-hidden">

          <!-- 800+ -->
          <div class="flex items-center gap-4 px-8 py-7 border-r border-white/10">
            <div class="w-9 h-9 rounded-full bg-[#4dfa03] flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="3"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            <div>
              <p class="text-white font-black text-4xl leading-none">800+</p>
              <p class="text-white/50 text-xs mt-2">Project Done</p>
            </div>
          </div>

          <!-- Top Rated -->
          <div class="flex items-center gap-4 px-8 py-7 border-r border-white/10">
            <div class="w-12 h-12 rounded-full border-2 border-white/30 flex flex-col items-center justify-center shrink-0">
              <span class="text-white/70 font-black text-[8px] leading-tight tracking-widest uppercase">Top</span>
              <span class="text-white/70 font-black text-[8px] leading-tight tracking-widest uppercase">Rated</span>
            </div>
            <div>
              <p class="text-white font-black text-2xl leading-none">Top Rated</p>
              <p class="text-white/50 text-xs mt-2">Badge in Fiverr</p>
            </div>
          </div>

          <!-- 4,9 -->
          <div class="flex items-center gap-4 px-8 py-7 border-r border-white/10">
            <span class="text-[#f5d020] text-4xl leading-none shrink-0">★</span>
            <div>
              <p class="text-white font-black text-4xl leading-none">4,9</p>
              <p class="text-white/50 text-xs mt-2">Customer Satisfaction</p>
            </div>
          </div>

          <!-- 13 Years -->
          <div class="flex items-center gap-4 px-8 py-7">
            <p class="text-white font-black text-4xl leading-none shrink-0">13</p>
            <p class="text-white/50 text-xs leading-snug">Years of<br>Experience</p>
          </div>

        </div>

        <!-- ── STUDIO BANNER IMAGE ── -->
        <div class="w-full rounded-2xl overflow-hidden bg-[#111] aspect-[16/5]">
          <img
            :src="studioBanner"
            alt="Studio workspace"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- ── TEAM GRID ── -->
        <div class="grid grid-cols-4 mt-10 mb-14 gap-4">
          <div
            v-for="(member, i) in teamMembers"
            :key="i"
            class="flex flex-col"
          >
            <!-- FLIP CARD -->
            <div class="flip-card rounded-3xl overflow-hidden" style="height: 460px;">
              <div class="flip-card-inner w-full h-full">

                <!-- FRONT -->
                <div class="flip-card-front w-full h-full" :style="{ backgroundColor: member.cardBg }">
                  <img :src="member.photo" :alt="member.name" class="w-full h-full object-cover" />
                </div>

                <!-- BACK -->
                <div class="flip-card-back w-full h-full" :style="{ backgroundColor: member.cardBg2 }">
                  <img :src="member.photo2" :alt="member.name + ' alt'" class="w-full h-full object-cover" />
                </div>

              </div>
            </div>

            <!-- Info -->
            <div class="pt-3 pb-4 pr-4">
              <p class="text-white font-black text-lg leading-tight">{{ member.name }}</p>
              <p class="text-white/40 text-xs italic mt-0.5">{{ member.role }}</p>
            </div>
          </div>
        </div>

        <!-- ── CLIENT / BRAND LOGOS (STATIC) ── -->
        <div class="rounded-2xl py-8 mb-0">
          <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-5">
            <img src="/public/assets/icons/brand-partner/01_monster_energy.png" alt="Monster Energy">
            <img src="/public/assets/icons/brand-partner/02_brian_antonion.png" alt="Brian Antonion">
            <img src="/public/assets/icons/brand-partner/03_kastel_oil.png" alt="Kastel Oil">
            <img src="/public/assets/icons/brand-partner/04_resurock.png" alt="Resurock">
            <img src="/public/assets/icons/brand-partner/05_ufo_logo.png" alt="UFO">
            <img src="/public/assets/icons/brand-partner/06_balkun_brothers.png" alt="Balkun Brothers">
            <img src="/public/assets/icons/brand-partner/07_fuego.png" alt="Fuego">
            <img src="/public/assets/icons/brand-partner/08_p_logo.png" alt="P Logo">
            <img src="/public/assets/icons/brand-partner/09_pigdon_street_film_festival.png" alt="Pigdon Street Film Festival">
            <img src="/public/assets/icons/brand-partner/10_atliens.png" alt="ATLiens">
            <img src="/public/assets/icons/brand-partner/11_showdown.png" alt="Showdown">
            <img src="/public/assets/icons/brand-partner/12_cheung_kuong.png" alt="Cheung Kuong">
          </div>
        </div>

      </main>
    </div>

    <FooterSection />

  </div>
</template>

<script>
export default {
  name: 'OurTeam',

  data() {
    return {
      studioBanner: '/assets/image/team/banner.png',

      teamMembers: [
        {
          name: 'Ammar Adani',
          role: 'Art Director',
          photo:  '/assets/image/team/ammar.png',
          photo2: '/assets/image/team/ammar_alt.png',
          cardBg:  '#54d99f',
          cardBg2: '#3ab87f',
        },
        {
          name: 'Deny Irawan',
          role: 'Senior Illustrator',
          photo:  '/assets/image/team/deni.png',
          photo2: '/assets/image/team/deni_alt.png',
          cardBg:  '#fff9e5',
          cardBg2: '#f0e8c0',
        },
        {
          name: 'Silvy Winda',
          role: 'Project Manager',
          photo:  '/assets/image/team/winda.png',
          photo2: '/assets/image/team/winda_alt.png',
          cardBg:  '#f994ff',
          cardBg2: '#d966e8',
        },
        {
          name: 'Mohammad Yusrin',
          role: 'Chief Creative Officer',
          photo:  '/assets/image/team/yusrin.png',
          photo2: '/assets/image/team/yusrin_alt.png',
          cardBg:  '#abdec9',
          cardBg2: '#7bbfa6',
        },
      ],
    }
  },
}
</script>

<style scoped>
/* ── FLIP CARD ── */
.flip-card {
  perspective: 1000px;
  cursor: pointer;
}

.flip-card-inner {
  position: relative;
  transform-style: preserve-3d;
  transition: transform 0.55s cubic-bezier(0.4, 0.2, 0.2, 1);
}

.flip-card:hover .flip-card-inner {
  transform: rotateY(180deg);
}

.flip-card-front,
.flip-card-back {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
}

.flip-card-back {
  transform: rotateY(180deg);
}
</style>