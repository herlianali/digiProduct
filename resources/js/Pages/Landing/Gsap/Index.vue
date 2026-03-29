<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import BannerCards from './Components/BannerCards.vue'
import OurWorkSection from './Components/OurWorkSection.vue'
import ProductSection from './Components/ProductSection.vue'
import RackDivider from './Components/RackDivider.vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import ArrowUpLeft from '@/Components/Icons/ArrowUpLeft.vue'
import ArrowUpRight from '@/Components/Icons/ArrowUpRight.vue'
import { ShoppingCartIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import {
    StarIcon as StarSolid,
    ArrowUpLeftIcon as ArrowUpLeftSolid,
    ArrowUpRightIcon as ArrowUpRightSolid,
    ArrowDownIcon as ArrowDownSolid,
    CheckBadgeIcon as CheckBadgeSolid,
} from '@heroicons/vue/24/solid'

gsap.registerPlugin(ScrollTrigger)

const bannerRef = ref(null)
const navbarRef = ref(null)
const navbarContainerRef = ref(null)
const isNavbarSticky = ref(false)
const sliderTrackRef = ref(null)
let scrollTriggerInstance = null
let sliderTween = null

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
})

onUnmounted(() => {
    if (scrollTriggerInstance) scrollTriggerInstance.kill()
    if (sliderTween) sliderTween.kill()
    ScrollTrigger.getAll().forEach(t => t.kill())
})

