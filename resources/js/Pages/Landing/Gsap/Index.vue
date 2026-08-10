<!-- index.vue -->
<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import FooterSection from './ComponentsV2/FooterSection.vue'
import LoadingScreen from './ComponentsV2/LoadingScreen.vue'
import BannerCards from './ComponentsV2/BannerCards.vue'
import OurWorkSection from './ComponentsV2/OurWorkSection.vue'
import ProductSection from './ComponentsV2/ProductSection.vue'
import RackDivider from './ComponentsV2/RackDivider.vue'
import BentoGridSection from './ComponentsV2/BentoGridSection.vue'
import NavbarFloating from './ComponentsV2/NavbarFloating.vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import { useCart } from '@/Composables/useCart'
import {
    StarIcon as StarSolid,
    ArrowUpRightIcon as ArrowUpRightSolid,
    ArrowDownIcon as ArrowDownSolid,
} from '@heroicons/vue/24/solid'

gsap.registerPlugin(ScrollTrigger)

const bannerRef = ref(null)
const sliderTrackRef = ref(null)
const sliderContainerRef = ref(null)
const sliderPrevBtn = ref(null)
const sliderNextBtn = ref(null)
let isSliderHovered = ref(false)
let autoScrollInterval = null

const arrowRef = ref(null)
let arrowAnimation = null

const cart = useCart()
const addedIds = ref(new Set())
// ─── Product Section ───────────────────────────────────────────────
const productSectionRef = ref(null)
const activeFilter = ref('artwork')
const activeProductId = ref(2)

const handleAddToCart = (product) => {
    cart.add({
        id:        product.id,
        name:      product.name,
        price:     product.is_free ? 0 : (parseFloat(product.price) || 0),
        is_free:   product.is_free ?? false,
        thumbnail: product.image ?? null,
    })
    addedIds.value = new Set(addedIds.value).add(product.id)
    setTimeout(() => {
        const next = new Set(addedIds.value)
        next.delete(product.id)
        addedIds.value = next
    }, 1500)
}

const products = [
    { id: 1,  name: 'Autobiography',      category: 'artwork', image: '/assets/image/shop/product-1.png', tags: ['Illustration', 'Dark'], price: 15, is_free: false },
    { id: 2,  name: 'Incarnation',        category: 'artwork', image: '/assets/image/shop/product-2.png', tags: ['Illustration', 'Bold'], price: 20, is_free: false },
    { id: 3,  name: 'Everything is Evil', category: 'artwork', image: '/assets/image/shop/product-3.png', tags: ['Poster', 'Dark'],       price: 18, is_free: false },
    { id: 4,  name: 'Drunk',              category: 'artwork', image: '/assets/image/shop/product-4.png', tags: ['Illustration'],         price: 12, is_free: false },
    { id: 5,  name: 'Minor Threat',       category: 'artwork', image: '/assets/image/shop/product-5.png', tags: ['Poster', 'Bold'],       price: 0,  is_free: true  },
    { id: 6,  name: 'Simple',             category: 'artwork', image: '/assets/image/shop/product-6.png', tags: ['Minimal'],              price: 10, is_free: false },
    { id: 7,  name: 'Rest in Happy',      category: 'artwork', image: '/assets/image/shop/product-7.png', tags: ['Illustration'],         price: 15, is_free: false },
    { id: 8,  name: 'Enemy',              category: 'artwork', image: '/assets/image/shop/product-8.png', tags: ['Dark', 'Bold'],         price: 22, is_free: false },
    { id: 9,  name: 'Grotesk Bold',       category: 'font',    image: '/assets/image/shop/product-4.png', tags: ['Sans-Serif', 'Bold'],   price: 30, is_free: false },
    { id: 10, name: 'Display Noir',       category: 'font',    image: '/assets/image/shop/product-6.png', tags: ['Display', 'Dark'],      price: 25, is_free: false },
]

const redirectToProduct = (id) => {
    window.location.href = `/product-place/detail/${id}`
}

const redirectToAboutUs = () => {
    window.location.href = `/our-team`
}

