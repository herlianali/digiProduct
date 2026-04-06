<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import BannerCards from './Components/BannerCards.vue'
import OurWorkSection from './Components/OurWorkSection.vue'
import ProductSection from './Components/ProductSection.vue'
import RackDivider from './Components/RackDivider.vue'
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
const bentoGridRef = ref(null)
const isNavbarVisible = ref(false)
const sliderTrackRef = ref(null)
const sliderContainerRef = ref(null)
const sliderPrevBtn = ref(null)
const sliderNextBtn = ref(null)
let isSliderHovered = ref(false)
let autoScrollInterval = null

let sliderTween = null
let hoverAnimations = [] // Store animations for cleanup

const testimonials = [
    { id: 1, name: 'Sarah Johnson', role: 'Brand Director', company: 'Luxe Studio', avatar: 'SJ', avatarColor: '#6366f1', text: 'The creativity and attention to detail is absolutely unmatched. Our brand identity has never looked better.', rating: 5 },
    { id: 2, name: 'Marcus Tan', role: 'Founder', company: 'TanStack Co.', avatar: 'MT', avatarColor: '#10b981', text: 'Working with SUPPLAYBOX was a game changer. Delivery was fast and the results blew us away.', rating: 5 },
    { id: 3, name: 'Priya Mehta', role: 'CMO', company: 'Spark Agency', avatar: 'PM', avatarColor: '#f59e0b', text: 'From concept to final artwork, the process was seamless. Highly recommend for any packaging project.', rating: 5 },
    { id: 4, name: 'Lucas Bernard', role: 'Creative Lead', company: 'Neon Works', avatar: 'LB', avatarColor: '#ec4899', text: 'Bold, expressive, and exactly on-brand. They understood our vision immediately.', rating: 5 },
    { id: 5, name: 'Aiko Nakamura', role: 'Product Designer', company: 'Umami Labs', avatar: 'AN', avatarColor: '#3b82f6', text: "The illustration style is so unique and versatile. We've used it across every touchpoint.", rating: 5 },
    { id: 6, name: 'Diego Reyes', role: 'CEO', company: 'Brillo Brands', avatar: 'DR', avatarColor: '#f97316', text: 'Exceptional work, tight deadlines, zero stress. This is the team you want on your side.', rating: 5 },
]

onMounted(() => {
    initNavbarAnimation()
    initSectionAnimations()
    initInfiniteSlider()
    initCardHoverAnimations()
    initCardEntranceAnimation()
})

onUnmounted(() => {
    if (sliderTween) sliderTween.kill()
    ScrollTrigger.getAll().forEach(t => t.kill())
    
    // Cleanup hover animations
    hoverAnimations.forEach(({ element, enterHandler, leaveHandler }) => {
        element.removeEventListener('mouseenter', enterHandler)
        element.removeEventListener('mouseleave', leaveHandler)
    })
    
    // Cleanup slider navigation
    if (sliderPrevBtn.value) {
        sliderPrevBtn.value.removeEventListener('click', () => {})
    }
    if (sliderNextBtn.value) {
        sliderNextBtn.value.removeEventListener('click', () => {})
    }
})