const initInfiniteSlider = () => {
    const track = sliderTrackRef.value
    if (!track) return

    const cards = Array.from(track.children)
    cards.forEach(card => {
        const clone = card.cloneNode(true)
        clone.setAttribute('aria-hidden', 'true')
        track.appendChild(clone)
    })

    const totalWidth = track.scrollWidth / 2

    sliderTween = gsap.to(track, {
        x: `-=${totalWidth}`,
        duration: 30,
        ease: 'none',
        repeat: -1,
        modifiers: {
            x: gsap.utils.unitize(x => parseFloat(x) % totalWidth)
        }
    })

    track.addEventListener('mouseenter', () => sliderTween.pause())
    track.addEventListener('mouseleave', () => sliderTween.resume())
}

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
                        position: 'fixed', top: '20px', right: '20px',
                        left: 'auto', width: 'auto',
                        maxWidth: 'calc(100vw - 40px)', overflow: 'visible',
                    })
                    gsap.to(navbarContainerRef.value, {
                        borderRadius: '50px',
                        boxShadow: '0 10px 30px -5px rgba(0,0,0,0.2)',
                        backgroundColor: 'rgba(255,255,255,0.98)',
                        backdropFilter: 'blur(10px)',
                        padding: '0 20px',
                        border: '1px solid rgba(255,255,255,0.2)',
                        duration: 0.4, ease: 'power2.out',
                    })
                    gsap.to('.nav-button', { scale: 0.9, duration: 0.3 })
                }
            } else {
                if (isNavbarSticky.value) {
                    gsap.to(navbarContainerRef.value, {
                        position: 'relative', top: 'auto', right: 'auto', left: 0,
                        width: '100%', maxWidth: '100%', borderRadius: '0',
                        boxShadow: 'none', backgroundColor: 'white',
                        backdropFilter: 'none', padding: '0', border: 'none',
                        overflow: 'visible', duration: 0.4, ease: 'power2.out',
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
        scrollTrigger: { trigger: '.section-title', start: 'top 80%', toggleActions: 'play none none reverse' },
        y: 50, opacity: 0, duration: 1, stagger: 0.2
    })
}

const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId)
    if (element) {
        const offset = isNavbarSticky.value ? 100 : 0
        window.scrollTo({ top: element.getBoundingClientRect().top + window.scrollY - offset, behavior: 'smooth' })
    }
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-gray-200 to-white">

        <!-- Navbar (tetap sama seperti script asli) -->
        <section class="relative bg-white text-white w-full">
            <div ref="navbarContainerRef" class="z-[1000] transition-all duration-300">
                <nav ref="navbarRef" class="w-full">
                    <div class="flex justify-between items-center max-w-[1200px] mx-auto px-8 py-4">
                        <div class="hidden md:flex gap-8 items-center">
                            <a href="#" class="text-black font-medium hover:text-emerald-500 transition-colors duration-300 no-underline" @click.prevent="scrollToSection('about-us')">About Us</a>
                            <a href="#" class="text-black font-medium hover:text-emerald-500 transition-colors duration-300 no-underline" @click.prevent="scrollToSection('platform')">Platform</a>
                            <a href="#" class="text-black font-medium hover:text-emerald-500 transition-colors duration-300 no-underline" @click.prevent="scrollToSection('shop')">Shop</a>
                            <a href="#" class="text-black font-medium hover:text-emerald-500 transition-colors duration-300 no-underline" @click.prevent="scrollToSection('contact')">Contact</a>
                        </div>
                        <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                            <button class="bg-green-500 hover:bg-green-700 font-bold text-black px-4 py-1 rounded-full nav-button flex items-center gap-1 transition-colors duration-200">
                                FREE SKETCH!
                                <ChevronRightIcon class="w-5 h-5 font-bold text-black inline-block" />
                            </button>
                            <ShoppingCartIcon class="w-6 h-6 text-green-700 cursor-pointer" />
                            <span class="hidden md:inline-block border border-gray-600 rounded-full px-6 py-1 bg-white/10 backdrop-blur-sm text-black text-sm">$100</span>
                            <button class="md:hidden">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </section>

        <!-- HERO SECTION - BENTO GRID menggunakan Tailwind CSS (tanpa custom CSS) -->
        <div class="bg-gradient-to-b from-[#dedede] to-[#f2f2f2] px-3 pt-3 pb-0">
            <div class="max-w-[1280px] mx-auto">
                <!-- Grid 5 kolom, 2 baris dengan gap 2.5 (10px) -->
                <div class="grid grid-cols-12 gap-2.5 auto-rows-min">

                    <!-- Kolom 1-2: Welcome (span 3 kolom di grid 12) + baris 1 -->
                    <div class="col-span-3 row-span-1 bg-[#fce109] rounded-2xl p-4 md:p-5 flex flex-col">
                        <div class="flex justify-between items-start flex-1">
                            <div class="flex-1">
                                <p class="text-xs font-medium text-black/70">Welcome to</p>
                                <h1 class="font-['Archivo_Black',sans-serif] text-3xl md:text-4xl text-black leading-tight tracking-tighter">SUPPLAYBOX</h1>
                            </div>
                            <div class="w-[90px] h-[100px] md:w-[110px] md:h-[130px] flex items-end justify-center">
                                <img src="" alt="mascot" class="w-full h-full object-contain object-bottom" />
                            </div>
                        </div>
                        <p class="text-2xl md:text-3xl font-light text-black mt-1">Hi Robert,</p>
                        <p class="text-xs md:text-sm text-black/80 mt-1 max-w-[210px] leading-relaxed">Most people freeze at the starting line. Don't hesitate Just give it a shot,</p>
                        <button class="mt-3 w-fit bg-green-600 hover:bg-green-700 text-white font-extrabold text-xs rounded-full px-4 py-1.5 flex items-center gap-1 transition-all">
                            FREE SKETCH!
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Kolom 3: Merged Card (800+ & Services) - span 2 kolom, row 1-2 -->
                    <div class="col-span-2 row-span-2 bg-black rounded-2xl flex flex-col overflow-hidden">
                        <!-- Green 800+ area -->
                        <div class="bg-[#0bfd09] px-4 pt-4 pb-0 relative">
                            <div class="text-5xl md:text-6xl font-['Archivo_Black',sans-serif] text-black leading-none tracking-tighter">800+</div>
                            <!-- Wave effect dengan SVG inline -->
                            <svg class="w-full h-12 mt-2" viewBox="0 0 260 50" preserveAspectRatio="none">
                                <circle cx="10" cy="45" r="38" fill="#18ff14"/>
                                <circle cx="55" cy="42" r="38" fill="#18ff14"/>
                                <circle cx="100" cy="45" r="38" fill="#18ff14"/>
                                <circle cx="145" cy="42" r="38" fill="#18ff14"/>
                                <circle cx="190" cy="45" r="38" fill="#18ff14"/>
                                <circle cx="235" cy="42" r="38" fill="#18ff14"/>
                                <rect x="0" y="30" width="260" height="20" fill="black"/>
                            </svg>
                        </div>
                        <!-- Badge -->
                        <div class="bg-black/10 px-4 py-2 flex items-center gap-2">
                            <CheckBadgeSolid class="text-yellow-400 w-5 h-5 md:w-6 md:h-6" />
                            <div class="text-xs text-black"><span class="font-bold">Project</span> <b class="font-black">Finished</b></div>
                        </div>
                        <!-- Design Service -->
                        <div class="px-4 py-3 border-b border-gray-800">
                            <h3 class="font-['Archivo_Black',sans-serif] text-white text-sm md:text-base">Design Service</h3>
                            <p class="text-[10px] md:text-[11px] text-gray-500 leading-relaxed mt-1">Brand Identity / Logo Design / Poster<br>Design / Packaging Design / Social<br>Media Design / Infographic Design /<br>Editorial Design / Book Design</p>
                        </div>
                        <!-- Illustration Service -->
                        <div class="px-4 py-3 border-b border-gray-800">
                            <h3 class="font-['Archivo_Black',sans-serif] text-white text-sm md:text-base">Illustration Service</h3>
                            <p class="text-[10px] md:text-[11px] text-gray-500 leading-relaxed mt-1">2D Illustration / Environmental Design<br>Game Design / Character design /<br>Mascot Illustration / Advertising<br>Illustration</p>
                        </div>
                        <!-- Arrow Down -->
                        <div class="py-2 flex justify-center">
                            <ArrowDownSolid class="text-white w-5 h-5" />
                        </div>
                        <!-- Bottom Image -->
                        <div class="flex-1 min-h-[50px] overflow-hidden">
                            <img src="" alt="service illustration" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Kolom 4: Teepublic (span 2) -->
                    <div class="col-span-2 row-span-1 bg-[#2ecfcf] rounded-2xl flex flex-col">
                        <div class="px-3 pt-3 flex justify-between items-center">
                            <span class="text-white text-[10px] font-bold tracking-wider flex items-center gap-1"><span class="w-1.5 h-1.5 bg-white rounded-full"></span>TEEPUBLIC</span>
                            <ArrowUpRightSolid class="text-white w-4 h-4" />
                        </div>
                        <div class="flex-1 overflow-hidden min-h-[100px]">
                            <img src="" alt="teepublic" class="w-full h-full object-cover" />
                        </div>
                        <div class="px-3 pb-3">
                            <p class="font-['Archivo_Black',sans-serif] text-white text-base md:text-lg leading-tight">Unlimited<br>Prints.</p>
                        </div>
                    </div>

                    <!-- Kolom 5: Behance (span 2) -->
                    <div class="col-span-2 row-span-1 bg-black rounded-2xl flex flex-col">
                        <div class="px-3 pt-3 flex justify-between items-start">
                            <div>
                                <p class="font-['Archivo_Black',sans-serif] text-white text-2xl md:text-3xl">Bē</p>
                                <p class="text-[9px] text-gray-500 leading-tight">More<br>Professional<br>Portofolio</p>
                            </div>
                            <ArrowUpRightSolid class="text-white w-4 h-4" />
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <img src="" alt="behance" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Kolom 6: Upwork (span 2) -->
                    <div class="col-span-2 row-span-1 bg-black rounded-2xl flex flex-col">
                        <div class="flex-1 px-3 pt-3 relative">
                            <p class="text-[10px] text-gray-500 uppercase">New Seller</p>
                            <p class="text-white text-lg font-light">on <b class="font-['Archivo_Black',sans-serif]">upwork</b></p>
                            <ArrowUpRightSolid class="absolute top-3 right-3 text-white w-4 h-4" />
                            <div class="h-[90px] mt-2 overflow-hidden">
                                <img src="" alt="upwork" class="w-full h-full object-cover object-top" />
                            </div>
                        </div>
                        <div class="bg-green-600 px-3 py-2">
                            <p class="text-white text-xs font-extrabold leading-tight">Enjoy many Bonuses<br>and Discounts<br>on this Platform!</p>
                            <p class="text-[9px] text-white/80 mt-1">Help us<br>to grow even more :)</p>
                        </div>
                    </div>

                    <!-- Row 2 - Kolom 1: Pinterest + TikTok + Starter (stack) -->
                    <div class="col-span-3 row-span-1 grid grid-cols-2 grid-rows-2 gap-2.5">
                        <!-- Pinterest - span 2 baris di kolom 1 -->
                        <div class="row-span-2 bg-[#E60023] rounded-2xl p-3 flex flex-col relative overflow-hidden">
                            <div class="flex justify-between items-start">
                                <svg class="w-7 h-7 fill-white" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.934 1.407-5.96 1.407-5.96s-.359-.718-.359-1.779c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.768 1.518 1.69 0 1.03-.656 2.57-.994 3.997-.283 1.195.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.739.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.27 1.042-1 2.342-1.488 3.137C9.594 23.316 10.779 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
                                <ArrowUpRightSolid class="text-white w-4 h-4" />
                            </div>
                            <p class="text-white text-xs mt-2 leading-relaxed relative z-10">Let's <b class="font-black italic">CONNECT</b> on Pinterest, Share some pins, and dive into our <b class="font-black italic">Portfolio</b> + inspo boards.</p>
                            <div class="absolute bottom-0 left-0 right-0 h-[45%] overflow-hidden opacity-70">
                                <img src="" alt="pinterest" class="w-full h-full object-cover object-top" />
                            </div>
                        </div>
                        <!-- TikTok Card -->
                        <div class="bg-white border border-gray-200 rounded-2xl p-2.5 relative overflow-hidden">
                            <div class="flex justify-between items-start">
                                <svg class="w-6 h-6 fill-black" viewBox="0 0 24 24"><path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-2.89 2.89 2.896 2.896 0 0 1-2.89-2.89c0-1.6 1.3-2.89 2.89-2.89c.306 0 .594.05.867.14v-3.5a6.329 6.329 0 0 0-5.44 6.25c0 3.5 2.85 6.35 6.35 6.35c3.5 0 6.35-2.85 6.35-6.35V8.465a8.297 8.297 0 0 0 4.85 1.55V6.686a4.85 4.85 0 0 1-2.86-.9z"/></svg>
                                <ArrowUpRightSolid class="text-black w-3.5 h-3.5" />
                            </div>
                            <p class="text-[10px] text-gray-700 mt-1 leading-tight max-w-[100px]">Some memes for entertainment, and behind the scenes of our routine.</p>
                            <div class="absolute bottom-0 right-0 w-[45%] h-[50%] overflow-hidden">
                                <img src="" alt="tiktok" class="w-full h-full object-cover object-top" />
                            </div>
                        </div>
                        <!-- Starter Pack -->
                        <div class="bg-gray-100 border border-gray-300 rounded-2xl p-2.5">
                            <p class="text-xs font-bold text-black">Starter Pack<br>Design.</p>
                            <div class="flex gap-1.5 my-2">
                                <span class="w-4 h-4 rounded-full bg-red-600"></span>
                                <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                                <span class="w-4 h-4 rounded-full bg-yellow-400 border border-gray-300"></span>
                                <span class="text-[9px] font-bold text-gray-500">+2 More</span>
                            </div>
                            <p class="text-[9px] text-gray-500 leading-relaxed">Canva/<br>Shutterstock/<br>iconscout dll</p>
                        </div>
                    </div>

                    <!-- Kolom 4-5: Fiverr + Product + Team (span 4 kolom) -->
                    <div class="col-span-4 row-span-1 flex flex-col gap-2.5">
                        <!-- Fiverr Card -->
                        <div class="bg-[#f2edea] rounded-2xl p-3 relative overflow-hidden h-[130px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-semibold text-gray-500">Top Rated</p>
                                    <p class="text-base font-black text-black">Badge on <em class="italic">fiverr</em>.</p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <ArrowUpRightSolid class="text-gray-600 w-3.5 h-3.5" />
                                    <span class="bg-orange-600 text-white text-[8px] font-bold px-2 py-0.5 rounded-full">TOP RATED</span>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 h-[75%] w-[45%] overflow-hidden">
                                <img src="" alt="fiverr" class="w-full h-full object-cover object-top" />
                            </div>
                            <div class="absolute bottom-2 right-3 text-right">
                                <span class="text-[8px] text-gray-500 block">For secure</span>
                                <span class="text-[8px] text-gray-500 block">transactions!</span>
                            </div>
                        </div>
                        <!-- Product + Team row -->
                        <div class="flex gap-2.5 flex-1">
                            <div class="flex-1 bg-[#fce109] rounded-2xl p-3 flex flex-col">
                                <p class="font-['Archivo_Black',sans-serif] text-black text-xl leading-tight">Our<br>Product</p>
                                <div class="flex-1 flex items-center justify-center py-2">
                                    <img src="" alt="product" class="h-12 w-auto object-contain" />
                                </div>
                                <div class="text-center">
                                    <ArrowDownSolid class="text-black w-4 h-4 inline" />
                                </div>
                            </div>
                            <div class="flex-1 bg-[#7c4d35] rounded-2xl relative overflow-hidden">
                                <img src="" alt="team" class="w-full h-full object-cover object-top" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <ArrowUpRightSolid class="absolute top-2 right-2 text-white w-4 h-4" />
                                <div class="absolute bottom-2 left-2 right-2">
                                    <p class="font-['Archivo_Black',sans-serif] text-white text-base leading-tight">Meet<br>Our Team</p>
                                    <div class="inline-flex items-center gap-1 bg-black/50 border border-white/20 rounded-full px-2 py-0.5 mt-1">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        <span class="text-[9px] text-white">RIN</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 6: Amidst Text (span 2) -->
                    <div class="col-span-2 row-span-1 bg-white rounded-2xl p-4 flex flex-col justify-center">
                        <p class="text-gray-300 text-lg md:text-xl leading-tight">Amidst</p>
                        <p class="font-['Archivo_Black',sans-serif] text-black text-xl md:text-2xl leading-tight italic">the noise</p>
                        <p class="text-gray-300 text-lg md:text-xl">and</p>
                        <p class="font-['Archivo_Black',sans-serif] text-black text-xl md:text-2xl leading-tight italic">visual</p>
                        <p class="font-['Archivo_Black',sans-serif] text-black text-xl md:text-2xl leading-tight italic">disruption.</p>
                        <p class="text-emerald-500 font-['Archivo_Black',sans-serif] text-base md:text-lg">it's not</p>
                        <p class="text-emerald-500 font-['Archivo_Black',sans-serif] text-base md:text-lg">about</p>
                        <p class="text-black text-sm">being <span class="text-emerald-500 font-['Archivo_Black',sans-serif]">fast</span></p>
                        <p class="text-black text-sm">and <span class="text-emerald-500 font-['Archivo_Black',sans-serif]">empty.</span></p>
                        <p class="text-gray-400 text-sm mt-1">but...<span class="inline-flex items-center justify-center w-5 h-5 bg-black rounded-full ml-1 align-middle"><ArrowDownSolid class="text-white w-3 h-3" /></span></p>
                    </div>
                </div>

                <!-- Rack Divider (tetap menggunakan komponen RackDivider) -->
                <div class="mt-2">
                    <RackDivider
                        :color="'#000000'"
                        :top-color="'#ffffff'"
                        :mid-radius="40"
                        :height="36"
                        :tab-height="28"
                        :notch-depth="28"
                        :tab-width="35"
                        :radius="8"
                        :responsive="true"
                        :manual-positions="[
                            { xPct: 3,  type: 'out' },
                            { xPct: 27, type: 'out' },
                            { xPct: 30, type: 'in'  },
                            { xPct: 40, type: 'out' },
                            { xPct: 43, type: 'in'  },
                            { xPct: 95, type: 'out' }
                        ]"
                    />
                </div>
            </div>
        </div>

        <!-- Hero + Testimonial Slider (tetap sama seperti script asli) -->
        <div class="bg-gradient-to-b from-black to-slate-800 text-white">
            <div class="px-10 md:px-40 pt-12 pb-10">
                <h1 class="text-6xl md:text-7xl font-bold leading-tight">Perception</h1>
                <p class="text-6xl md:text-7xl leading-tight">And <b class="font-bold">Connection.</b></p>
                <p class="text-gray-400 mt-4 text-lg max-w-xl">
                    We craft visual identities that make people feel something. Real brands. Real stories.
                </p>
            </div>

            <!-- Infinite Slider -->
            <div class="relative w-full overflow-hidden py-4" style="mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);">
                <div ref="sliderTrackRef" class="flex gap-5 w-max will-change-transform px-6 py-2">
                    <div v-for="item in testimonials" :key="item.id" class="flex-shrink-0 w-[300px] bg-white/5 border border-white/10 rounded-2xl p-6 flex flex-col gap-1 cursor-default transition-all duration-300 hover:border-white/30 hover:-translate-y-1 backdrop-blur-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" :style="{ backgroundColor: item.avatarColor }">
                                {{ item.avatar }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ item.name }}</p>
                                <p class="text-xs text-gray-500">{{ item.role }} · {{ item.company }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 mb-2">
                            <StarSolid v-for="i in item.rating" :key="i" class="w-4 h-4 text-yellow-400" />
                        </div>
                        <p class="text-sm text-gray-300 leading-relaxed">"{{ item.text }}"</p>
                    </div>
                </div>
            </div>

            <!-- Brand Logos Strip (tetap sama) -->
            <div class="px-10 md:px-20 pt-10 pb-14">
                <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-6 opacity-40">
                    <svg class="h-7 fill-white" viewBox="0 0 80 30"><path d="M7.2 23.4L0 6.6h5.2l4.5 11.8L22.8 6.6H80l-58.4 16.1c-3.1.8-5.4.9-7.2.7-2-.2-5.3-1.4-7.2 0z"/></svg>
                    <svg class="h-7 fill-white" viewBox="0 0 80 40"><path d="M40 4L20 38h10l10-18 10 18h10L40 4z"/><rect x="15" y="30" width="50" height="5" rx="1"/></svg>
                    <svg class="h-8 fill-white" viewBox="0 0 814 1000"><path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 454.9 0 306.3 0 164.8 0 67.7 54.5 16.4 107.4 16.4c55.3 0 97.1 38.3 137.5 38.3 38.9 0 88.1-41.4 155.3-41.4 24.6 0 108.8 2.6 168.9 80.8zm-172.3-117.1c-3.9-24.5-11.1-49.4-28.2-71.3-23.2-29.5-57.4-50.9-91.4-50.9-2.6 0-5.2.3-7.8.6 2.6 27.5 13 52.3 28.2 72.1 18.8 24.5 52.4 44.7 99.2 49.5z"/></svg>
                    <svg class="h-7 fill-white" viewBox="0 0 272 92"><path d="M115.8 47.2c0 15.6-12.2 27.1-27.1 27.1S61.6 62.8 61.6 47.2C61.6 31.5 73.8 20 88.7 20s27.1 11.6 27.1 27.2zm-11.9 0c0-9.7-7.1-16.4-15.2-16.4s-15.2 6.7-15.2 16.4 7.1 16.4 15.2 16.4 15.2-6.8 15.2-16.4zM168 47.2c0 15.6-12.2 27.1-27.1 27.1s-27.1-11.5-27.1-27.1c0-15.6 12.1-27.2 27.1-27.2S168 31.5 168 47.2zm-11.9 0c0-9.7-7.1-16.4-15.2-16.4s-15.2 6.7-15.2 16.4 7.1 16.4 15.2 16.4 15.2-6.8 15.2-16.4zM219 21.5v49.5c0 20.3-12 28.6-26.2 28.6-13.4 0-21.4-9-24.4-16.3l10.3-4.3c1.9 4.5 6.5 9.8 14.2 9.8 9.3 0 15-5.7 15-16.5v-4.1h-.4c-2.7 3.4-8 6.3-14.6 6.3-13.9 0-26.6-12.1-26.6-27.6C166.3 31.2 179 20 192.9 20c6.6 0 12 2.9 14.6 6.1h.4V21.5H219zm-11 25.9c0-9.5-6.3-16.4-14.4-16.4-8.2 0-15 6.9-15 16.4 0 9.4 6.8 16.3 15 16.3 8.1 0 14.4-6.9 14.4-16.3zM233 3v70h-11.6V3H233zM271.7 56.5l9.2 6.1c-3 4.4-10.1 12-22.4 12-15.3 0-26.7-11.8-26.7-27.1 0-16.1 11.5-27.2 25.4-27.2 14 0 20.8 11.3 23 17.4l1.2 3.1-36 14.9c2.8 5.4 7 8.2 13 8.2s10.2-3 13.3-7.4zm-28.3-9.7l24.1-10c-1.3-3.3-5.3-5.6-10-5.6-6 0-14.4 5.3-14.1 15.6z"/><path d="M35.3 40.5V29.3h39.9c.4 2.1.6 4.5.6 7.2 0 8.9-2.4 19.9-10.3 27.8-7.6 8-17.4 12.2-30.2 12.2C16.1 76.5 0 60.9 0 41.2S16.1 5.9 35.3 5.9c13.1 0 22.4 5.1 29.4 11.8l-8.3 8.3c-5-4.7-11.8-8.3-21.1-8.3-17.2 0-30.7 13.9-30.7 31.3s13.4 31.3 30.7 31.3c11.2 0 17.5-4.5 21.6-8.6 3.3-3.3 5.5-8.1 6.3-14.6H35.3z"/></svg>
                    <svg class="h-7 fill-white" viewBox="0 0 60 25"><path d="M59.6 14c0-5.8-2.8-10.4-8.2-10.4-5.4 0-8.7 4.6-8.7 10.3 0 6.8 3.8 10.2 9.4 10.2 2.7 0 4.7-.6 6.3-1.5v-4.5c-1.6.8-3.3 1.3-5.6 1.3-2.2 0-4.2-.8-4.4-3.5h11.1c0-.3.1-1.4.1-1.9zm-11.2-2.1c0-2.6 1.6-3.7 3-3.7 1.4 0 2.9 1.1 2.9 3.7h-5.9zM33.7 3.6c-2.2 0-3.7 1-4.5 1.7l-.3-1.4h-5v26.2l5.7-1.2V23c.9.6 2.1 1.5 4.1 1.5 4.2 0 8-3.3 8-10.6-.1-6.7-3.9-10.3-8-10.3zm-1.4 15.8c-1.4 0-2.2-.5-2.7-1.1v-8.8c.6-.7 1.4-1.1 2.7-1.1 2.1 0 3.5 2.3 3.5 5.5 0 3.3-1.4 5.5-3.5 5.5zm-12.7-17L25.3 1l-5.7 1.2v1.2l-5.6 1.2V26h5.6V2.4zM8.6 5.7L3 7V26h5.6V5.7zM5.8.5C3.9.5 2.5 1.9 2.5 3.7S3.9 7 5.8 7s3.3-1.5 3.3-3.3S7.8.5 5.8.5z"/></svg>
                    <svg class="h-8 fill-white" viewBox="0 0 38 57"><path d="M19 28.5a9.5 9.5 0 1 1 19 0 9.5 9.5 0 0 1-19 0z"/><path d="M0 47.5A9.5 9.5 0 0 1 9.5 38H19v9.5a9.5 9.5 0 0 1-19 0z"/><path d="M19 0v19h9.5a9.5 9.5 0 1 0 0-19H19z"/><path d="M0 9.5A9.5 9.5 0 0 0 9.5 19H19V0H9.5A9.5 9.5 0 0 0 0 9.5z"/><path d="M0 28.5A9.5 9.5 0 0 0 9.5 38H19V19H9.5A9.5 9.5 0 0 0 0 28.5z"/></svg>
                    <svg class="h-7 fill-white" viewBox="0 0 100 100"><path d="M6.1 7.5c2.5 2 5 2.8 9.4 2.5l51.3-3.1c1 0 .1-1-.4-1.3L57.9.5C56.9-.3 54.8-.3 53 .1L4.3 3.8c-2 .3-2.4 1.4-.6 2.5L6.1 7.5zm3.3 12.6v54c0 2.9 1.5 4 4.7 3.8l56.4-3.2c3.2-.2 3.6-2.4 3.6-5V15.9c0-2.5-.9-3.8-3-3.6L13.6 15c-2.4.2-4.2 1.5-4.2 5.1zm55.3 3.5c.3 1.4 0 2.8-1.4 3l-2.3.4v33.8c-2 1-3.9 1.6-5.5 1.6-2.5 0-3.1-.8-5-3.1L37.4 36.5v23.4l5.8 1.3s0 2.8-3.9 2.8l-10.8.6c-.3-.6 0-2.2 1.1-2.5l2.8-.8V28.6l-3.9-.3c-.3-1.4.4-3.4 2.4-3.5l11.6-.7 19.8 30.2V25.5l-4.9-.6c-.3-1.7 1-2.9 2.5-3l10.9-.3zM3 2.4L55.1.1c6.3-.6 7.9.2 11.8 3.1l16.3 11.5c2.7 1.9 3.6 2.4 3.6 4.5V83c0 4.1-1.5 6.5-6.8 6.9L18.2 93c-4 .3-5.9-.4-8-2.9L1.3 79.7C-.2 77.8 0 74.1 0 70.8V8c0-4 1.5-5.6 3-5.6z"/></svg>
                </div>
            </div>
        </div>

        <!-- Banner -->
        <div ref="bannerRef" class="bg-white">
            <BannerCards />
        </div>

        <!-- Main -->
        <main>
            <OurWorkSection />
            <ProductSection />
        </main>

        <!-- Footer -->
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
                        <button class="bg-white text-black px-4 py-2 rounded-lg hover:bg-gray-200 transition">
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