const redirectToGetInTouch = () => {
    window.location.href = `/get-in-touch?voucher=FRST`
}

const filteredProducts = () => {
  const filtered = products.filter(p => p.category === activeFilter.value)
  return filtered.map((product, index) => ({
    ...product,
    index: index
  }))
}

const setFilter = (filter) => {
    if (activeFilter.value === filter) return
    const cards = productSectionRef.value?.querySelectorAll('.product-card')
    if (cards) {
        gsap.to(cards, {
            opacity: 0,
            y: 12,
            duration: 0.18,
            stagger: 0.03,
            ease: 'power2.in',
            onComplete: () => {
                activeFilter.value = filter
            }
        })
    } else {
        activeFilter.value = filter
    }
}
// ───────────────────────────────────────────────────────────────────

let sliderTween = null

const testimonials = [
    { id: 1, name: 'Sarah Johnson', role: 'Brand Director', company: 'Luxe Studio', country: 'United States', flag: '🇺🇸', avatar: 'SJ', avatarColor: '#6366f1', text: 'The creativity and attention to detail is absolutely unmatched. Our brand identity has never looked better.', rating: 5 },
    { id: 2, name: 'Marcus Tan', role: 'Founder', company: 'TanStack Co.', country: 'Singapore', flag: '🇸🇬', avatar: 'MT', avatarColor: '#10b981', text: 'Working with SUPPLAYBOX was a game changer. Delivery was fast and the results blew us away.', rating: 5 },
    { id: 3, name: 'Priya Mehta', role: 'CMO', company: 'Spark Agency', country: 'India', flag: '🇮🇳', avatar: 'PM', avatarColor: '#f59e0b', text: 'From concept to final artwork, the process was seamless. Highly recommend for any packaging project.', rating: 5 },
    { id: 4, name: 'Lucas Bernard', role: 'Creative Lead', company: 'Neon Works', country: 'France', flag: '🇫🇷', avatar: 'LB', avatarColor: '#ec4899', text: 'Bold, expressive, and exactly on-brand. They understood our vision immediately.', rating: 5 },
    { id: 5, name: 'Aiko Nakamura', role: 'Product Designer', company: 'Umami Labs', country: 'Japan', flag: '🇯🇵', avatar: 'AN', avatarColor: '#3b82f6', text: "The illustration style is so unique and versatile. We've used it across every touchpoint.", rating: 5 },
    { id: 6, name: 'Diego Reyes', role: 'CEO', company: 'Brillo Brands', country: 'Mexico', flag: '🇲🇽', avatar: 'DR', avatarColor: '#f97316', text: 'Exceptional work, tight deadlines, zero stress. This is the team you want on your side.', rating: 5 },
]

let marqueeObserver = null
let arrowObserver = null

let isMarqueeInViewport = true

const applyMarqueeState = () => {
    if (!sliderTween) return
    if (isMarqueeInViewport && !isSliderHovered.value) {
        sliderTween.resume()
    } else {
        sliderTween.pause()
    }
}

onMounted(() => {
    initSectionAnimations()
    initInfiniteSlider()
    initProductSectionAnimation()
    initArrowAnimation()
    initVisibilityGates()
})

onUnmounted(() => {
    if (sliderTween) sliderTween.kill()
    if (arrowAnimation) arrowAnimation.kill()
    ScrollTrigger.getAll().forEach(t => t.kill())
    marqueeObserver?.disconnect()
    arrowObserver?.disconnect()
})

// ─── Pause animasi infinite saat elemen di luar viewport ───────────
const initVisibilityGates = () => {
    if (sliderContainerRef.value) {
        marqueeObserver = new IntersectionObserver(
            ([entry]) => {
                isMarqueeInViewport = entry.isIntersecting
                applyMarqueeState()
            },
            { threshold: 0 }
        )
        marqueeObserver.observe(sliderContainerRef.value)
    }

    if (arrowRef.value) {
        arrowObserver = new IntersectionObserver(
            ([entry]) => {
                if (!arrowAnimation) return
                if (entry.isIntersecting) {
                    arrowAnimation.resume()
                } else {
                    arrowAnimation.pause()
                }
            },
            { threshold: 0 }
        )
        arrowObserver.observe(arrowRef.value)
    }
}