const initCardHoverAnimations = () => {
    // Target semua card utama dalam bento grid
    const cardSelectors = [
        '.col-span-3:not(.grid)', // Welcome card
        '.col-span-2.row-span-2', // Design & Illustration Service
        '.col-span-2:not(.row-span-2):not(.flex-col.gap-3)', // Teepublic
        '.col-span-3.bg-black', // Behance
        '.col-span-2.row-span-2 > .bg-black', // Upwork card
        '.bg-white.rounded-2xl.p-3', // Amidst text card
        '.col-span-3.grid .row-span-2', // Pinterest card
        '.col-span-3.grid .bg-white.border', // TikTok card
        '.col-span-3.grid .bg-gray-200', // Starter Pack card
        '.bg-\\[\\#f2ecea\\]', // Fiverr card
        '.col-span-5 .col-span-5.bg-\\[\\#fee100\\]', // Our Product card
        '.col-span-5 .col-span-7.bg-\\[\\#abdec9\\]' // Our Team card
    ]
    
    const allCards = bentoGridRef.value?.querySelectorAll(cardSelectors.join(','))
    
    if (!allCards) return
    
    allCards.forEach(card => {
        // Skip if already has hover animation
        if (card._hasHoverAnimation) return
        
        // Set initial styles
        gsap.set(card, {
            transformOrigin: 'center center',
            zIndex: 1
        })
        
        // Create hover animations
        const onMouseEnter = () => {
            // Main card animation
            const tl = gsap.timeline()
            tl.to(card, {
                scale: 1.03,
                y: -8,
                boxShadow: '0 30px 40px -20px rgba(0, 0, 0, 0.3), 0 10px 15px -8px rgba(0, 0, 0, 0.15)',
                duration: 0.35,
                ease: 'back.out(0.4)',
                overwrite: true
            }, 0)
            
            // Animate images inside card
            const images = card.querySelectorAll('img')
            if (images.length) {
                tl.to(images, {
                    scale: 1.08,
                    duration: 0.4,
                    ease: 'power2.out',
                    stagger: 0.02,
                    overwrite: true
                }, 0)
            }
            
            // Animate text elements
            const texts = card.querySelectorAll('p, h1, h2, h3, h4, span, button')
            if (texts.length) {
                tl.to(texts, {
                    y: -2,
                    duration: 0.25,
                    ease: 'power2.out',
                    stagger: 0.01,
                    overwrite: true
                }, 0)
            }
            
            // Special animation for specific cards
            if (card.querySelector('.rounded-t-2xl.bg-\\[\\#268a00\\]')) {
                tl.to(card.querySelector('.rounded-t-2xl.bg-\\[\\#268a00\\]'), {
                    y: -4,
                    duration: 0.3,
                    ease: 'power2.out'
                }, 0)
            }
            
            // Add border highlight effect
            gsap.to(card, {
                borderColor: 'rgba(0,0,0,0.2)',
                duration: 0.2,
                overwrite: true
            })
        }
        
        const onMouseLeave = () => {
            const tl = gsap.timeline()
            tl.to(card, {
                scale: 1,
                y: 0,
                boxShadow: 'none',
                duration: 0.3,
                ease: 'power2.in',
                overwrite: true
            }, 0)
            
            const images = card.querySelectorAll('img')
            if (images.length) {
                tl.to(images, {
                    scale: 1,
                    duration: 0.25,
                    ease: 'power2.in',
                    overwrite: true
                }, 0)
            }
            
            const texts = card.querySelectorAll('p, h1, h2, h3, h4, span, button')
            if (texts.length) {
                tl.to(texts, {
                    y: 0,
                    duration: 0.2,
                    ease: 'power2.in',
                    overwrite: true
                }, 0)
            }
            
            if (card.querySelector('.rounded-t-2xl.bg-\\[\\#268a00\\]')) {
                tl.to(card.querySelector('.rounded-t-2xl.bg-\\[\\#268a00\\]'), {
                    y: 0,
                    duration: 0.25,
                    ease: 'power2.in'
                }, 0)
            }
            
            gsap.to(card, {
                borderColor: '',
                duration: 0.2,
                overwrite: true
            })
        }
        
        card.addEventListener('mouseenter', onMouseEnter)
        card.addEventListener('mouseleave', onMouseLeave)
        
        // Store for cleanup
        hoverAnimations.push({
            element: card,
            enterHandler: onMouseEnter,
            leaveHandler: onMouseLeave
        })
        
        card._hasHoverAnimation = true
    })
}

const initCardEntranceAnimation = () => {
    const cards = bentoGridRef.value?.querySelectorAll('[class*="col-span"]')
    if (!cards) return
    
    // Filter out container elements
    const validCards = Array.from(cards).filter(card => 
        !card.classList.contains('grid') || 
        card.querySelectorAll('[class*="col-span"]').length <= 1
    )
    
    gsap.fromTo(validCards,
        {
            opacity: 0,
            y: 40,
            scale: 0.95
        },
        {
            opacity: 1,
            y: 0,
            scale: 1,
            duration: 0.7,
            stagger: {
                each: 0.04,
                from: 'start',
                grid: 'auto'
            },
            ease: 'back.out(0.3)',
            scrollTrigger: {
                trigger: bentoGridRef.value,
                start: 'top 85%',
                toggleActions: 'play none none reverse',
                once: false
            }
        }
    )
}

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

    // Store current position for manual control
    let currentX = 0
    let isDragging = false
    let startX = 0
    let dragOffset = 0

    // Mouse enter - pause auto scroll and show navigation buttons
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

    // Manual navigation functions
    const slidePrev = () => {
        if (!sliderTween) return
        const currentX = parseFloat(gsap.getProperty(track, 'x'))
        const slideWidth = track.children[0]?.offsetWidth + 20 // width + gap
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

    // Attach navigation functions to buttons
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

    // Optional: Add drag to slide functionality
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
        
        // Snap to nearest slide
        const currentX = parseFloat(gsap.getProperty(track, 'x'))
        const slideWidth = track.children[0]?.offsetWidth + 20
        const totalWidth = track.scrollWidth / 2
        let normalizedX = currentX % totalWidth
        if (normalizedX > 0) normalizedX = -totalWidth + normalizedX
        
        const slideIndex = Math.round(-normalizedX / slideWidth)
        const targetX = -slideIndex * slideWidth
        
        gsap.to(track, {
            x: targetX,
            duration: 0.4,
            ease: 'power2.out'
        })
    }

    track.addEventListener('mousedown', onMouseDown)
    window.addEventListener('mousemove', onMouseMove)
    window.addEventListener('mouseup', onMouseUp)
    track.style.cursor = 'grab'
}

