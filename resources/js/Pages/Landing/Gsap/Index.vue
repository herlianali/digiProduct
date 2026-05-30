<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import FooterSection from './ComponentsV2/FooterSection.vue'
import LoadingScreen from './ComponentsV2/LoadingScreen.vue'
import BannerCards from './Components/BannerCards.vue'
import OurWorkSection from './Components/OurWorkSection.vue'
import ProductSection from './Components/ProductSection.vue'
import RackDivider from './Components/RackDivider.vue'
import BentoGridSection from './ComponentsV2/BentoGridSection.vue' // ← import component baru
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ShoppingCartIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

import {
    StarIcon as StarSolid,
    ArrowUpRightIcon as ArrowUpRightSolid,
    ArrowDownIcon as ArrowDownSolid,
} from '@heroicons/vue/24/solid'

gsap.registerPlugin(ScrollTrigger)

const bannerRef = ref(null)
const navbarRef = ref(null)
const navbarContainerRef = ref(null)
const isNavbarVisible = ref(false)
const sliderTrackRef = ref(null)
const sliderContainerRef = ref(null)
const sliderPrevBtn = ref(null)
const sliderNextBtn = ref(null)
let isSliderHovered = ref(false)
let autoScrollInterval = null

const arrowRef = ref(null)
let arrowAnimation = null

// ─── Product Section ───────────────────────────────────────────────
const productSectionRef = ref(null)
const activeFilter = ref('artwork')
const activeProductId = ref(2)

const products = [
    { id: 1,  name: 'Autobiography',      category: 'artwork', image: '/assets/image/shop/product-1.png' },
    { id: 2,  name: 'Incarnation',        category: 'artwork', image: '/assets/image/shop/product-2.png' },
    { id: 3,  name: 'Everything is Evil', category: 'artwork', image: '/assets/image/shop/product-3.png' },
    { id: 4,  name: 'Drunk',              category: 'artwork', image: '/assets/image/shop/product-4.png' },
    { id: 5,  name: 'Minor Threat',       category: 'artwork', image: '/assets/image/shop/product-5.png' },
    { id: 6,  name: 'Simple',             category: 'artwork', image: '/assets/image/shop/product-6.png' },
    { id: 7,  name: 'Rest in Happy',      category: 'artwork', image: '/assets/image/shop/product-7.png' },
    { id: 8,  name: 'Enemy',              category: 'artwork', image: '/assets/image/shop/product-8.png' },
    { id: 9,  name: 'Grotesk Bold',       category: 'font',    image: '/assets/image/shop/product-4.png' },
    { id: 10, name: 'Display Noir',       category: 'font',    image: '/assets/image/shop/product-6.png' },
]

const redirectToProduct = (id) => {
    window.location.href = `/product-place/detail/${id}`
}

const redirectToAboutUs = () => {
    window.location.href = `/our-team`
}

const filteredProducts = () => products.filter(p => p.category === activeFilter.value)

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

onMounted(() => {
    initNavbarAnimation()
    initSectionAnimations()
    initInfiniteSlider()
    initProductSectionAnimation()
    initArrowAnimation()
})

onUnmounted(() => {
    if (sliderTween) sliderTween.kill()
    if (arrowAnimation) arrowAnimation.kill()
    ScrollTrigger.getAll().forEach(t => t.kill())
})

