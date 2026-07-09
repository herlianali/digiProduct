<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { ShoppingCartIcon } from '@heroicons/vue/24/outline'
import { ChevronRightIcon } from '@heroicons/vue/20/solid'
import gsap from '@/plugins/gsap'

const bentoGridRef = ref(null)
const helloText = ref('')
const fullHelloText = 'Hi friends,'
let typewriterTimeout = null
let isTypewriterActive = true

const TYPE_SPEED = 100
const DELETE_SPEED = 80
const PAUSE_AFTER_TYPE = 1500
const PAUSE_AFTER_DELETE = 400

const runTypewriterLoop = () => {
  if (!isTypewriterActive) return

  let i = 0
  helloText.value = ''

  const typeStep = () => {
    if (!isTypewriterActive) return
    if (i < fullHelloText.length) {
      helloText.value += fullHelloText.charAt(i)
      i++
      typewriterTimeout = setTimeout(typeStep, TYPE_SPEED)
    } else {
      typewriterTimeout = setTimeout(deleteStep, PAUSE_AFTER_TYPE)
    }
  }

  const deleteStep = () => {
    if (!isTypewriterActive) return
    if (helloText.value.length > 0) {
      helloText.value = helloText.value.slice(0, -1)
      typewriterTimeout = setTimeout(deleteStep, DELETE_SPEED)
    } else {
      typewriterTimeout = setTimeout(runTypewriterLoop, PAUSE_AFTER_DELETE)
    }
  }

  typeStep()
}

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
  setTimeout(runTypewriterLoop, 400)
})

onUnmounted(() => {
  activeTweens.forEach((tweens) => tweens.forEach(t => t.kill()))
  activeTweens.clear()
  handlers.forEach(({ onEnter, onLeave }, card) => {
    card.removeEventListener('mouseenter', onEnter)
    card.removeEventListener('mouseleave', onLeave)
  })
  handlers.clear()
  isTypewriterActive = false
  clearTimeout(typewriterTimeout)
})
</script>