// ─── Arrow Animation ───────────────────────────────────────────────
const initArrowAnimation = () => {
    if (!arrowRef.value) return

    arrowAnimation = gsap.to(arrowRef.value, {
        x: 8,
        duration: 0.6,
        repeat: -1,
        yoyo: true,
        ease: "power1.inOut",
        repeatDelay: 0.1
    })
}

// ─── Product Section Animation ─────────────────────────────────────
const initProductSectionAnimation = () => {
    if (!productSectionRef.value) return

    gsap.from('.shop-title', {
        scrollTrigger: {
            trigger: productSectionRef.value,
            start: 'top 85%',
            toggleActions: 'play none none reverse',
        },
        y: 36,
        opacity: 0,
        duration: 0.7,
        ease: 'power3.out',
    })

    gsap.from('.shop-filter-btn', {
        scrollTrigger: {
            trigger: productSectionRef.value,
            start: 'top 82%',
            toggleActions: 'play none none reverse',
        },
        y: 16,
        opacity: 0,
        duration: 0.45,
        stagger: 0.08,
        ease: 'power2.out',
        delay: 0.15,
    })

    animateProductCards()
}

const animateProductCards = () => {
    setTimeout(() => {
        const cards = productSectionRef.value?.querySelectorAll('.product-card')
        if (!cards || !cards.length) return

        gsap.fromTo(cards,
            { opacity: 0, y: 30, scale: 0.97 },
            {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.55,
                stagger: { each: 0.06, from: 'start', grid: 'auto' },
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: productSectionRef.value,
                    start: 'top 78%',
                    toggleActions: 'play none none reverse',
                },
            }
        )

        gsap.from('.shop-cta-btn', {
            scrollTrigger: {
                trigger: '.shop-cta-btn',
                start: 'top 95%',
                toggleActions: 'play none none reverse',
            },
            y: 20,
            opacity: 0,
            duration: 0.5,
            ease: 'power2.out',
        })
    }, 50)
}

const onFilterChange = (filter) => {
    setFilter(filter)
    setTimeout(() => animateProductCards(), 80)
}
// ───────────────────────────────────────────────────────────────────