// ─── Arrow Animation ───────────────────────────────────────────────
const initArrowAnimation = () => {
    if (!arrowRef.value) return
    
    // Buat timeline infinite untuk animasi arrow ke kanan-kiri
    arrowAnimation = gsap.to(arrowRef.value, {
        x: 8, // bergerak ke kanan 8px
        duration: 0.6,
        repeat: -1, // infinite
        yoyo: true, // bolak-balik
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
        sliderTween.pause()
        isSliderHovered.value = true
        showNavigationButtons()
    })

    container.addEventListener('mouseleave', () => {
        sliderTween.resume()
        isSliderHovered.value = false
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

const initNavbarAnimation = () => {
    gsap.set(navbarContainerRef.value, {
        opacity: 0,
        y: -16,
        pointerEvents: 'none',
        position: 'fixed',
        top: '16px',
        right: '16px',
        left: 'auto',
        width: 'auto',
        zIndex: 1000,
        borderRadius: '50px',
        backgroundColor: 'rgba(255,255,255,0.97)',
        backdropFilter: 'blur(12px)',
        padding: '0 16px',
        border: '1px solid rgba(0,0,0,0.08)',
        boxShadow: '0 8px 24px -4px rgba(0,0,0,0.12)',
    })

    ScrollTrigger.create({
        trigger: '.bento-grid-section',
        start: 'bottom top',
        onEnter: () => {
            if (isNavbarVisible.value) return
            isNavbarVisible.value = true
            gsap.to(navbarContainerRef.value, { opacity: 1, y: 0, pointerEvents: 'auto', duration: 0.45, ease: 'power3.out' })
        },
        onLeaveBack: () => {
            if (!isNavbarVisible.value) return
            isNavbarVisible.value = false
            gsap.to(navbarContainerRef.value, { opacity: 0, y: -16, pointerEvents: 'none', duration: 0.3, ease: 'power2.in' })
        },
    })
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

const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId)
    if (element) {
        const offset = isNavbarVisible.value ? 80 : 0
        window.scrollTo({
            top: element.getBoundingClientRect().top + window.scrollY - offset,
            behavior: 'smooth',
        })
    }
}

const openLink = (url) => {
    window.open(url, '_blank')
}
</script>

<template>
    <LoadingScreen />
    
    <div ref="navbarContainerRef" class="z-[1000]">
        <nav ref="navbarRef">
            <div class="flex items-center gap-1 py-2.5">
                <div class="hidden md:flex gap-1 items-center">
                    <a href="#" class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5" @click.prevent="scrollToSection('about-us')">About Us</a>
                    <a href="#" class="font-bold text-black text-sm px-3 py-1.5 rounded-full bg-[#fee100] hover:bg-yellow-300 transition-colors duration-200 no-underline" @click.prevent="scrollToSection('platform')">Platform ▾</a>
                    <a href="#" class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5" @click.prevent="scrollToSection('shop')">Shop</a>
                    <a href="#" class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5" @click.prevent="scrollToSection('contact')">Contact</a>
                </div>

                <div class="hidden md:block w-px h-5 bg-gray-200 mx-1"></div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button class="bg-[#4dfa03] hover:bg-green-400 font-extrabold text-black px-4 py-1.5 rounded-full nav-button flex items-center gap-1 transition-all whitespace-nowrap text-sm border border-black/10">
                        FREE SKETCH!
                        <ChevronRightIcon class="w-3.5 h-3.5 text-black inline-block" />
                    </button>
                    <ShoppingCartIcon class="w-5 h-5 text-black cursor-pointer hover:text-green-600 transition-colors" />
                    <span class="hidden md:inline-block border border-gray-200 rounded-full px-3 py-1 text-black text-sm font-medium">$100</span>
                    <div class="w-7 h-7 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center flex-shrink-0">
                        <img src="" alt="user" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Bento Grid Section - menggunakan component terpisah -->
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
                { xPct: 3, type: 'out' },
                { xPct: 27, type: 'out' },
                { xPct: 30, type: 'in' },
                { xPct: 40, type: 'out' },
                { xPct: 43, type: 'in' },
                { xPct: 95, type: 'out' }
            ]"
        />
    </div>

    <div class="bg-gradient-to-b from-black to-gray-800 text-white pb-28">
        <div class="px-10 md:px-40 pt-12 pb-10">
            <h1 class="text-8xl font-bold">Perception</h1>
            <p class="text-8xl">And <b class="font-bold">Connection.</b></p>
        </div>

        <!-- Slider with Navigation -->
        <div ref="sliderContainerRef" class="relative w-full overflow-hidden py-4 group">
            <button
                ref="sliderPrevBtn"
                class="absolute left-8 top-1/2 -translate-y-1/2 z-20 bg-[#b4f000] backdrop-blur-sm text-white rounded-full p-3 shadow-lg hover:bg-green-700 transition-all duration-300 opacity-0 -translate-x-5 scale-90 hidden"
                style="display: none;"
            >
                <ChevronLeftIcon class="w-6 h-6" />
            </button>

            <button
                ref="sliderNextBtn"
                class="absolute right-8 top-1/2 -translate-y-1/2 z-20 bg-[#b4f000] backdrop-blur-sm text-white rounded-full p-3 shadow-lg hover:bg-green transition-all duration-300 opacity-0 translate-x-5 scale-90 hidden"
                style="display: none;"
            >
                <ChevronRightIcon class="w-6 h-6" />
            </button>

            <div ref="sliderTrackRef" class="flex gap-5 w-max will-change-transform px-6 py-2 cursor-grab active:cursor-grabbing">
                <div
                    v-for="item in testimonials"
                    :key="item.id"
                    class="flex-shrink-0 w-[500px] bg-white rounded-2xl p-8 flex flex-col gap-3 cursor-default shadow-sm"
                >
                    <!-- Header: avatar + name + country -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-14 h-14 rounded-full flex items-center justify-center text-md font-bold text-black flex-shrink-0"
                                :style="{ backgroundColor: item.avatarColor }"
                            >
                                {{ item.avatar }}
                            </div>
                            <div>
                                <p class="text-base font-bold text-black leading-tight">{{ item.name }}</p>
                                <p class="text-md text-gray-500 mt-0.5 flex items-center gap-1">
                                    <span>{{ item.flag }}</span>
                                    <span>{{ item.country }}</span>
                                </p>
                                <p class="text-md text-gray-500 mt-0.5 flex items-center">{{ item.role }} - {{ item.company }}</p>
                            </div>
                        </div>
                        <!-- three dots -->
                        <div class="flex flex-col gap-[3px] mt-1">
                            <span class="w-1 h-1 rounded-full bg-gray-400 block"></span>
                            <span class="w-1 h-1 rounded-full bg-gray-400 block"></span>
                            <span class="w-1 h-1 rounded-full bg-gray-400 block"></span>
                        </div>
                    </div>

                    <!-- Stars + rating number -->
                    <div class="flex items-center gap-1 border-t-2 border-gray-100 pt-2">
                        <StarSolid v-for="i in item.rating" :key="i" class="w-4 h-4 text-black" />
                        <span class="text-md font-semibold text-black ml-1">{{ item.rating }}</span>
                    </div>

                    <!-- Review text -->
                    <p class="text-md text-gray-700 leading-relaxed">{{ item.text }}</p>
                </div>
            </div>
        </div>

        <!-- logo partner -->
        <div class="px-8 pt-6 pb-14">
            <div class="flex flex-wrap justify-center items-center gap-x-10 gap-y-4 opacity-80">
                <img src="/public/assets/icons/brand-partner/01_monster_energy.png" alt="">
                <img src="/public/assets/icons/brand-partner/02_brian_antonion.png" alt="">
                <img src="/public/assets/icons/brand-partner/03_kastel_oil.png" alt="">
                <img src="/public/assets/icons/brand-partner/04_resurock.png" alt="">
                <img src="/public/assets/icons/brand-partner/05_ufo_logo.png" alt="">
                <img src="/public/assets/icons/brand-partner/06_balkun_brothers.png" alt="">
                <img src="/public/assets/icons/brand-partner/07_fuego.png" alt="">
                <img src="/public/assets/icons/brand-partner/08_p_logo.png" alt="">
                <img src="/public/assets/icons/brand-partner/09_pigdon_street_film_festival.png" alt="">
                <img src="/public/assets/icons/brand-partner/10_atliens.png" alt="">
                <img src="/public/assets/icons/brand-partner/11_showdown.png" alt="">
                <img src="/public/assets/icons/brand-partner/12_cheung_kuong.png" alt="">
            </div>
        </div>

        
    </div>
    <!-- featured section -->
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
                <div class="bg-[#333333] rounded-xl flex items-center">
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
    <div class="bg-white pt-28 pb-8 px-16 text-black">
        <div class="flex justify-center items-start gap-8">
            <div class="flex flex-col flex-shrink-0">
                <h1 class="text-6xl font-extrabold pb-2">Our <br>Services</h1>
                <p class="max-w-md">Price differences across various service provider platforms due to additional costs for taxes and service fees set by each platform.</p>
            </div>
            <div class="flex flex-col px-12">
                <div class="flex justify-center gap-8">
                    <div class="flex flex-col flex-1">
                        <h1 class="text-5xl font-bold pb-2">Design Service</h1>
                        <p class="leading-relaxed">
                            <span class="inline-flex flex-wrap gap-x-1 mt-2 text-xl">
                                <span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer">Brand Identity</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer"> Logo Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer"> Poster Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer"> Packaging Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer"> Social Media Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer"> Infographic Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer"> Editorial Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer"> Book Design</span>
                            </span>
                        </p>
                    </div>
                    <div class="flex flex-col flex-1">
                        <h1 class="text-5xl font-bold pb-2">Illustration Service</h1>
                        <p class="leading-relaxed">
                            <span class="inline-flex flex-wrap gap-x-1 mt-2 text-xl">
                                <span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer">2D Illustration</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer">Environmental Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer">Game Design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer">Character design</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer">Mascot Illustration</span>
                                <b class="text-yellow-500">/</b><span class="skill-item bg-transparent hover:border hover:border-black rounded-full px-3 text-black inline-block whitespace-nowrap transition-all duration-300 hover:bg-[#b4f000] hover:border-black hover:text-black cursor-pointer">Advertising Illustration</span>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex justify-start items-center mt-6 w-full">
                    <div class="flex items-center w-full">
                        <div class="bg-[#e6e6e6] rounded-r-full py-2 pl-4 pr-2 flex items-center justify-between flex-1">
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
        />
    </div>

    <!-- ============================================================ -->
    <!-- product placement                                            -->
    <!-- ============================================================ -->
    <div ref="productSectionRef" id="shop" class="bg-[#e6e6e6] py-14 px-16 text-black">

        <!-- Header row: title + filter tabs -->
        <div class="flex justify-between items-end mb-10">
            <h1 class="shop-title text-6xl font-bold leading-tight">
                Official<br />Supplaybox Shop
            </h1>

            <!-- Filter tabs: wrapped in a single pill container -->
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

        <!-- Product grid -->
        <div class="grid grid-cols-4 mb-10 gap-4">
            <div
                v-for="product in filteredProducts()"
                :key="product.id"
                class="product-card group cursor-pointer border border-black/10 transition-all duration-300 ease-out rounded-2xl overflow-hidden"
                @click="redirectToProduct(product.id)"
            >
                <!-- Artwork image wrapper dengan padding -->
                <div class="product-card-image-wrap p-12 pb-0 transition-colors duration-300">
                    <div class="overflow-hidden aspect-[3/4] w-full rounded-xl">
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.05]"
                        />
                    </div>
                </div>

                <!-- Card footer -->
                <div class="product-card-footer px-4 py-3 flex items-center justify-center transition-colors duration-300">
                    <span class="text-2xl font-semibold text-black">{{ product.name }}</span>
                </div>
            </div>
        </div>

        <!-- CTA button -->
        <div class="flex justify-center">
            <button class="shop-cta-btn group relative overflow-hidden bg-black text-white font-bold text-sm tracking-widest uppercase px-12 py-4 rounded-full transition-all duration-300 hover:tracking-[0.18em]">
                <!-- Green fill slides in from left on hover -->
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
    font-size: 13px;
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

.product-card {
    background-color: #ffffff;
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                background-color 0.3s ease-out;
    will-change: transform, box-shadow;
}

.product-card-image-wrap {
    background-color: #d0d0d0;
    transition: background-color 0.3s ease-out;
}

.product-card-footer {
    background-color: #d0d0d0;
    transition: background-color 0.3s ease-out;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.18);
    background-color: #b4f000;
    z-index: 10;
}

.product-card:hover .product-card-image-wrap {
    background-color: #b4f000;
}

.product-card:hover .product-card-footer {
    background-color: #b4f000;
    border-top-color: rgba(0,0,0,0.1);
}

.shop-cta-btn .absolute {
    transition: transform 0.38s cubic-bezier(0.22, 1, 0.36, 1);
}

.cursor-grab  { cursor: grab; }
.cursor-grabbing { cursor: grabbing; }
</style>