const showNavigationButtons = () => {
    if (sliderPrevBtn.value && sliderNextBtn.value) {
        gsap.killTweensOf([sliderPrevBtn.value, sliderNextBtn.value])
        gsap.to(sliderPrevBtn.value, {
            opacity: 1,
            x: 0,
            scale: 1,
            duration: 0.4,
            ease: 'back.out(0.5)',
            display: 'flex'
        })
        gsap.to(sliderNextBtn.value, {
            opacity: 1,
            x: 0,
            scale: 1,
            duration: 0.4,
            ease: 'back.out(0.5)',
            display: 'flex'
        })
    }
}

const hideNavigationButtons = () => {
    if (sliderPrevBtn.value && sliderNextBtn.value) {
        gsap.killTweensOf([sliderPrevBtn.value, sliderNextBtn.value])
        gsap.to(sliderPrevBtn.value, {
            opacity: 0,
            x: -20,
            scale: 0.8,
            duration: 0.3,
            ease: 'power2.in',
            onComplete: () => {
                if (!isSliderHovered.value) {
                    gsap.set(sliderPrevBtn.value, { display: 'none' })
                }
            }
        })
        gsap.to(sliderNextBtn.value, {
            opacity: 0,
            x: 20,
            scale: 0.8,
            duration: 0.3,
            ease: 'power2.in',
            onComplete: () => {
                if (!isSliderHovered.value) {
                    gsap.set(sliderNextBtn.value, { display: 'none' })
                }
            }
        })
    }
}


// const initInfiniteSlider = () => {
//     const track = sliderTrackRef.value
//     if (!track) return

//     Array.from(track.children).forEach(card => {
//         const clone = card.cloneNode(true)
//         clone.setAttribute('aria-hidden', 'true')
//         track.appendChild(clone)
//     })

//     const totalWidth = track.scrollWidth / 2

//     sliderTween = gsap.to(track, {
//         x: `-=${totalWidth}`,
//         duration: 30,
//         ease: 'none',
//         repeat: -1,
//         modifiers: {
//             x: gsap.utils.unitize(x => parseFloat(x) % totalWidth),
//         },
//     })