<template>
  <div ref="bentoGridRef" class="mt-6 bg-white">
    <div class="w-full px-3 sm:px-4">

      <!-- ===================== DESKTOP GRID (md+) ===================== -->
      <div
        class="hidden md:grid gap-3 lg:gap-4"
        style="grid-template-columns: repeat(12, minmax(0,1fr));"
      >

        <!-- ROW 1 -->

        <!-- Welcome Card -->
        <div
          class="bento-card bg-[#fce109] rounded-2xl p-4 flex flex-col relative overflow-hidden cursor-pointer"
          style="grid-column: 1 / 4; grid-row: 1 / 2;"
        >
          <div class="flex justify-between items-start">
            <div>
              <p class="font-medium text-black/70" style="font-size: clamp(1rem, 3.5cqw, 1rem);">Welcome to</p>
              <h1 class="font-extrabold text-black leading-tight tracking-tighter" style="font-size: clamp(2rem, 8cqw, 2.25rem);">SUPPLAYBOX</h1>
            </div>
            <img data-arrow="top-right" src="/public/assets/image/banner/arrow-black-right.svg" class="flex-shrink-0" style="width: 6cqw; height: 6cqw; max-width: 24px; max-height: 24px;" alt="" />
          </div>
          <p class="font-light text-black mt-1 typewriter-text" style="font-size: clamp(2rem, 5cqw, 1.5rem); min-height: 1.5em;">{{ helloText }}<span class="typewriter-cursor"></span></p>
          <p class="text-black/80 max-w-[55%] leading-relaxed flex-1" style="font-size: clamp(0.7rem, 3cqw, 0.875rem);">Most people freeze at the starting line. Don't hesitate. Just give it a shot.</p>
          <div class="mt-9">
            <button class="absolute w-fit z-10 bg-[#4dfa03] hover:bg-green-700 border border-black font-extrabold rounded-full px-4 py-1 flex items-center transition-all whitespace-nowrap" style="font-size: clamp(1rem, 4.5cqw, 2.5rem); bottom: clamp(10px, 1cqw, 8px); left: clamp(1.5rem, 6cqw, 12rem);">
              FREE SKETCH! <ChevronRightIcon class="w-[25px] h-[25px] font-bold" />
            </button>
            <img src="/public/assets/image/banner/card-supplaybox-animated.svg" alt="mascot" class="absolute right-0 object-contain pointer-events-none z-20" style="width: 55cqw; max-width: 200px; bottom: clamp(-24px, -3cqw, -8px);" />
          </div>
        </div>

        <!-- Design & Illustration (row-span-2) -->
        <div
          class="bento-card bg-gray-200 border border-black rounded-2xl flex flex-col overflow-hidden cursor-pointer relative"
          style="grid-column: 4 / 6; grid-row: 1 / 3;"
        >
          <div class="flex justify-center items-center p-2" style="height: clamp(160px, 70cqw, 360px);">
            <img src="/public/assets/image/banner/card-service-animated2.svg" class="w-full h-full" alt="" />
          </div>
          <div class="bg-black rounded-tl-2xl rounded-tr-2xl">
            <h3 class="font-bold text-white px-4 py-1" style="font-size: clamp(1rem, 4.5cqw, 1.125rem);">Design Service</h3>
            <div class="rounded-t-2xl bg-gray-200">
              <p class="text-gray-800 leading-relaxed px-4 py-2 text-justify" style="font-size: clamp(0.65rem, 3cqw, 0.8125rem);">
                Brand Identity / Logo Design / Poster Design / Packaging Design / Social Media Design / Infographic Design / Editorial Design / Book Design
              </p>
              <div class="bg-black rounded-tl-2xl rounded-tr-2xl">
                <h3 class="font-bold text-white px-4 py-1" style="font-size: clamp(1rem, 4.5cqw, 1.125rem);">Illustration Service</h3>
                <div class="rounded-t-2xl bg-gray-200">
                  <p class="text-gray-800 leading-relaxed px-4 pt-2 text-justify" style="font-size: clamp(0.65rem, 3cqw, 0.8125rem);">
                    2D Illustration / Environmental Design / Game Design / Character design / Mascot Illustration / Advertising Illustration
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="pb-1 flex justify-center p-2">
            <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="" style="width: 9cqw; height: 9cqw; max-width: 32px; max-height: 32px;" />
          </div>
          <div class="flex-1 overflow-hidden relative min-h-[6rem]">
            <img src="/public/assets/image/banner/card-service-animated.svg" alt="service illustration" class="absolute -bottom-5 left-8 object-center z-20" style="width: 80cqw; height: 80cqw;" />
          </div>
        </div>

        <!-- Teepublic -->
        <div
          class="bento-card bg-[#4c60d8] rounded-2xl flex flex-col cursor-pointer"
          style="grid-column: 6 / 8; grid-row: 1 / 2;"
        >
          <div class="px-2 pt-2 flex justify-between items-center">
            <div class="tracking-wider flex items-center gap-1">
              <img src="/public/assets/image/banner/card-teepublic-icon.svg" alt="" style="width: 12cqw; height: 12cqw; max-width: 32px; max-height: 32px;" />
              <span class="text-white font-bold" style="font-size: clamp(1.2rem, 4cqw, 1rem);">TEEPUBLIC</span>
            </div>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" style="margin-right: 6px; width: 8cqw; height: 8cqw; max-width: 24px; max-height: 24px;" />
          </div>
          <div class="flex items-end overflow-hidden">
            <img src="/public/assets/image/banner/card-teepublic-animated.svg" alt="teepublic" class="w-full h-full object-cover" />
          </div>
          <div class="px-4 pb-4">
            <p class="font-bold text-white" style="font-size: clamp(0.8rem, 4cqw, 1rem);">Unlimited<br />Prints.</p>
          </div>
        </div>

        <!-- Behance -->
        <div
          class="bento-card bg-black rounded-2xl flex flex-col overflow-hidden cursor-pointer"
          style="grid-column: 8 / 11; grid-row: 1 / 2;"
        >
          <div class="px-4 pt-4 flex justify-between items-start">
            <div>
              <p class="font-bold text-white leading-tight" style="font-size: clamp(1.5rem, 9cqw, 2.25rem);">Bē</p>
              <p class="text-white leading-tight" style="font-size: clamp(0.65rem, 3cqw, 0.75rem);">More<br />Professional<br />Portofolio</p>
            </div>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" style="width: 6cqw; height: 6cqw; max-width: 24px; max-height: 24px;" />
          </div>
          <div class="flex-1 overflow-hidden">
            <img src="/public/assets/image/banner/card-be-animated.svg" alt="behance" class="w-full h-full object-cover object-left" />
          </div>
        </div>

        <!-- Icons + Upwork + Amidst (col-span-2, row-span-2) -->
        <div class="flex flex-col gap-3" style="grid-column: 11 / 13; grid-row: 1 / 3;">
          <div class="flex justify-between items-center gap-2 pr-1 pt-1" style="container-type: inline-size;">
            <ShoppingCartIcon class="text-green-500 cursor-pointer hover:text-green-600 transition-colors" style="width: 10cqw; height: 10cqw; max-width: 40px; max-height: 40px;" />
            <span class="border-2 border-black rounded-full px-8 py-1 text-black font-medium text-center whitespace-nowrap" style="font-size: clamp(0.9rem, 4.5cqw, 1.125rem);">$100</span>
            <div class="rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center" style="width: 10cqw; height: 10cqw; max-width: 40px; max-height: 40px;">
              <img src="" alt="user" class="w-full h-full object-cover" />
            </div>
          </div>

          <div class="bento-card bg-black rounded-2xl flex flex-col overflow-hidden flex-1 cursor-pointer">
            <div class="px-4 pt-4 relative">
              <p class="text-white" style="font-size: clamp(0.8rem, 4cqw, 1rem);">New Seller</p>
              <p class="text-white" style="font-size: clamp(0.8rem, 4cqw, 1rem);">on <b class="font-['Archivo_Black',sans-serif]">upwork</b></p>
              <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="absolute top-4 right-4" style="width: 6cqw; height: 6cqw; max-width: 24px; max-height: 24px;" />
            </div>
            <div class="flex-1 flex flex-col justify-end overflow-hidden">
              <div class="overflow-hidden" style="height: 55cqw;">
                <img src="/public/assets/image/banner/card-upwork-animated.svg" alt="upwork" class="w-full h-full object-cover object-bottom" />
              </div>
              <div class="rounded-t-2xl bg-[#268a00] px-4 py-3 -mt-[50px] relative z-10">
                <p class="text-white font-extrabold leading-tight" style="font-size: clamp(0.75rem, 3.5cqw, 0.8125rem);">Enjoy many Bonuses<br />and Discounts<br />on this Platform!</p>
                <p class="text-white mt-1" style="font-size: clamp(0.7rem, 3.2cqw, 0.75rem);">Help us<br />to grow even more :)</p>
              </div>
            </div>
          </div>

          <div class="bento-card bg-white rounded-2xl p-3 flex flex-col justify-center cursor-pointer border border-gray-100" style="font-size: clamp(0.85rem, 4.2cqw, 1rem);">
            <p class="text-[#cccccc]">Amidst</p>
            <p class="text-[#b3b3b3] font-medium italic">the noise</p>
            <p class="text-[#cccccc]">and</p>
            <p class="text-[#b3b3b3] font-medium italic">visual</p>
            <p class="text-[#b3b3b3] font-medium italic">disruption.</p>
            <p class="text-[#108a00] font-medium">it's not</p>
            <p class="text-[#108a00] font-medium">about</p>
            <p class="text-[#cccccc]">being <span class="text-[#108a00] font-medium">fast</span></p>
            <p class="text-[#cccccc]">and <span class="text-[#108a00] font-medium">empty.</span></p>
            <p class="text-[#cccccc] mt-0.5">but...<span class="inline-flex items-center justify-center ml-1 align-middle" style="width: 4cqw; height: 4cqw; max-width: 16px; max-height: 16px;">
              <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="Down Arrow" class="w-full h-full">
            </span></p>
          </div>
        </div>

        <!-- ROW 2 -->

        <!-- Pinterest + TikTok + Starter Pack -->
        <div
          class="grid grid-cols-2 grid-rows-2 gap-3"
          style="grid-column: 1 / 4; grid-row: 2 / 3; height: clamp(360px, 25vw, 380px);"
        >
          <div class="bento-card row-span-2 bg-[#E60023] rounded-2xl p-3 flex flex-col relative overflow-hidden cursor-pointer">
            <div class="flex justify-between items-start pb-2">
              <img src="/public/assets/image/banner/card-path-logo.svg" style="width: 20cqw; height: 20cqw; max-width: 40px; max-height: 40px;" alt="" />
              <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" style="width: 14cqw; height: 14cqw; max-width: 44px; max-height: 44px;" alt="" />
            </div>
            <p class="text-white pt-2 leading-relaxed relative z-10" style="font-size: clamp(0.8rem, 4cqw, 1rem);">Let's <b class="font-black italic">CONNECT</b> on Pinterest, Share some pins, and dive into our <b class="font-black italic">Portfolio</b> + inspo boards.</p>
            <div class="absolute bottom-0 left-0 right-0 h-[55%] overflow-hidden">
              <img src="/public/assets/image/banner/card-path-animated.svg" alt="pinterest" class="absolute bottom-0 left-0 object-cover" style="width: 120cqw; height: 120cqw; object-position: bottom;" />
            </div>
          </div>

          <div class="bento-card bg-white border border-gray-800 rounded-2xl p-2 relative overflow-hidden cursor-pointer">
            <div class="flex justify-between items-start px-1 pt-1">
              <img src="/public/assets/image/banner/card-tiktok-logo.svg" style="width: 20cqw; height: 20cqw; max-width: 40px; max-height: 40px;" alt="TikTok" />
              <img data-arrow="right" src="/public/assets/image/banner/arrow-black-right.svg" alt="TikTok" style="width: 14cqw; height: 14cqw; max-width: 24px; max-height: 24px;" />
            </div>
            <p class="text-[10px] max-w-[120px] px-1 pt-2 font-semibold">some memes for entertainment, and behind the scenes of our routine.</p>
            <div class="absolute -bottom-1 left-0 right-0 h-[45%] overflow-hidden">
              <img src="/public/assets/image/banner/card-tiktok-animated.svg" alt="tiktok" class="w-full h-full object-cover object-top" />
            </div>
          </div>

          <div class="bento-card bg-gray-200 flex flex-col gap-1 rounded-2xl p-3 relative overflow-hidden cursor-pointer">
            <div>
              <p class="text-[12px] font-bold text-black">Starter Pack<br />Design.</p>
              <div class="flex items-center pt-6 relative h-6">
                <span class="absolute left-0 rounded-full bg-[#34bf72] border border-black" style="width: 18cqw; height: 18cqw; max-width: 28px; max-height: 28px;"></span>
                <span class="absolute left-3 rounded-full bg-[#fec200] border border-black" style="width: 18cqw; height: 18cqw; max-width: 28px; max-height: 28px;"></span>
                <span class="absolute left-6 rounded-full bg-[#3bffff] border border-black" style="width: 18cqw; height: 18cqw; max-width: 28px; max-height: 28px;"></span>
                <span class="absolute left-9 rounded-full bg-[#fa191e] border border-black" style="width: 18cqw; height: 18cqw; max-width: 28px; max-height: 28px;"></span>
                <span class="absolute left-[6em] text-[12px] text-gray-500">+2 More</span>
              </div>
            </div>
            <div class="mt-auto">
              <p class="text-[12px] leading-relaxed">Canva/<br />Shutterstock/<br />iconscout dll</p>
            </div>
          </div>
        </div>

        <!-- Fiverr + Our Product + Our Team -->
        <div
          class="flex flex-col gap-3"
          style="grid-column: 6 / 11; grid-row: 2 / 3; height: clamp(260px, 19vw, 380px);"
        >
          <div class="bento-card bg-[#f2ecea] rounded-2xl p-2 relative overflow-hidden flex-shrink-0 cursor-pointer" style="height: clamp(85px, 8vw, 125px);">
            <div class="flex justify-between items-start gap-2 relative z-10">
              <img data-arrow="left" src="/public/assets/image/banner/arrow-black-left.svg" alt="" class="p-2" style="width: 9cqw; height: 9cqw; max-width: 36px; max-height: 36px;">
              <div class="flex flex-col items-end text-[#0b3117]" style="font-size: clamp(0.9rem, 4.5cqw, 1.5rem);">
                <p>Top Rated</p>
                <p>Badge on <em class="font-bold">fiverr</em>.</p>
              </div>
            </div>
            <div class="absolute bottom-0 left-0 h-[90%] w-[50%] overflow-hidden">
              <img src="/public/assets/image/banner/card-toprated-animated.svg" alt="fiverr" class="w-full h-full object-cover object-top" />
            </div>
            <img src="/public/assets/image/banner/card-fiver-badge.svg" alt="Fiverr Badge" class="absolute bottom-6 right-28" style="width: 8cqw; height: 8cqw; max-width: 32px; max-height: 32px;" />
            <div class="absolute bottom-1 right-2 text-right italic text-[#0b3117]" style="font-size: clamp(0.65rem, 3cqw, 0.6875rem);">
              <span class="block">For secure</span>
              <span class="block">transactions!</span>
            </div>
          </div>

          <div class="grid grid-cols-12 gap-3 flex-1 min-h-0">
            <div class="bento-card col-span-5 bg-[#fee100] rounded-2xl p-2 relative overflow-hidden cursor-pointer" >
              <div class="absolute left-1/2 bottom-0 -translate-x-1/2 w-0 h-0 z-0 border-l-[200px] border-r-[200px] border-b-[120px] border-l-transparent border-r-transparent border-b-[#c0f901]"></div>
              <p class="font-extrabold text-black relative z-20 px-2" style="font-size: clamp(1.1rem, 5.5cqw, 1.5rem);">Our<br />Product</p>
              <div class="flex-1 flex items-center justify-center py-1 relative z-10">
                <img src="/public/assets/image/banner/card-ourproduct-animated.svg" alt="product" class="w-[40%] h-[40%] object-contain" />
              </div>
              <div class="text-center relative z-10 absolute -bottom-1 left-0 right-0">
                <img data-arrow="bottom" src="/public/assets/image/banner/arrow-black-bottom.svg" alt="Arrow" class="inline" style="width: 8cqw; height: 8cqw; max-width: 32px; max-height: 32px;" />
              </div>
            </div>

            <div class="bento-card col-span-7 bg-[#abdec9] rounded-2xl relative overflow-hidden cursor-pointer">
              <div class="flex-1 flex items-center justify-center relative h-full">
                <img src="/public/assets/image/banner/card-ourteam-animated.png" alt="team" class="w-full h-full px-4" />
              </div>
              <div class="absolute bottom-4 left-2 right-2">
                <p class="font-['Archivo_Black',sans-serif] text-white font-bold leading-tight" style="font-size: clamp(1.25rem, 6cqw, 1.5rem);">Meet<br />Our Team</p>
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
          <button class="mt-4 z-10 bg-[#4dfa03] border border-black font-extrabold text-sm rounded-full px-2 py-0.5 whitespace-nowrap">
            FREE SKETCH!
          </button>
          <img src="/public/assets/image/banner/card-supplaybox-animated.svg" alt="mascot" class="absolute -right-2 -bottom-2 w-[60%] max-w-[180px] object-contain pointer-events-none z-20" />
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
            <div class="bg-black rounded-tl-xl rounded-tr-xl">
              <h3 class="font-bold text-white px-3 py-1 text-[11px]">Design Service</h3>
              <div class="bg-gray-200 rounded-tl-xl rounded-tr-xl">
                <p class="text-[9px] text-gray-800 leading-tight p-2">
                  Brand Identity / Logo Design / Poster Design / Packaging Design / Social Media Design / Infographic Design / Editorial Design / Book Design
                </p>
                <div class="bg-black rounded-tl-xl rounded-tr-xl">
                  <h3 class="font-bold text-white px-3 py-1 text-[9px]">Illustration Service</h3>
                  <div class="bg-gray-200 rounded-tl-xl rounded-tr-xl">
                    <p class="text-[9px] text-gray-800 leading-tight px-2 pt-2 pb-1">
                      2D Illustration / Environmental Design / Game Design / Character design / Mascot Illustration / Advertising Illustration
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
        
        <div class="bento-card col-span-1 row-span-2 bg-[#E60023] rounded-2xl p-3 flex flex-col relative overflow-hidden cursor-pointer min-h-[220px]">
          <div class="flex justify-between items-start">
            <img src="/public/assets/image/banner/card-path-logo.svg" class="w-6 h-6" alt="" />
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" class="w-3 h-3" alt="" />
          </div>
          <p class="text-white text-[8px] mt-2 leading-relaxed relative z-10">Let's <b class="italic">CONNECT</b> on Pinterest, share some pins, and dive into our <b class="italic">Portfolio</b> + inspo boards.</p>
          <div class="absolute bottom-0 left-0 right-0 h-[50%] overflow-hidden">
            <img src="/public/assets/image/banner/card-path-animated.svg" alt="pinterest" class="w-full h-full object-cover object-top" />
          </div>
        </div>
        
        <div class="bento-card col-span-1 bg-white border-[1.5px] border-gray-800 rounded-2xl p-2 relative overflow-hidden cursor-pointer min-h-[105px]">
          <div class="flex justify-between items-start">
            <img src="/public/assets/image/banner/card-tiktok-logo.svg" class="w-6 h-6" alt="TikTok" />
            <img data-arrow="right" src="/public/assets/image/banner/arrow-black-right.svg" alt="TikTok" class="w-3 h-3" />
          </div>
          <p class="text-[8px] mt-1 font-semibold leading-tight">Some memes for entertainment, and behind the scenes of our routine.</p>
          <div class="absolute bottom-0 left-0 right-1 h-[40%] overflow-hidden">
            <img src="/public/assets/image/banner/card-tiktok-animated.svg" alt="tiktok" class="w-full h-full object-cover object-top" />
          </div>
        </div>

        <div class="bento-card col-span-1 bg-gray-200 rounded-2xl p-2 flex flex-col cursor-pointer min-h-[105px]">
          <p class="text-[9px] font-bold text-black leading-tight">Starter Pack <br> Design.</p>
          <div class="relative h-3 mt-1.5">
            <span class="absolute left-0 w-3 h-3 rounded-full bg-[#34bf72] border border-black"></span>
            <span class="absolute left-1.5 w-3 h-3 rounded-full bg-[#fec200] border border-black"></span>
            <span class="absolute left-3 w-3 h-3 rounded-full bg-[#3bffff] border border-black"></span>
            <span class="absolute left-[18px] w-3 h-3 rounded-full bg-[#FF222A] border border-black"></span>
          </div>
          <div class="mt-auto">
            <p class="text-[6.5px] text-gray-600 leading-tight">Canva/<br />Shutterstock/<br />iconscout dll</p>
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
          <div class="px-2 pt-2 flex justify-between items-start">
            <div>
              <p class="font-bold text-white text-xl leading-none">Bē</p>
              <p class="text-[7.5px] text-white leading-tight mt-0.5">More Professional</p>
            </div>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="w-2.5 h-2.5" />
          </div>
          <div class="overflow-hidden mt-auto w-full">
            <img src="/public/assets/image/banner/card-be-animated.svg" alt="behance" class="w-full h-full object-cover" />
          </div>
        </div>

        <div class="bento-card col-span-1 row-span-2 bg-black rounded-2xl flex flex-col overflow-hidden cursor-pointer min-h-[220px]">
          <div class="px-3 pt-3 relative">
            <p class="text-[9px] text-white leading-none">New Seller</p>
            <p class="text-white text-[9px] leading-tight">on <b class="font-['Archivo_Black',sans-serif]">upwork</b></p>
            <img data-arrow="right" src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="absolute top-3 right-3 w-2.5 h-2.5" />
          </div>
          <div class="overflow-hidden mt-1 flex-1 flex flex-col justify-end">
            <img src="/public/assets/image/banner/card-upwork-animated.svg" alt="upwork" class="w-full h-auto object-cover" />
            <div class="bg-[#268a00] px-2 py-1.5 relative z-10 rounded-t-xl -mt-4">
              <p class="text-white text-[7.5px] font-extrabold leading-tight">Enjoy many Bonuses<br />and Discounts<br />on this Platform!</p>
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
  container-type: inline-size;
  container-name: card;
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

.typewriter-cursor {
  display: inline-block;
  width: 2px;
  height: 1em;
  background-color: currentColor;
  margin-left: 2px;
  vertical-align: text-bottom;
  animation: blink-cursor 0.8s step-end infinite;
}

@keyframes blink-cursor {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

button:active {
  transform: scale(0.97);
}
</style>