const initInfiniteSlider = () => {
    const track = sliderTrackRef.value
    if (!track) return

    Array.from(track.children).forEach(card => {
        const clone = card.cloneNode(true)
        clone.setAttribute('aria-hidden', 'true')
        track.appendChild(clone)
    })

    const totalWidth = track.scrollWidth / 2
    const slideDuration = 30

    sliderTween = gsap.to(track, {
        x: `-=${totalWidth}`,
        duration: slideDuration,
        ease: 'none',
        repeat: -1,
        modifiers: {
            x: gsap.utils.unitize(x => parseFloat(x) % totalWidth),
        },
    })

    let isDragging = false
    let startX = 0
    let dragOffset = 0

    const container = sliderContainerRef.value
    container.addEventListener('mouseenter', () => {
        isSliderHovered.value = true
        applyMarqueeState()
        showNavigationButtons()
    })

    container.addEventListener('mouseleave', () => {
        isSliderHovered.value = false
        applyMarqueeState()
        hideNavigationButtons()
    })

    const slidePrev = () => {
        if (!sliderTween) return
        const currentX = parseFloat(gsap.getProperty(track, 'x'))
        const slideWidth = track.children[0]?.offsetWidth + 20
        const newX = currentX + slideWidth
        gsap.to(track, {
            x: newX,
            duration: 0.6,
            ease: 'power2.out',
            modifiers: {
                x: gsap.utils.unitize(x => {
                    const total = track.scrollWidth / 2
                    let val = parseFloat(x) % total
                    if (val > 0) val = -total + val
                    return val
                })
            }
        })
    }

    const slideNext = () => {
        if (!sliderTween) return
        const currentX = parseFloat(gsap.getProperty(track, 'x'))
        const slideWidth = track.children[0]?.offsetWidth + 20
        const newX = currentX - slideWidth
        gsap.to(track, {
            x: newX,
            duration: 0.6,
            ease: 'power2.out',
            modifiers: {
                x: gsap.utils.unitize(x => {
                    const total = track.scrollWidth / 2
                    let val = parseFloat(x) % total
                    if (val > 0) val = -total + val
                    return val
                })
            }
        })
    }

    if (sliderPrevBtn.value) {
        sliderPrevBtn.value.addEventListener('click', (e) => {
            e.stopPropagation()
            slidePrev()
        })
    }

    if (sliderNextBtn.value) {
        sliderNextBtn.value.addEventListener('click', (e) => {
            e.stopPropagation()
            slideNext()
        })
    }

    const onMouseDown = (e) => {
        isDragging = true
        startX = e.clientX
        dragOffset = parseFloat(gsap.getProperty(track, 'x'))
        gsap.set(track, { cursor: 'grabbing' })
        sliderTween.pause()
    }

    const onMouseMove = (e) => {
        if (!isDragging) return
        const delta = e.clientX - startX
        const newX = dragOffset + delta
        gsap.set(track, { x: newX })
    }

    const onMouseUp = () => {
        if (!isDragging) return
        isDragging = false
        gsap.set(track, { cursor: 'grab' })
        const currentX = parseFloat(gsap.getProperty(track, 'x'))
        const slideWidth = track.children[0]?.offsetWidth + 20
        const totalWidth = track.scrollWidth / 2
        let normalizedX = currentX % totalWidth
        if (normalizedX > 0) normalizedX = -totalWidth + normalizedX
        const slideIndex = Math.round(-normalizedX / slideWidth)
        const targetX = -slideIndex * slideWidth
        gsap.to(track, { x: targetX, duration: 0.4, ease: 'power2.out' })
        // Setelah drag selesai, kembalikan ke state sesuai hover/visibility saat ini
        // (bukan langsung resume, supaya tidak melanggar aturan pause saat hover/off-viewport)
        applyMarqueeState()
    }

    track.addEventListener('mousedown', onMouseDown)
    window.addEventListener('mousemove', onMouseMove)
    window.addEventListener('mouseup', onMouseUp)
    track.style.cursor = 'grab'
}

const showNavigationButtons = () => {
    if (sliderPrevBtn.value && sliderNextBtn.value) {
        gsap.killTweensOf([sliderPrevBtn.value, sliderNextBtn.value])
        gsap.to(sliderPrevBtn.value, { opacity: 1, x: 0, scale: 1, duration: 0.4, ease: 'back.out(0.5)', display: 'flex' })
        gsap.to(sliderNextBtn.value, { opacity: 1, x: 0, scale: 1, duration: 0.4, ease: 'back.out(0.5)', display: 'flex' })
    }
}

const hideNavigationButtons = () => {
    if (sliderPrevBtn.value && sliderNextBtn.value) {
        gsap.killTweensOf([sliderPrevBtn.value, sliderNextBtn.value])
        gsap.to(sliderPrevBtn.value, {
            opacity: 0, x: -20, scale: 0.8, duration: 0.3, ease: 'power2.in',
            onComplete: () => { if (!isSliderHovered.value) gsap.set(sliderPrevBtn.value, { display: 'none' }) }
        })
        gsap.to(sliderNextBtn.value, {
            opacity: 0, x: 20, scale: 0.8, duration: 0.3, ease: 'power2.in',
            onComplete: () => { if (!isSliderHovered.value) gsap.set(sliderNextBtn.value, { display: 'none' }) }
        })
    }
}

const initSectionAnimations = () => {
    gsap.from('.section-title', {
        scrollTrigger: {
            trigger: '.section-title',
            start: 'top 80%',
            toggleActions: 'play none none reverse',
        },
        y: 50,
        opacity: 0,
        duration: 1,
        stagger: 0.2,
    })
}

const openLink = (url) => {
    window.open(url, '_blank')
}
</script>

