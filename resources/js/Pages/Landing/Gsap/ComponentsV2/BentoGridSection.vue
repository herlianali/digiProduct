<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { ShoppingCartIcon } from '@heroicons/vue/24/outline'
import gsap from '@/plugins/gsap'

const bentoGridRef = ref(null)

// Map: card element -> array of active arrow tweens
const activeTweens = new Map()

// ─── Arrow rules berdasarkan filename src ───────────────────────────
const ARROW_RULES = [
  { match: 'arrow-black-bottom', x: 0,  y: 6  },
  { match: 'arrow-white-bottom', x: 0,  y: 6  },
  { match: 'arrow-black-left',   x: 4,  y: 4  },
  { match: 'arrow-white-left',   x: 4,  y: 4  },
  { match: 'arrow-black-right',  x: 4,  y: -4 },
  { match: 'arrow-white-right',  x: 4,  y: -4 },
]

const getArrowDir = (src) => {
  const rule = ARROW_RULES.find(r => src.includes(r.match))
  return rule ? { x: rule.x, y: rule.y } : null
}
// ────────────────────────────────────────────────────────────────────

const startCardAnimations = (card) => {
  stopCardAnimations(card)
  const tweens = []

  tweens.push(gsap.to(card, {
    scale: 1.03,
    y: -6,
    boxShadow: '0 24px 40px -12px rgba(0,0,0,0.25)',
    duration: 0.3,
    ease: 'back.out(0.5)',
  }))

  const allImgs = card.querySelectorAll('img')
  const nonArrowImgs = Array.from(allImgs).filter(img => !getArrowDir(img.src || img.getAttribute('src') || ''))
  if (nonArrowImgs.length) {
    tweens.push(gsap.to(nonArrowImgs, {
      scale: 1.07,
      duration: 0.35,
      ease: 'power2.out',
      stagger: 0.02,
    }))
  }

  allImgs.forEach(img => {
    const src = img.src || img.getAttribute('src') || ''
    const dir = getArrowDir(src)
    if (!dir) return
    gsap.set(img, { x: 0, y: 0 })
    const tween = gsap.to(img, {
      x: dir.x,
      y: dir.y,
      duration: 0.45,
      repeat: -1,
      yoyo: true,
      ease: 'sine.inOut',
    })
    tweens.push(tween)
  })

  activeTweens.set(card, tweens)
}

const stopCardAnimations = (card) => {
  const tweens = activeTweens.get(card)
  if (tweens) {
    tweens.forEach(t => t.kill())
    activeTweens.delete(card)
  }
  gsap.set(card, { scale: 1, y: 0, boxShadow: 'none' })
  const allImgs = card.querySelectorAll('img')
  allImgs.forEach(img => {
    gsap.set(img, { scale: 1, x: 0, y: 0 })
  })
}

const handlers = new Map()

const initCardHoverAnimations = () => {
  const cards = bentoGridRef.value?.querySelectorAll('.bento-card')
  if (!cards) return

  cards.forEach(card => {
    gsap.set(card, { transformOrigin: 'center center' })
    const onEnter = () => startCardAnimations(card)
    const onLeave = () => stopCardAnimations(card)
    card.addEventListener('mouseenter', onEnter)
    card.addEventListener('mouseleave', onLeave)
    handlers.set(card, { onEnter, onLeave })
  })
}

const initCardEntranceAnimation = () => {
  const cards = bentoGridRef.value?.querySelectorAll('.bento-card')
  if (!cards) return

  gsap.fromTo(cards,
    { opacity: 0, y: 30, scale: 0.95 },
    {
      opacity: 1, y: 0, scale: 1,
      duration: 0.6,
      stagger: { each: 0.05, from: 'start' },
      ease: 'back.out(0.4)',
      scrollTrigger: {
        trigger: bentoGridRef.value,
        start: 'top 85%',
        toggleActions: 'play none none reverse',
        once: false,
      }
    }
  )
}

onMounted(() => {
  initCardHoverAnimations()
  initCardEntranceAnimation()
})

onUnmounted(() => {
  activeTweens.forEach((tweens) => tweens.forEach(t => t.kill()))
  activeTweens.clear()
  handlers.forEach(({ onEnter, onLeave }, card) => {
    card.removeEventListener('mouseenter', onEnter)
    card.removeEventListener('mouseleave', onLeave)
  })
  handlers.clear()
})
</script>