//     track.addEventListener('mouseenter', () => sliderTween.pause())
//     track.addEventListener('mouseleave', () => sliderTween.resume())
// }

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
        trigger: bentoGridRef.value,
        start: 'bottom top',
        onEnter: () => {
            if (isNavbarVisible.value) return
            isNavbarVisible.value = true
            gsap.to(navbarContainerRef.value, {
                opacity: 1,
                y: 0,
                pointerEvents: 'auto',
                duration: 0.45,
                ease: 'power3.out',
            })
        },
        onLeaveBack: () => {
            if (!isNavbarVisible.value) return
            isNavbarVisible.value = false
            gsap.to(navbarContainerRef.value, {
                opacity: 0,
                y: -16,
                pointerEvents: 'none',
                duration: 0.3,
                ease: 'power2.in',
            })
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
</script>

<template>
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

    <div class="bg-[#dedede]">
        <div ref="bentoGridRef" class="mt-6 bg-[#ffffff]">
            <div class="w-full max-w-[1600px] mx-auto px-4">
                <div class="grid grid-cols-12 gap-4">

                    <!-- Col 1-3: Welcome -->
                    <div class="col-span-3 bg-[#fce109] rounded-2xl p-4 flex flex-col relative overflow-hidden h-max-[400px]">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-md font-medium text-black/70">Welcome to</p>
                                <h1 class="font-extrabold text-4xl text-black leading-tight tracking-tighter">SUPPLAYBOX</h1>
                            </div>
                            <img src="/public/assets/image/banner/arrow-black-right.svg" class="w-7 h-7" alt="" />
                        </div>
                        <p class="text-2xl md:text-3xl font-light text-black mt-1">Hi Robert,</p>
                        <p class="text-md md:text-sm text-black/80 mt-2 max-w-[55%] leading-relaxed flex-1">Most people freeze at the starting line. Don't hesitate. Just give it a shot.</p>
                        <div>
                            <button class="w-fit z-10 bg-[#4dfa03] hover:bg-green-700 border border-black font-extrabold text-2xl rounded-full px-5 flex items-center transition-all whitespace-nowrap">
                                FREE SKETCH!
                                <ChevronRightIcon class="w-4 h-4" />
                            </button>
                            <img src="/public/assets/image/banner/card-supplaybox-animated.svg" alt="mascot" class="absolute right-1 -bottom-4 w-[50%] max-w-[300px] object-contain pointer-events-none z-20" />
                        </div>
                    </div>

                    <!-- Col 4-5: Design & Illustration Service (row-span-2) -->
                    <div class="col-span-2 row-span-2 bg-gray-200 border border-black rounded-2xl flex flex-col overflow-hidden">
                        <div class="flex justify-center items-center p-6">
                            <img src="/public/assets/image/banner/card-service-animated2.svg" class="w-full h-full" alt="" />
                        </div>
                        <div class="bg-black rounded-tl-2xl rounded-tr-2xl">
                            <h3 class="font-bold text-white px-4 py-1 text-lg">Design Service</h3>
                            <div class="rounded-t-2xl bg-gray-200">
                                <p class="text-[9px] md:text-[10px] text-gray-800 leading-relaxed px-4 py-2 text-justify">
                                    Brand Identity / Logo Design / Poster Design / Packaging Design / Social 
                                    Media Design / Infographic Design / Editorial Design / Book Design
                                </p>
                                <div class="bg-black rounded-tl-2xl rounded-tr-2xl">
                                    <h3 class="font-bold text-white px-4 py-1 text-lg">Illustration Service</h3>
                                    <div class="rounded-t-2xl bg-gray-200">
                                        <p class="text-[9px] md:text-[10px] text-gray-800 leading-relaxed px-4 pt-2 text-justify">
                                            2D Illustration / Environmental Design Game Design / Character design / 
                                            Mascot Illustration / Advertising Illustration
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pb-1 flex justify-center p-2">
                            <img src="/public/assets/image/banner/arrow-black-bottom.svg" alt="" class="w-10 h-10" />
                        </div>
                        <div class="flex-1 min-h-[40px] overflow-hidden">
                            <img src="/public/assets/image/banner/card-service-animated.svg" alt="service illustration" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Col 6-7: Teepublic -->
                    <div class="col-span-2 bg-[#4c60d8] rounded-2xl flex flex-col">
                        <div class="px-6 pt-4 flex justify-between items-center">
                            <div class="tracking-wider flex items-center gap-1">
                                <img src="/public/assets/image/banner/card-teepublic-icon.svg" alt="" class="w-6 h-6" />
                                <span class="text-white font-bold text-base">TEEPUBLIC</span>
                            </div>
                            <img src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="w-5 h-5" />
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <img src="/public/assets/image/banner/card-teepublic-animated.svg" alt="teepublic" class="w-full h-full object-cover" />
                        </div>
                        <div class="px-6 pb-4">
                            <p class="font-bold text-white text-base">Unlimited<br />Prints.</p>
                        </div>
                    </div>

                    <!-- Col 8-10: Behance (col-span-3) -->
                    <div class="col-span-3 bg-black rounded-2xl flex flex-col overflow-hidden">
                        <div class="px-6 pt-4 flex justify-between items-start">
                            <div>
                                <p class="font-bold text-white text-4xl leading-tight">Bē</p>
                                <p class="text-[12px] text-white leading-tight">More<br />Professional<br />Portofolio</p>
                            </div>
                            <img src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="w-5 h-5" />
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <img src="/public/assets/image/banner/card-be-animated.svg" alt="behance" class="w-full h-full object-cover object-left" />
                        </div>
                    </div>

                    <!-- Col 11-12: Icons (atas) + Upwork (bawah) + Amidst text -->
                    <div class="col-span-2 row-span-2 flex flex-col gap-3">
                        <div class="flex justify-between items-center gap-2 pr-1 pt-1">
                            <ShoppingCartIcon class="w-8 h-8 text-green-500 cursor-pointer hover:text-green-600 transition-colors" />
                            <span class="border-2 border-black rounded-full px-4 py-0 text-black text-base font-medium text-center whitespace-nowrap">$100</span>
                            <div class="w-7 h-7 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center">
                                <img src="" alt="user" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <div class="bg-black rounded-2xl flex flex-col overflow-hidden">
                            <div class="px-6 pt-4 relative text-nowrap">
                                <p class="text-lg text-white">New Seller</p>
                                <p class="text-white text-lg">on <b class="font-['Archivo_Black',sans-serif]">upwork</b></p>
                                <img src="/public/assets/image/banner/arrow-white-right.svg" alt="" class="absolute top-4 right-6 w-5 h-5" />
                            </div>
                            <div class="flex-1 flex flex-col justify-end overflow-hidden">
                                <div class="h-[260px] overflow-hidden">
                                    <img src="/public/assets/image/banner/card-upwork-animated.svg" alt="upwork" class="w-full h-full object-cover object-bottom" />
                                </div>
                                <div class="rounded-t-2xl bg-[#268a00] px-6 py-3 -mt-[60px] relative z-10">
                                    <p class="text-white text-[16px] font-extrabold leading-tight">Enjoy many Bonuses<br />and Discounts<br />on this Platform!</p>
                                    <p class="text-[14px] text-white mt-2">Help us<br />to grow even more :)</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-3 flex flex-col justify-center text-lg">
                            <p class="text-[#cccccc]">Amidst</p>
                            <p class="text-[#b3b3b3] font-medium italic">the noise</p>
                            <p class="text-[#cccccc]">and</p>
                            <p class="text-[#b3b3b3] font-medium italic">visual</p>
                            <p class="text-[#b3b3b3] font-medium italic">disruption.</p>
                            <p class="text-[#108a00] font-medium">it's not</p>
                            <p class="text-[#108a00] font-medium">about</p>
                            <p class="text-[#cccccc] ">being <span class="text-[#108a00] font-medium">fast</span></p>
                            <p class="text-[#cccccc] ">and <span class="text-[#108a00] font-medium">empty.</span></p>
                            <p class="text-[#cccccc]  mt-0.5">but...<span class="inline-flex items-center justify-center w-4 h-4 ml-1 align-middle"><img src="/public/assets/image/banner/arrow-black-bottom.svg" alt="Down Arrow" class="w-3 h-3"></span></p>
                        </div>
                    </div>

                    <!-- ROW 2 -->

                    <!-- Col 1-3: Pinterest + TikTok + Starter Pack -->
                    <div class="col-span-3 grid grid-cols-2 grid-rows-2 gap-4 h-[420px]">
                        <div class="row-span-2 bg-[#E60023] rounded-2xl p-3 flex flex-col relative overflow-hidden">
                            <div class="flex justify-between items-start pb-2">
                                <img src="/public/assets/image/banner/card-path-logo.svg" class="w-8 h-8" alt="" />
                                <img src="/public/assets/image/banner/arrow-white-right.svg" class="w-5 h-5" alt="" />
                            </div>
                            <p class="text-white text-sm mt-1 leading-relaxed relative z-10">Let's <b class="font-black italic">CONNECT</b> on Pinterest, Share some pins, and dive into our <b class="font-black italic">Portfolio</b> + inspo boards.</p>
                            <div class="absolute bottom-0 left-0 right-0 h-[40%] overflow-hidden">
                                <img src="/public/assets/image/banner/card-path-animated.svg" alt="pinterest" class="w-full h-full object-cover object-top" />
                            </div>
                        </div>

                        <div class="bg-white border border-gray-800 rounded-2xl p-2 relative overflow-hidden">
                            <div class="flex justify-between items-start px-1 pt-1">
                                <img src="/public/assets/image/banner/card-tiktok-logo.svg" class="w-8 h-8" alt="TikTok" />
                                <img src="/public/assets/image/banner/arrow-black-right.svg" alt="TikTok" class="w-5 h-5" />
                            </div>
                            <p class="text-[9px] max-w-[130px] p-1 font-semibold">Some memes for entertainment, and behind the scenes of our routine.</p>
                            <div class="absolute -bottom-1 left-0 right-0 h-[45%] overflow-hidden">
                                <img src="/public/assets/image/banner/card-tiktok-animated.svg" alt="tiktok" class="w-full h-full object-cover object-top" />
                            </div>
                        </div>

                        <div class="bg-gray-200 flex flex-col gap-1 rounded-2xl p-3 relative overflow-hidden">
                            <div>
                                <p class="text-[10px] font-bold text-black">Starter Pack<br />Design.</p>
                                <div class="flex items-center pt-2 relative">
                                    <span class="absolute left-0 w-5 h-5 rounded-full bg-[#34bf72] border border-black"></span>
                                    <span class="absolute left-2 w-5 h-5 rounded-full bg-[#fec200] border border-black"></span>
                                    <span class="absolute left-4 w-5 h-5 rounded-full bg-[#3bffff] border border-black"></span>
                                    <span class="absolute left-6 w-5 h-5 rounded-full bg-[#fa191e] border border-black"></span>
                                    <span class="absolute left-10 text-[9px] text-gray-400">+2 More</span>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <p class="text-[9px] leading-relaxed">Canva/<br />Shutterstock/<br />iconscout dll</p>
                            </div>
                        </div>
                    </div>

                    <!-- Col 4-7: Fiverr + Our Product + Our Team -->
                    <div class="col-span-5 flex flex-col gap-4">
                        <div class="bg-[#f2ecea] rounded-2xl p-2 relative overflow-hidden h-[150px]">
                            <div class="flex justify-between items-start gap-2 relative z-10">
                                <img src="/public/assets/image/banner/arrow-black-left.svg" alt="" class="p-2">
                                <div class="flex flex-col items-end text-[#0b3117] text-2xl">
                                    <p class="">Top Rated</p>
                                    <p class="">Badge on <em class="font-bold">fiverr</em>.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 h-[90%] w-[50%] overflow-hidden">
                                <img src="/public/assets/image/banner/card-toprated-animated.svg" alt="fiverr" class="w-full h-full object-cover object-top" />
                            </div>
                            <img src="/public/assets/image/banner/card-fiver-badge.svg" alt="Fiverr Badge" class="absolute bottom-8 right-32 w-10 h-10" />
                            <div class="absolute bottom-1 right-2 text-right text-[12px] italic text-[#0b3117]">
                                <span class="block">For secure</span>
                                <span class="block">transactions!</span>
                            </div>
                        </div>

                        <!-- Our Product + Our Team -->
                        <div class="grid grid-cols-12 gap-4 flex-1 max-h-[260px]">
                            <div class="col-span-5 bg-[#fee100] rounded-2xl p-2 relative overflow-hidden">
                                <div class="absolute left-1/2 bottom-0 -translate-x-1/2 w-0 h-0 z-0 border-l-[260px] border-r-[260px] border-b-[90px] border-l-transparent border-r-transparent border-b-[#c0f901]"></div>
                                <p class="font-extrabold text-black text-2xl relative z-20 px-2">Our<br />Product</p>
                                <div class="flex-1 flex items-center justify-center py-1 relative z-10">
                                    <img src="/public/assets/image/banner/card-ourproduct-animated.svg" alt="product" class="w-[55%] h-[55%] object-contain" />
                                </div>
                                <div class="text-center relative z-10">
                                    <img src="/public/assets/image/banner/arrow-black-bottom.svg" alt="Arrow" class="inline w-10 h-10" />
                                </div>
                            </div>

                            <div class="col-span-7 bg-[#abdec9] rounded-2xl relative overflow-hidden">
                                <div class="flex-1 flex items-center justify-center relative">
                                    <img src="/public/assets/image/banner/card-ourteam-animated.svg" alt="team" class="w-[80%] h-[50%] object-cover" />
                                </div>
                                <div class="absolute bottom-6 left-2 right-2">
                                    <p class="font-['Archivo_Black',sans-serif] text-white text-sm leading-tight">Meet<br />Our Team</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

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

    <div class="bg-gradient-to-b from-black to-gray-500 text-white">
        <div class="px-10 md:px-40 pt-12 pb-10">
            <h1 class="text-6xl md:text-7xl font-bold leading-tight">Perception</h1>
            <p class="text-6xl md:text-7xl leading-tight">And <b class="font-bold">Connection.</b></p>
            <p class="text-gray-400 mt-4 text-lg max-w-xl">We craft visual identities that make people feel something. Real brands. Real stories.</p>
        </div>

        <!-- Slider with Navigation -->
        <div 
            ref="sliderContainerRef" 
            class="relative w-full overflow-hidden py-4 group"
            
        >
            <!-- Previous Button -->
            <button
                ref="sliderPrevBtn"
                class="absolute left-8 top-1/2 -translate-y-1/2 z-20 bg-[#b4f000] backdrop-blur-sm text-white rounded-full p-3 shadow-lg hover:bg-green-700 transition-all duration-300 opacity-0 -translate-x-5 scale-90 hidden"
                style="display: none;"
            >
                <ChevronLeftIcon class="w-6 h-6" />
            </button>

            <!-- Next Button -->
            <button
                ref="sliderNextBtn"
                class="absolute right-8 top-1/2 -translate-y-1/2 z-20 bg-[#b4f000] backdrop-blur-sm text-white rounded-full p-3 shadow-lg hover:bg-green transition-all duration-300 opacity-0 translate-x-5 scale-90 hidden"
                style="display: none;"
            >
                <ChevronRightIcon class="w-6 h-6" />
            </button>

            <!-- Slider Track -->
            <div ref="sliderTrackRef" class="flex gap-5 w-max will-change-transform px-6 py-2 cursor-grab active:cursor-grabbing">
                <div v-for="item in testimonials" :key="item.id" class="flex-shrink-0 w-[500px] bg-white border border-white/10 rounded-2xl p-6 flex flex-col gap-1 cursor-default transition-all duration-300 hover:border-white/30 hover:-translate-y-1 backdrop-blur-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold text-black flex-shrink-0" :style="{ backgroundColor: item.avatarColor }">{{ item.avatar }}</div>
                        <div>
                            <p class="text-sm font-semibold text-black">{{ item.name }}</p>
                            <p class="text-xs text-gray-700">{{ item.role }} · {{ item.company }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mb-2">
                        <StarSolid v-for="i in item.rating" :key="i" class="w-4 h-4 text-yellow-400" />
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed">"{{ item.text }}"</p>
                </div>
            </div>
        </div>

        <div class="px-10 md:px-20 pt-10 pb-14">
            <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-6 opacity-40">
                <svg class="h-7 fill-white" viewBox="0 0 80 30"><path d="M7.2 23.4L0 6.6h5.2l4.5 11.8L22.8 6.6H80l-58.4 16.1c-3.1.8-5.4.9-7.2.7-2-.2-5.3-1.4-7.2 0z"/></svg>
                <svg class="h-7 fill-white" viewBox="0 0 80 40"><path d="M40 4L20 38h10l10-18 10 18h10L40 4z"/><rect x="15" y="30" width="50" height="5" rx="1"/></svg>
                <svg class="h-8 fill-white" viewBox="0 0 814 1000"><path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 454.9 0 306.3 0 164.8 0 67.7 54.5 16.4 107.4 16.4c55.3 0 97.1 38.3 137.5 38.3 38.9 0 88.1-41.4 155.3-41.4 24.6 0 108.8 2.6 168.9 80.8zm-172.3-117.1c-3.9-24.5-11.1-49.4-28.2-71.3-23.2-29.5-57.4-50.9-91.4-50.9-2.6 0-5.2.3-7.8.6 2.6 27.5 13 52.3 28.2 72.1 18.8 24.5 52.4 44.7 99.2 49.5z"/></svg>
                <svg class="h-7 fill-white" viewBox="0 0 272 92"><path d="M115.8 47.2c0 15.6-12.2 27.1-27.1 27.1S61.6 62.8 61.6 47.2C61.6 31.5 73.8 20 88.7 20s27.1 11.6 27.1 27.2zm-11.9 0c0-9.7-7.1-16.4-15.2-16.4s-15.2 6.7-15.2 16.4 7.1 16.4 15.2 16.4 15.2-6.8 15.2-16.4zM168 47.2c0 15.6-12.2 27.1-27.1 27.1s-27.1-11.5-27.1-27.1c0-15.6 12.1-27.2 27.1-27.2S168 31.5 168 47.2zm-11.9 0c0-9.7-7.1-16.4-15.2-16.4s-15.2 6.7-15.2 16.4 7.1 16.4 15.2 16.4 15.2-6.8 15.2-16.4zM219 21.5v49.5c0 20.3-12 28.6-26.2 28.6-13.4 0-21.4-9-24.4-16.3l10.3-4.3c1.9 4.5 6.5 9.8 14.2 9.8 9.3 0 15-5.7 15-16.5v-4.1h-.4c-2.7 3.4-8 6.3-14.6 6.3-13.9 0-26.6-12.1-26.6-27.6C166.3 31.2 179 20 192.9 20c6.6 0 12 2.9 14.6 6.1h.4V21.5H219zm-11 25.9c0-9.5-6.3-16.4-14.4-16.4-8.2 0-15 6.9-15 16.4 0 9.4 6.8 16.3 15 16.3 8.1 0 14.4-6.9 14.4-16.3zM233 3v70h-11.6V3H233zM271.7 56.5l9.2 6.1c-3 4.4-10.1 12-22.4 12-15.3 0-26.7-11.8-26.7-27.1 0-16.1 11.5-27.2 25.4-27.2 14 0 20.8 11.3 23 17.4l1.2 3.1-36 14.9c2.8 5.4 7 8.2 13 8.2s10.2-3 13.3-7.4zm-28.3-9.7l24.1-10c-1.3-3.3-5.3-5.6-10-5.6-6 0-14.4 5.3-14.1 15.6z"/><path d="M35.3 40.5V29.3h39.9c.4 2.1.6 4.5.6 7.2 0 8.9-2.4 19.9-10.3 27.8-7.6 8-17.4 12.2-30.2 12.2C16.1 76.5 0 60.9 0 41.2S16.1 5.9 35.3 5.9c13.1 0 22.4 5.1 29.4 11.8l-8.3 8.3c-5-4.7-11.8-8.3-21.1-8.3-17.2 0-30.7 13.9-30.7 31.3s13.4 31.3 30.7 31.3c11.2 0 17.5-4.5 21.6-8.6 3.3-3.3 5.5-8.1 6.3-14.6H35.3z"/></svg>
            </div>
        </div>
        <!-- featured section -->
        <div class="bg-black text-white py-12 rounded-3xl rounded-tr-3xl text-center">
            <div class="mx-auto px-10 justify-between flex gap-6">
                <img src="/assets/image/our-work.svg" alt="Featured Image" class="">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Featured</h2>
            </div>
            
            <OurWorkSection />
            
            <!-- section footer - POSISI KANAN -->
            <div class="flex justify-end items-center mt-8 opacity-70 px-10">
                <div class="flex items-center gap-6">
                    <!-- More Portfolio Link -->
                    <div class="flex items-center gap-2 group cursor-pointer">
                        <span class="text-white group-hover:text-[#b4f000] transition-colors duration-300">
                            More Portfolio
                        </span>
                        <img 
                            src="/assets/image/panah-porto.svg" 
                            alt="" 
                            class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1 group-hover:brightness-0 group-hover:saturate-100 group-hover:invert-[60%] group-hover:sepia-[100%] group-hover:hue-rotate-[60deg]"
                        >
                    </div>
                    
                    <!-- Social Icons -->
                    <div class="bg-[#333333] rounded-lg px-4 py-1 flex items-center gap-4">
                        <div class="social-icon-wrapper group">
                            <img 
                                src="/assets/image/pinterest-icon-porto.svg" 
                                alt="Pinterest" 
                                class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg] group-hover:brightness-[100%]"
                            >
                        </div>
                        
                        <div class="social-icon-wrapper group">
                            <img 
                                src="/assets/image/behance-icon-porto.svg" 
                                alt="Behance" 
                                class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg]"
                            >
                        </div>
                        
                        <div class="social-icon-wrapper group">
                            <img 
                                src="/assets/image/fiverr-icon-porto.svg" 
                                alt="Fiverr" 
                                class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg]"
                            >
                        </div>
                        
                        <div class="social-icon-wrapper group">
                            <img 
                                src="/assets/image/upwork-icon-porto.svg" 
                                alt="Upwork" 
                                class="h-6 object-cover transition-all duration-300 group-hover:scale-110 group-hover:brightness-0 group-hover:invert group-hover:sepia-0 group-hover:saturate-100 group-hover:hue-rotate-[60deg]"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="min-h-screen bg-gradient-to-b from-gray-200 to-white">
        <div ref="bannerRef" class="bg-white">
            <BannerCards />
        </div>

        <main>
            
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
                            <li>Brand Identity</li>
                            <li>Logo Design</li>
                            <li>Illustration</li>
                            <li>Packaging Design</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Connect</h4>
                        <p class="text-gray-400 mb-4">Let's CONNECT on Pinterest</p>
                        <button class="bg-white text-black px-4 py-2 rounded-lg hover:bg-gray-200 transition">Follow Us</button>
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
.social-icon-wrapper:hover img {
  filter: brightness(0) saturate(100%) invert(78%) sepia(54%) saturate(1234%) hue-rotate(38deg) brightness(101%) contrast(101%);
}