<template>
    <LoadingScreen />

    <NavbarFloating
        trigger-el=".bento-grid-section"
        balance="$100"
        avatar-url=""
        @cta-click="() => {}"
    />

    <BentoGridSection class="bento-grid-section" />

    <div class="bg-[#dedede]">
        <RackDivider
            :color="'#000000'"
            :top-color="'#ffffff'"
            :mid-radius="40"
            :height="40"
            :tab-height="34"
            :notch-depth="34"
            :tab-width="40"
            :radius="10"
            :responsive="true"
            :manual-positions="[
                { xPct: 4,  type: 'out' },
                { xPct: 25, type: 'out' },
                { xPct: 30, type: 'in' },
                { xPct: 38, type: 'out' },
                { xPct: 43, type: 'in' },
                { xPct: 93, type: 'out' }
            ]"
            :manual-positions-mobile="[
                { xPct: 12, type: 'out' },
                { xPct: 35, type: 'out' },
                { xPct: 62, type: 'in' },
                { xPct: 80, type: 'out' }
            ]"
            :tab-width-mobile="28"
        />
    </div>

    <div class="bg-gradient-to-b from-black to-gray-800 text-white pb-28">
        <div class="px-6 sm:px-10 md:px-20 lg:px-40 pt-8 sm:pt-10 md:pt-12 pb-8 sm:pb-10">
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-bold leading-tight">
                Perception
            </h1>
            <p class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl leading-tight">
                And <b class="font-bold">Connection.</b>
            </p>
        </div>

        <div ref="sliderContainerRef" class="relative w-full overflow-hidden py-4 group">
            <button
                ref="sliderPrevBtn"
                class="absolute left-2 sm:left-4 md:left-6 lg:left-8 top-1/2 -translate-y-1/2 z-20 bg-[#b4f000] backdrop-blur-sm text-black rounded-full p-2 sm:p-2.5 md:p-3 shadow-lg hover:bg-green-400 transition-all duration-300 opacity-0 -translate-x-5 scale-90 hidden"
                style="display: none;"
            >
                <ChevronLeftIcon class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" />
            </button>

            <button
                ref="sliderNextBtn"
                class="absolute right-2 sm:right-4 md:right-6 lg:right-8 top-1/2 -translate-y-1/2 z-20 bg-[#b4f000] backdrop-blur-sm text-black rounded-full p-2 sm:p-2.5 md:p-3 shadow-lg hover:bg-green-400 transition-all duration-300 opacity-0 translate-x-5 scale-90 hidden"
                style="display: none;"
            >
                <ChevronRightIcon class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" />
            </button>

            <div ref="sliderTrackRef" class="flex gap-2.5 sm:gap-3 w-max will-change-transform px-3 sm:px-4 md:px-6 py-2 cursor-grab active:cursor-grabbing">
                <div
                    v-for="item in testimonials"
                    :key="item.id"
                    class="testimonial-card flex-shrink-0 w-[260px] sm:w-[300px] md:w-[340px] lg:w-[380px] bg-white rounded-2xl p-3 sm:p-3.5 md:p-4 flex flex-col gap-1.5 sm:gap-2 cursor-default shadow-sm"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-9 h-9 sm:w-10 sm:h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center text-[22px] font-bold text-black flex-shrink-0"
                                :style="{ backgroundColor: item.avatarColor }"
                            >
                                {{ item.avatar }}
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-black leading-tight">{{ item.name }}</p>
                                <p class="text-[11px] sm:text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                    <span>{{ item.flag }}</span>
                                    <span class="hidden sm:inline">{{ item.country }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 border-t border-gray-100 pt-1.5">
                        <StarSolid v-for="i in item.rating" :key="i" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-black" />
                        <span class="text-[10px] sm:text-xs font-semibold text-black ml-1">{{ item.rating }}</span>
                    </div>

                    <p class="text-[11px] sm:text-xs text-gray-700 leading-snug line-clamp-3">
                        {{ item.text }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Logo partner -->
        <div class="px-4 pt-4 pb-8">
            <div class="flex flex-wrap justify-center items-center px-4 opacity-80">
                <img src="/public/assets/icons/brand-partner/all-logo.png" />
            </div>
        </div>
    </div>

    <div class="relative z-10 -mt-20 -mb-20 bg-black text-black py-12 rounded-3xl text-center">
        <div class="mx-auto px-10 justify-between flex gap-6">
            <img src="/public/assets/image/our-work.svg" alt="Featured Image" class="">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Featured</h2>
        </div>

        <OurWorkSection />

        <div class="flex justify-end items-center opacity-90 px-10">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2 group cursor-pointer">
                    <span class="text-white group-hover:text-[#b4f000] transition-colors duration-300">More Portfolio</span>
                    <img src="/public/assets/image/panah-porto.svg" alt="" class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1 group-hover:brightness-0 group-hover:saturate-100 group-hover:invert-[60%] group-hover:sepia-[100%] group-hover:hue-rotate-[60deg]">
                </div>
                <div class="bg-[#333333] rounded-xl flex items-center gap-2 px-2">
                    <div class="social-icon-wrapper group" @click="openLink('https://pin.it/yegYhYpFy')">
                        <img src="/public/assets/image/pinterest-icon-porto.svg" alt="Pinterest" class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg]">
                    </div>
                    <div class="social-icon-wrapper group" @click="openLink('https://www.behance.net/supplay_box')">
                        <img src="/public/assets/image/behance-icon-porto.svg" alt="Behance" class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg]">
                    </div>
                    <div class="social-icon-wrapper group" @click="openLink('https://www.fiverr.com/user/supplaybox/portfolio')">
                        <img src="/public/assets/image/fiverr-icon-porto.svg" alt="Fiverr" class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg]">
                    </div>
                    <div class="social-icon-wrapper group" @click="openLink('https://www.upwork.com/freelancers/~018928f9b657bc5557?p=2002178990555295744')">
                        <img src="/public/assets/image/upwork-icon-porto.svg" alt="Upwork" class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg]">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Services -->
    <div id="our-services" class="bg-white pt-28 pb-2 px-24 text-black" style="scroll-margin-top: 100px;">
        <div class="flex justify-center items-start gap-8">
            <div class="flex flex-col min-w-[480px] max-w-[600px]">
                <h4 style="font-family: 'Arial Black' !important; line-height: 1;" class="text-[64px] pb-4">
                    Our<br>Services
                </h4>
                <p class="max-w-2xl text-[18px]" style="font-family: 'Barlow' !important;">Price differences across various service provider platforms due to additional costs for taxes and service fees set by each platform.</p>
            </div>
            <div class="flex flex-col">
                <div class="flex justify-center gap-8">
                    <div class="flex flex-col flex-1">
                        <h1 class="text-[40px] font-extrabold pb-2" style="font-family: 'Barlow' !important;">Design Service</h1>
                        <p class="leading-relaxed">
                            <span class="inline-flex flex-wrap mt-2 text-xl">
                                <span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer">Brand Identity</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer"> Logo Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer"> Poster Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer"> Packaging Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer"> Social Media Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer"> Infographic Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer"> Editorial Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer"> Book Design</span>
                            </span>
                        </p>
                    </div>
                    <div class="flex flex-col flex-1">
                        <h1 class="text-[40px] font-extrabold pb-2" style="font-family: 'Barlow' !important;">Illustration Service</h1>
                        <p class="leading-relaxed">
                            <span class="inline-flex flex-wrap mt-2 text-xl">
                                <span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer">2D Illustration</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer">Environmental Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer">Game Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer">Character design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer">Mascot Illustration</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:outline hover:outline-2 hover:outline-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:text-black cursor-pointer">Advertising Illustration</span>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex justify-start items-center mt-6 w-full">
                    <div class="flex items-center w-full">
                        <div class="bg-[#e6e6e6] rounded-r-full py-2 pl-4 pr-2 flex items-center justify-between flex-1" @click="redirectToGetInTouch">
                            <span class="text-2xl">Claim coupon <b class="text-black">10% off</b> for your first order</span>
                            <button class="bg-black text-white px-16 py-1 rounded-full uppercase text-2xl font-medium whitespace-nowrap ml-4 transition-all duration-300 ease-out hover:bg-[#b4f000] hover:text-black">
                                claim!
                            </button>
                        </div>
                        <div ref="arrowRef" class="bg-[#ff6600] p-2 rounded-full ml-4 flex-shrink-0">
                            <img
                                src="/public/assets/image/banner/arrow-white-bottom.svg"
                                alt=""
                                class="rotate-90 -scale-100 w-6 h-6"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#cfcfcf]">
        <RackDivider
            :color="'#e6e6e6'"
            :top-color="'#ffffff'"
            :mid-radius="40"
            :height="40"
            :tab-height="34"
            :notch-depth="34"
            :tab-width="40"
            :radius="10"
            :responsive="true"
            :manual-positions="[
                { xPct: 25, type: 'out' },
                { xPct: 30, type: 'in' },
                { xPct: 90, type: 'out' },
            ]"
            :manual-positions-mobile="[
                { xPct: 15, type: 'out' },
                { xPct: 35, type: 'in' },
                { xPct: 75, type: 'out' }
            ]"
            :tab-width-mobile="28"
        />
    </div>

    <!-- ============================================================ -->
    <!-- product placement                                            -->
    <!-- ============================================================ -->
    <div ref="productSectionRef" id="shop" class="bg-[#e6e6e6] py-4 px-16 text-black" style="scroll-margin-top: 100px;">
    <!-- Header row: title + filter tabs -->
    <div class="flex justify-between items-end mb-4 px-16">
      <h1 class="shop-title text-[48px] font-extrabold font-barlow leading-tight">
        Official<br />Supplaybox <br> Shop
      </h1>

      <div class="shop-filter-wrap">
        <button
          class="shop-filter-btn"
          :class="activeFilter === 'artwork' ? 'shop-filter-active' : 'shop-filter-inactive'"
          @click="onFilterChange('artwork')"
        >
          <span class="filter-dot" :class="activeFilter === 'artwork' ? 'filter-dot-active' : 'filter-dot-inactive'"></span>
          Artwork
        </button>
        <button
          class="shop-filter-btn"
          :class="activeFilter === 'font' ? 'shop-filter-active' : 'shop-filter-inactive'"
          @click="onFilterChange('font')"
        >
          <span class="filter-dot" :class="activeFilter === 'font' ? 'filter-dot-active' : 'filter-dot-inactive'"></span>
          Font
        </button>
      </div>
    </div>

    <div class="grid grid-cols-4 mb-10">
      <div
        v-for="product in filteredProducts()"
        :key="product.id"
        class="product-card group cursor-pointer transition-all duration-300 ease-out overflow-hidden"
      >
        <!-- Card dengan border yang disesuaikan -->
        <div
          class="product-card-inner border-b border-black/20"
          :class="[
            // Border kiri: hilang untuk card index 3 dan 7 (card ke-4 dan ke-8)
            (product.index === 3 || product.index === 7) ? 'border-r-0' : 'border-r border-black/20',
            // Border kanan: hilang untuk card index 0 dan 4 (card ke-1 dan ke-5)
            (product.index === 0 || product.index === 4) ? 'border-l-0' : 'border-l border-black/20',
          ]"
        >
          <div
            class="product-card-image-wrap p-8 pb-0 transition-colors duration-300"
            @click="redirectToProduct(product.id)"
          >
            <div class="overflow-hidden aspect-[3/4] w-full rounded-xl">
              <img
                :src="product.image"
                :alt="product.name"
                class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.05]"
              />
            </div>
          </div>

          <div class="product-card-footer px-4 py-3 flex flex-col items-center justify-center gap-2 transition-colors duration-300">
            <span
              class="font-barlow text-[35px] text-center cursor-pointer hover:underline"
              @click="redirectToProduct(product.id)"
            >
              {{ product.name }}
            </span>

            <button
              @click.stop="handleAddToCart(product)"
              class="add-to-cart-btn w-full mt-1 py-2 rounded-xl font-black text-sm flex items-center justify-center gap-1.5 transition-all active:scale-95"
              :class="addedIds.has(product.id)
                ? 'bg-[#4dfa03] text-black'
                : 'bg-black text-white hover:bg-gray-800'"
            >
              <template v-if="addedIds.has(product.id)">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Added!
              </template>
              <template v-else>
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                </svg>
                {{ product.is_free ? 'Get Free' : 'Add to Cart' }}
              </template>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA button -->
    <div class="flex justify-center pb-8">
      <button class="shop-cta-btn group relative overflow-hidden bg-black text-white font-semibold text-[22px] font-barlow tracking-widest uppercase px-4 py-2 rounded-full transition-all duration-300 hover:tracking-[0.18em]">
        <span class="absolute inset-0 bg-[#b4f000] translate-x-[-101%] group-hover:translate-x-0 transition-transform duration-350 ease-out rounded-full"></span>
        <span class="relative z-10 group-hover:text-black transition-colors duration-200">View All Product</span>
      </button>
    </div>
  </div>
    <!-- ============================================================ -->

    <FooterSection />