<template>
  <div ref="bentoGridRef" class="mt-6 bg-white">
    <div class="w-full px-3 sm:px-4">

      <!-- ===================== DESKTOP GRID (md+) ===================== -->
      <div class="hidden md:grid grid-cols-12 gap-3 lg:gap-4">

        <!-- ROW 1 -->

        <!-- Welcome Card -->
        <div class="bento-card col-span-3 bg-[#fce109] rounded-2xl p-4 flex flex-col relative overflow-hidden cursor-pointer">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-md font-medium text-black/70">Welcome to</p>
              <h1 class="font-extrabold text-3xl xl:text-4xl text-black leading-tight tracking-tighter">SUPPLAYBOX</h1>
            </div>
            <img data-arrow="top-right" src="/public/assets/image/banner/arrow-black-right.svg" class="w-6 h-6 flex-shrink-0" alt="" />
          </div>
          <p class="text-xl xl:text-2xl font-light text-black mt-1">Hi Robert,</p>
          <p class="text-sm text-black/80 mt-2 max-w-[55%] leading-relaxed flex-1">Most people freeze at the starting line. Don't hesitate. Just give it a shot.</p>
          <div class="mt-1">
            <button class="absolute bottom-8 w-fit z-10 bg-[#4dfa03] hover:bg-green-700 border border-black font-extrabold text-lg xl:text-2xl rounded-full px-8 py-1 flex items-center transition-all whitespace-nowrap">
              FREE SKETCH!
            </button>
            <img src="/public/assets/image/banner/card-supplaybox-animated.svg" alt="mascot" class="absolute right-0 -bottom-5 w-[60%] max-w-[360px] object-contain pointer-events-none z-20" />
          </div>
        </div>

        <!-- Design & Illustration (row-span-2) -->
        <div class="bento-card col-span-2 row-span-2 bg-gray-200 border border-black rounded-2xl flex flex-col overflow-hidden cursor-pointer relative">
          <div class="flex justify-center items-center p-4 h-[14em]">
            <img src="/public/assets/image/banner/card-service-animated2.svg" class="w-full h-full" alt="" />
          </div>
          <div class="bg-black rounded-tl-2xl rounded-tr-2xl">
            <h3 class="font-bold text-white px-4 py-1 text-lg">Design Service</h3>
            <div class="rounded-t-2xl bg-gray-200">
              <p class="text-[12px] text-gray-800 leading-relaxed px-4 py-2 text-justify">
                Brand Identity / Logo Design / Poster Design / Packaging Design / Social Media Design / Infographic Design / Editorial Design / Book Design
              </p>
              <div class="bg-black rounded-tl-2xl rounded-tr-2xl">
                <h3 class="font-bold text-white px-4 py-1 text-lg">Illustration Service</h3>
                <div class="rounded-t-2xl bg-gray-200">
                  <p class="text-[12px] text-gray-800 leading-relaxed px-4 pt-2 text-justify">
                    2D Illustration / Environmental Design / Game Design / Character design / Mascot Illustration / Advertising Illustration
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="pb-1 flex justify-center p-2">
            <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="" class="w-8 h-8" />
          </div>
          <div class="flex-1 overflow-hidden relative min-h-[6rem]">
            <img src="/public/assets/image/banner/card-service-animated.svg" alt="service illustration" class="absolute -bottom-9 left-6 w-[16em] h-[16em] object-center z-20" />
          </div>
        </div>

        <!-- Teepublic -->
        <div class="bento-card col-span-2 bg-[#4c60d8] rounded-2xl flex flex-col cursor-pointer">
          <div class="px-4 pt-4 flex justify-between items-center">
            <div class="tracking-wider flex items-center gap-1">
              <img src="/public/assets/image/banner/card-teepublic-icon.svg" alt="" class="w-8 h-8" />
              <span class="text-white font-bold text-md">TEEPUBLIC</span>
            </div>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="w-6 h-6" />
          </div>
          <div class="flex items-center overflow-hidden h-[12em]">
            <img src="/public/assets/image/banner/card-teepublic-animated.svg" alt="teepublic" class="w-full h-auto object-cover" />
          </div>
          <div class="px-4 pb-4">
            <p class="font-bold text-white text-md">Unlimited<br />Prints.</p>
          </div>
        </div>

        <!-- Behance -->
        <div class="bento-card col-span-3 bg-black rounded-2xl flex flex-col overflow-hidden cursor-pointer">
          <div class="px-4 pt-4 flex justify-between items-start">
            <div>
              <p class="font-bold text-white text-3xl xl:text-4xl leading-tight">Bē</p>
              <p class="text-[11px] text-white leading-tight">More<br />Professional<br />Portofolio</p>
            </div>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="w-6 h-6" />
          </div>
          <div class="flex-1 overflow-hidden">
            <img src="/public/assets/image/banner/card-be-animated.svg" alt="behance" class="w-full h-full object-cover object-left" />
          </div>
        </div>

        <!-- Icons + Upwork + Amidst (col-span-2, row-span-2) -->
        <div class="col-span-2 row-span-2 flex flex-col gap-3">
          <div class="flex justify-between items-center gap-2 pr-1 pt-1">
            <ShoppingCartIcon class="w-10 h-10 text-green-500 cursor-pointer hover:text-green-600 transition-colors" />
            <span class="border-2 border-black rounded-full px-8 py-1 text-black text-lg font-medium text-center whitespace-nowrap">$100</span>
            <div class="w-10 h-10 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center">
              <img src="" alt="user" class="w-full h-full object-cover" />
            </div>
          </div>

          <div class="bento-card bg-black rounded-2xl flex flex-col overflow-hidden flex-1 cursor-pointer">
            <div class="px-4 pt-4 relative">
              <p class="text-base text-white">New Seller</p>
              <p class="text-white text-base">on <b class="font-['Archivo_Black',sans-serif]">upwork</b></p>
              <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="absolute top-4 right-4 w-6 h-6" />
            </div>
            <div class="flex-1 flex flex-col justify-end overflow-hidden">
              <div class="h-[200px] xl:h-[280px] overflow-hidden">
                <img src="/public/assets/image/banner/card-upwork-animated.svg" alt="upwork" class="w-full h-full object-cover object-bottom" />
              </div>
              <div class="rounded-t-2xl bg-[#268a00] px-4 py-3 -mt-[50px] relative z-10">
                <p class="text-white text-[13px] font-extrabold leading-tight">Enjoy many Bonuses<br />and Discounts<br />on this Platform!</p>
                <p class="text-[12px] text-white mt-1">Help us<br />to grow even more :)</p>
              </div>
            </div>
          </div>

          <div class="bento-card bg-white rounded-2xl p-3 flex flex-col justify-center text-base cursor-pointer border border-gray-100">
            <p class="text-[#cccccc]">Amidst</p>
            <p class="text-[#b3b3b3] font-medium italic">the noise</p>
            <p class="text-[#cccccc]">and</p>
            <p class="text-[#b3b3b3] font-medium italic">visual</p>
            <p class="text-[#b3b3b3] font-medium italic">disruption.</p>
            <p class="text-[#108a00] font-medium">it's not</p>
            <p class="text-[#108a00] font-medium">about</p>
            <p class="text-[#cccccc]">being <span class="text-[#108a00] font-medium">fast</span></p>
            <p class="text-[#cccccc]">and <span class="text-[#108a00] font-medium">empty.</span></p>
            <p class="text-[#cccccc] mt-0.5">but...<span class="inline-flex items-center justify-center w-4 h-4 ml-1 align-middle">
              <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="Down Arrow" class="w-3 h-3">
            </span></p>
          </div>
        </div>

        <!-- ROW 2 -->

        <!-- Pinterest + TikTok + Starter Pack -->
        <div class="col-span-3 grid grid-cols-2 grid-rows-2 gap-3 h-[380px] xl:h-[420px]">
          <div class="bento-card row-span-2 bg-[#E60023] rounded-2xl p-3 flex flex-col relative overflow-hidden cursor-pointer">
            <div class="flex justify-between items-start pb-2">
              <img src="/public/assets/image/banner/card-path-logo.svg" class="w-10 h-10" alt="" />
              <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" class="w-6 h-6" alt="" />
            </div>
            <p class="text-white text-md mt-3 leading-relaxed relative z-10">Let's <b class="font-black italic">CONNECT</b> on Pinterest, Share some pins, and dive into our <b class="font-black italic">Portfolio</b> + inspo boards.</p>
            <div class="absolute bottom-0 left-0 right-0 h-[40%] overflow-hidden">
              <img src="/public/assets/image/banner/card-path-animated.svg" alt="pinterest" class="w-[14em] h-[14em] object-cover object-top" />
            </div>
          </div>

          <div class="bento-card bg-white border border-gray-800 rounded-2xl p-2 relative overflow-hidden cursor-pointer">
            <div class="flex justify-between items-start px-1 pt-1">
              <img src="/public/assets/image/banner/card-tiktok-logo.svg" class="w-10 h-10" alt="TikTok" />
              <img data-arrow="right" src="/public/assets/image/banner/arrow-black-right.svg" alt="TikTok" class="w-6 h-6" />
            </div>
            <p class="text-[11px] max-w-[120px] px-1 pt-4 font-semibold">Some memes for entertainment, and behind the scenes of our routine.</p>
            <div class="absolute -bottom-1 left-0 right-0 h-[45%] overflow-hidden">
              <img src="/public/assets/image/banner/card-tiktok-animated.svg" alt="tiktok" class="w-full h-full object-cover object-top" />
            </div>
          </div>

          <div class="bento-card bg-gray-200 flex flex-col gap-1 rounded-2xl p-3 relative overflow-hidden cursor-pointer">
            <div>
              <p class="text-[12px] font-bold text-black">Starter Pack<br />Design.</p>
              <div class="flex items-center pt-6 relative h-6">
                <span class="absolute left-0 w-7 h-7 rounded-full bg-[#34bf72] border border-black"></span>
                <span class="absolute left-3 w-7 h-7 rounded-full bg-[#fec200] border border-black"></span>
                <span class="absolute left-6 w-7 h-7 rounded-full bg-[#3bffff] border border-black"></span>
                <span class="absolute left-9 w-7 h-7 rounded-full bg-[#fa191e] border border-black"></span>
                <span class="absolute left-[6em] text-[12px] text-gray-500">+2 More</span>
              </div>
            </div>
            <div class="mt-auto">
              <p class="text-[12px] leading-relaxed">Canva/<br />Shutterstock/<br />iconscout dll</p>
            </div>
          </div>
        </div>

        <!-- Fiverr + Our Product + Our Team -->
        <div class="col-span-5 flex flex-col gap-3 h-[380px] xl:h-[420px]">
          <div class="bento-card bg-[#f2ecea] rounded-2xl p-2 relative overflow-hidden flex-shrink-0 h-[130px] xl:h-[140px] cursor-pointer">
            <div class="flex justify-between items-start gap-2 relative z-10">
              <img data-arrow="left" src="/public/assets/image/banner/arrow-black-left.svg" alt="" class="p-2">
              <div class="flex flex-col items-end text-[#0b3117] text-lg xl:text-2xl">
                <p>Top Rated</p>
                <p>Badge on <em class="font-bold">fiverr</em>.</p>
              </div>
            </div>
            <div class="absolute bottom-0 left-0 h-[90%] w-[50%] overflow-hidden">
              <img src="/public/assets/image/banner/card-toprated-animated.svg" alt="fiverr" class="w-full h-full object-cover object-top" />
            </div>
            <img src="/public/assets/image/banner/card-fiver-badge.svg" alt="Fiverr Badge" class="absolute bottom-6 right-28 w-8 h-8" />
            <div class="absolute bottom-1 right-2 text-right text-[11px] italic text-[#0b3117]">
              <span class="block">For secure</span>
              <span class="block">transactions!</span>
            </div>
          </div>

          <div class="grid grid-cols-12 gap-3 flex-1 min-h-0">
            <div class="bento-card col-span-5 bg-[#fee100] rounded-2xl p-2 relative overflow-hidden cursor-pointer">
              <div class="absolute left-1/2 bottom-0 -translate-x-1/2 w-0 h-0 z-0 border-l-[240px] border-r-[240px] border-b-[90px] border-l-transparent border-r-transparent border-b-[#c0f901]"></div>
              <p class="font-extrabold text-black text-xl xl:text-2xl relative z-20 px-2">Our<br />Product</p>
              <div class="flex-1 flex items-center justify-center py-1 relative z-10">
                <img src="/public/assets/image/banner/card-ourproduct-animated.svg" alt="product" class="w-[45%] h-[45%] object-contain" />
              </div>
              <div class="text-center relative z-10 absolute bottom-3 left-0 right-0">
                <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="Arrow" class="inline w-8 h-8" />
              </div>
            </div>

            <div class="bento-card col-span-7 bg-[#abdec9] rounded-2xl relative overflow-hidden cursor-pointer">
              <div class="flex-1 flex items-center justify-center relative h-full">
                <img src="/public/assets/image/banner/card-ourteam-animated.png" alt="team" class="w-[80%] h-[50%]" />
              </div>
              <div class="absolute bottom-4 left-2 right-2">
                <p class="font-['Archivo_Black',sans-serif] text-white text-2xl font-bold leading-tight">Meet<br />Our Team</p>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- ===================== MOBILE LAYOUT (FLEX) ===================== -->
      <div class="md:hidden grid grid-cols-3 gap-3 w-full auto-rows-auto">
        <div class="bento-card col-span-2 bg-[#fce109] rounded-2xl p-3 relative overflow-hidden cursor-pointer min-h-[140px]">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-[10px] font-medium text-black/70">Welcome to</p>
              <h1 class="font-extrabold text-2xl text-black leading-tight tracking-tighter">SUPPLAYBOX</h1>
            </div>
            <img data-arrow="top-right" src="/public/assets/image/banner/arrow-black-right.svg" class="w-4 h-4" alt="" />
          </div>
          <p class="text-lg font-light text-black mt-0.5">Hi Robert,</p>
          <p class="text-[9px] text-black/80 mt-1 max-w-[55%] leading-relaxed">Most people freeze at the starting line. Don't hesitate. Just give it a shot.</p>
          <button class="mt-2 z-10 bg-[#4dfa03] border border-black font-extrabold text-sm rounded-full px-4 py-0.5 whitespace-nowrap">
            FREE SKETCH!
          </button>
          <img src="/public/assets/image/banner/card-supplaybox-animated.svg" alt="mascot" class="absolute right-0 -bottom-3 w-[45%] max-w-[180px] object-contain pointer-events-none z-20" />
        </div>

        <div class="bento-card col-span-1 row-span-3">
          <div class="col-span-3 flex justify-end items-center gap-3 py-1 pr-1">
            <ShoppingCartIcon class="w-6 h-6 text-green-500" />
            <span class="border-[1.5px] border-black rounded-full px-3 py-0.5 text-black text-[11px] font-medium whitespace-nowrap">$100</span>
            <div class="w-6 h-6 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center">
              <img src="" alt="user" class="w-full h-full object-cover" />
            </div>
          </div>
          <div class="row-span-2 bg-gray-200 border border-black rounded-2xl flex flex-col overflow-hidden cursor-pointer relative min-h-[220px]">
            <div class="flex justify-center items-center p-4 ">
              <img src="/public/assets/image/banner/card-service-animated2.svg" class="w-full h-full" alt="" />
            </div>
            <div class="bg-black rounded-tl-2xl rounded-tr-2xl">
              <h3 class="font-bold text-white px-2 py-1 text-[11px]">Design Service</h3>
              <div class="bg-gray-200">
                <p class="text-[9px] text-gray-800 leading-tight p-2">
                  Brand Identity / Logo / Poster / Packaging / Social Media / Infographic
                </p>
                <div class="bg-black">
                  <h3 class="font-bold text-white px-2 py-1 text-[11px]">Illustration</h3>
                  <div class="bg-gray-200">
                    <p class="text-[9px] text-gray-800 leading-tight px-2 pt-1 pb-1">
                      2D / Game / Character / Mascot
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex justify-center py-1">
              <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="" class="w-4 h-4" />
            </div>
            <div class="overflow-hidden relative flex-1 min-h-[140px]">
              <img src="/public/assets/image/banner/card-service-animated.svg" alt="service illustration" class="absolute -bottom-2 -left-0.5 w-full object-contain z-20" />
            </div>
          </div>
        </div>
        
        <div class="bento-card col-span-1 row-span-2 bg-[#E60023] rounded-2xl p-2 flex flex-col relative overflow-hidden cursor-pointer min-h-[220px]">
          <div class="flex justify-between items-start">
            <img src="/public/assets/image/banner/card-path-logo.svg" class="w-6 h-6" alt="" />
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" class="w-3 h-3" alt="" />
          </div>
          <p class="text-white text-[8px] mt-2 leading-relaxed relative z-10">Let's <b class="italic">CONNECT</b> on Pinterest, share some pins, and dive into our <b class="italic">Portfolio</b> + inspo boards.</p>
          <div class="absolute bottom-0 left-0 right-0 h-[40%] overflow-hidden">
            <img src="/public/assets/image/banner/card-path-animated.svg" alt="pinterest" class="w-full h-full object-cover object-top" />
          </div>
        </div>
        
        <div class="bento-card col-span-1 bg-white border border-gray-800 rounded-2xl p-2 relative overflow-hidden cursor-pointer min-h-[105px]">
          <div class="flex justify-between items-start">
            <img src="/public/assets/image/banner/card-tiktok-logo.svg" class="w-6 h-6" alt="TikTok" />
            <img data-arrow="right" src="/public/assets/image/banner/arrow-black-right.svg" alt="TikTok" class="w-3 h-3" />
          </div>
          <p class="text-[8px] mt-1 font-semibold leading-tight">Memes & behind the scenes.</p>
          <div class="absolute -bottom-1 left-0 right-0 h-[35%] overflow-hidden">
            <img src="/public/assets/image/banner/card-tiktok-animated.svg" alt="tiktok" class="w-full h-full object-cover object-top" />
          </div>
        </div>

        <div class="bento-card col-span-1 bg-gray-200 rounded-2xl p-2 flex flex-col cursor-pointer min-h-[105px]">
          <p class="text-[9px] font-bold text-black leading-tight">Starter Pack Design.</p>
          <div class="relative h-3 mt-1.5">
            <span class="absolute left-0 w-3 h-3 rounded-full bg-[#34bf72] border border-black"></span>
            <span class="absolute left-1.5 w-3 h-3 rounded-full bg-[#fec200] border border-black"></span>
            <span class="absolute left-3 w-3 h-3 rounded-full bg-[#3bffff] border border-black"></span>
          </div>
          <div class="mt-auto">
            <p class="text-[6.5px] text-gray-600 leading-tight">Canva/Shutterstock dll</p>
          </div>
        </div>

        <div class="bento-card col-span-1 bg-[#4c60d8] rounded-2xl flex flex-col overflow-hidden cursor-pointer min-h-[120px]">
          <div class="px-1.5 pt-1.5 flex justify-between items-center">
            <div class="flex items-center gap-0.5">
              <img src="/public/assets/image/banner/card-teepublic-icon.svg" alt="" class="w-3 h-3" />
              <span class="text-white font-bold text-[7.5px]">TEEPUBLIC</span>
            </div>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="w-2.5 h-2.5" />
          </div>
          <div class="overflow-hidden my-auto">
            <img src="/public/assets/image/banner/card-teepublic-animated.svg" alt="teepublic" class="w-full h-auto" />
          </div>
          <div class="px-1.5 pb-1.5">
            <p class="font-bold text-white text-[8px] leading-tight">Unlimited Prints.</p>
          </div>
        </div>
        
        <div class="bento-card col-span-1 bg-black rounded-2xl flex flex-col overflow-hidden cursor-pointer min-h-[120px]">
          <div class="px-1.5 pt-1.5 flex justify-between items-start">
            <div>
              <p class="font-bold text-white text-xl leading-none">Bē</p>
              <p class="text-[7.5px] text-white leading-tight mt-0.5">More Professional</p>
            </div>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="w-2.5 h-2.5" />
          </div>
          <div class="overflow-hidden mt-auto w-full">
            <img src="/public/assets/image/banner/card-be-animated.svg" alt="behance" class="w-full h-auto object-cover" />
          </div>
        </div>

        <div class="bento-card col-span-1 row-span-2 bg-black rounded-2xl flex flex-col overflow-hidden cursor-pointer min-h-[220px]">
          <div class="px-2 pt-2 relative">
            <p class="text-[9px] text-white leading-none">New Seller</p>
            <p class="text-white text-[9px] leading-tight">on <b class="font-['Archivo_Black',sans-serif]">upwork</b></p>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="absolute top-2 right-2 w-2.5 h-2.5" />
          </div>
          <div class="overflow-hidden mt-1 flex-1 flex flex-col justify-end">
            <img src="/public/assets/image/banner/card-upwork-animated.svg" alt="upwork" class="w-full h-auto object-cover" />
            <div class="bg-[#268a00] px-2 py-1.5 relative z-10 rounded-t-xl -mt-4">
              <p class="text-white text-[7.5px] font-extrabold leading-tight">Enjoy many Bonuses and Discounts!</p>
            </div>
          </div>
        </div>

        <div class="bento-card col-span-2 bg-[#f2ecea] rounded-2xl p-2 relative overflow-hidden cursor-pointer min-h-[90px]">
          <div class="flex justify-between items-center relative z-10">
            <img data-arrow="left" src="/public/assets/image/banner/arrow-black-left.svg" alt="" class="w-5 h-5 p-0.5">
            <div class="flex flex-col items-end text-[#0b3117] text-[10px] leading-tight">
              <p>Top Rated</p>
              <p>Badge on <em class="font-bold">fiverr</em>.</p>
            </div>
          </div>
          <div class="absolute bottom-0 left-0 h-[80%] w-[45%] overflow-hidden">
            <img src="/public/assets/image/banner/card-toprated-animated.svg" alt="fiverr" class="w-full h-full object-cover object-top" />
          </div>
          <div class="absolute bottom-1 right-2 text-right text-[7px] italic text-[#0b3117]">
            <span>For secure transactions!</span>
          </div>
        </div>

        <div class="bento-card col-span-1 bg-[#fee100] rounded-2xl p-2 relative overflow-hidden cursor-pointer min-h-[120px] flex flex-col justify-between">
          <p class="font-extrabold text-black text-[11px] leading-tight z-20">Our<br>Product</p>
          <div class="flex items-center justify-center relative z-10 flex-1">
            <img src="/public/assets/image/banner/card-ourproduct-animated.svg" alt="product" class="w-10 object-contain" />
          </div>
          <div class="text-center z-10">
            <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="Arrow" class="inline w-4 h-4" />
          </div>
          <div class="absolute left-1/2 bottom-0 -translate-x-1/2 w-0 h-0 z-0 border-l-[80px] border-r-[80px] border-b-[40px] border-l-transparent border-r-transparent border-b-[#c0f901]"></div>
        </div>
        
        <div class="bento-card col-span-1 bg-[#abdec9] rounded-2xl relative overflow-hidden cursor-pointer min-h-[120px] flex flex-col justify-between p-2">
          <div class="flex items-center justify-center flex-1">
            <img src="/public/assets/image/banner/card-ourteam-animated.png" alt="team" class="w-14" />
          </div>
          <div class="mt-auto">
            <p class="font-['Archivo_Black',sans-serif] text-white text-[9px] leading-tight">Meet<br>Our Team</p>
          </div>
        </div>

        <div class="bento-card col-span-1 bg-white rounded-2xl p-2 cursor-pointer border border-gray-100 flex flex-col justify-center min-h-[120px]">
          <p class="text-[#cccccc] text-[7.5px] leading-none">Amidst</p>
          <p class="text-[#b3b3b3] font-medium italic text-[7.5px] leading-none">the noise</p>
          <p class="text-[#cccccc] text-[7.5px] leading-none">and visual</p>
          <p class="text-[#b3b3b3] font-medium italic text-[7.5px] leading-none">disruption.</p>
          <p class="text-[#108a00] font-medium text-[7.5px] leading-none mt-1">it's not</p>
          <p class="text-[#108a00] font-medium text-[7.5px] leading-none">about</p>
          <p class="text-[#cccccc] text-[7.5px] leading-none">being <span class="text-[#108a00] font-medium">fast</span></p>
          <p class="text-[#cccccc] text-[7.5px] leading-none">and <span class="text-[#108a00] font-medium">empty.</span></p>
          <p class="text-[#cccccc] text-[7.5px] mt-1">but...<img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="" class="w-2 h-2 inline ml-0.5 align-middle"></p>
        </div>
        
      </div>
    </div>
  </div>
</template>

<style scoped>
.bento-card {
  transition: box-shadow 0.2s ease;
  will-change: transform, box-shadow;
  backface-visibility: hidden;
  -webkit-font-smoothing: subpixel-antialiased;
}

.bento-card:hover {
  z-index: 20;
}

.bento-card img {
  will-change: transform;
}

[data-arrow] {
  will-change: transform;
}

.overflow-hidden {
  -webkit-mask-image: -webkit-radial-gradient(white, black);
}

button:active {
  transform: scale(0.97);
}
</style>