.hover-bright-green:hover {
  filter: brightness(0) saturate(100%) invert(89%) sepia(23%) saturate(1397%) hue-rotate(36deg) brightness(98%) contrast(94%);
}

[class*="col-span"] {
    transition: box-shadow 0.2s ease;
    will-change: transform, box-shadow;
    cursor: pointer;
    backface-visibility: hidden;
    -webkit-font-smoothing: subpixel-antialiased;
}

[class*="col-span"]:hover {
    z-index: 20;
}

img {
    transition: transform 0.3s ease;
    will-change: transform;
}

p, h1, h2, h3, h4, span, button {
    backface-visibility: hidden;
    -webkit-font-smoothing: subpixel-antialiased;
}

.bg-black.rounded-2xl,
.bg-\\[\\#fce109\\],
.bg-\\[\\#4c60d8\\],
.bg-\\[\\#E60023\\],
.bg-\\[\\#f2ecea\\] {
    transition: all 0.2s ease;
}

.overflow-hidden {
    -webkit-mask-image: -webkit-radial-gradient(white, black);
}

button:active {
    transform: scale(0.95);
}

.cursor-grab {
    cursor: grab;
}

.cursor-grabbing {
    cursor: grabbing;
}

.slider-nav-btn {
    transition: all 0.3s ease;
}

.slider-nav-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

@keyframes pulse-ring {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.5);
    }
    100% {
        box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
    }
}

.group:hover .slider-nav-btn {
    animation: pulse-ring 0.6s ease-out;
}
</style>