</template>

<style scoped>
.social-icon-wrapper {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.6rem;
  border-radius: 0.775rem;
}

.social-icon-wrapper:hover {
  background-color: #b4f000;
  transform: scale(1.05);
}

.social-icon-wrapper:hover img {
  filter: brightness(0) saturate(100%);
}

/* Filter pill container */
.shop-filter-wrap {
    display: inline-flex;
    align-items: center;
    background: #d4d4d4;
    border-radius: 100px;
    padding: 3px;
    gap: 0;
}

.shop-filter-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 7px 16px;
    border-radius: 100px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: background 0.22s ease, color 0.22s ease;
    letter-spacing: 0.01em;
    line-height: 1;
}

.shop-filter-active {
    background: #b4f000;
    color: #111;
}

.shop-filter-inactive {
    background: transparent;
    color: #666;
}

.shop-filter-inactive:hover {
    color: #111;
}

.filter-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    transition: background 0.22s ease, border-color 0.22s ease;
}

.filter-dot-active {
    background: #111;
    border: none;
}

.filter-dot-inactive {
    background: transparent;
    border: 1.5px solid #999;
}

.product-card-inner {
  background-color: #e6e6e6;
  transition: background-color 0.3s ease-out, border-color 0.3s ease-out;
  border-top: none;
}

.product-card:hover .product-card-inner {
  background-color: #b4f000;
  border-color: rgba(0, 0, 0, 0.3);
}

.product-card-image-wrap {
  background-color: #e6e6e6;
  transition: background-color 0.3s ease-out;
}

.product-card-footer {
  background-color: #e6e6e6;
  transition: background-color 0.3s ease-out;
}

.product-card:hover .product-card-image-wrap {
  background-color: #b4f000;
}

.product-card:hover .product-card-footer {
  background-color: #b4f000;
}

.product-card {
    background-color: #e6e6e6;
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                background-color 0.3s ease-out;
    will-change: transform, box-shadow;
}

.product-card-image-wrap {
    background-color: #e6e6e6;
    transition: background-color 0.3s ease-out;
}

.product-card-footer {
    background-color: #e6e6e6;
    transition: background-color 0.3s ease-out;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.18);
    background-color: #b4f000;
    z-index: 10;
}
.product-card:hover .product-card-image-wrap { background-color: #b4f000; }
.product-card:hover .product-card-footer     { background-color: #b4f000; border-top-color: rgba(0,0,0,0.1); }

.add-to-cart-btn {
    opacity: 0;
    transform: translateY(6px);
    transition: opacity 0.2s ease, transform 0.2s ease, background-color 0.15s ease;
}
.product-card:hover .add-to-cart-btn {
    opacity: 1;
    transform: translateY(0);
}

.shop-cta-btn .absolute {
    transition: transform 0.38s cubic-bezier(0.22, 1, 0.36, 1);
}

.cursor-grab  { cursor: grab; }
.cursor-grabbing { cursor: grabbing; }

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@media (max-width: 480px) {
  .line-clamp-3 {
    -webkit-line-clamp: 2;
  }
}
</style>