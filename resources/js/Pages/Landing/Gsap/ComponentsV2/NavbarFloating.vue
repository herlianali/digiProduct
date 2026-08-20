<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import { UserCircleIcon } from '@heroicons/vue/20/solid'
import { useCart } from '@/Composables/useCart'
import CartSidebar from './CartSidebar.vue'

gsap.registerPlugin(ScrollTrigger)

const props = defineProps({
    triggerEl:  { type: [Object, String], default: null },
    balance:    { type: String,  default: '' },
    avatarUrl:  { type: String,  default: '' },
    staticMode: { type: Boolean, default: false },
})

const emit = defineEmits(['nav-click', 'cart-click', 'cta-click'])

const cart = useCart()

const navbarContainerRef = ref(null)
const isNavbarVisible    = ref(false)
const platformOpen       = ref(false)
const wrapperRef         = ref(null)
const platformBtnRef     = ref(null)
const dropdownRef        = ref(null)
const cartOpen           = ref(false)

const platformMenu = [
    {
        group: 'Service',
        items: [
            { label: 'Fiverr',    href: 'https://www.fiverr.com/users/supplaybox/portfolio' },
            { label: 'Upwork',    href: 'https://www.upwork.com/freelancers/~018928f9b657bc5557?p=2002178990555295744' },
        ],
    },
    {
        group: 'Portofolio',
        items: [
            { label: 'Behance',   href: 'https://www.behance.net/supplay_box' },
            { label: 'Pinterest', href: 'https://pin.it/yegYhYpFy' },
        ],
    },
    {
        group: 'Microstock',
        items: [
            { label: 'Canva',        href: 'https://canva.com' },
            { label: 'Shutterstock', href: 'https://shutterstock.com' },
            { label: 'Freepik',      href: 'https://freepik.com' },
        ],
    },
]

const togglePlatform = () => {
    platformOpen.value = !platformOpen.value
}

const closePlatform = () => { 
    platformOpen.value = false 
}

const handleClickOutside = (e) => {
    const target = e.target
    const isPlatformBtn = platformBtnRef.value && platformBtnRef.value.contains(target)
    const isDropdown = dropdownRef.value && dropdownRef.value.contains(target)
    
    if (!isPlatformBtn && !isDropdown) {
        closePlatform()
    }
}

const redirectLink = (section) => {
    switch (section) {
        case 'aboutUs': return window.location.origin + '/our-team'
        case 'contact': return window.location.origin + '/get-in-touch'
        default:        return '#'
    }
}

const formatPrice = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value || 0)
}

const handleCartClick = () => {
    cartOpen.value = true
    emit('cart-click')
}

onMounted(() => {
    if (!props.staticMode) initNavbarAnimation()
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    ScrollTrigger.getAll().forEach(t => t.kill())
    document.removeEventListener('click', handleClickOutside)
})

const initNavbarAnimation = () => {
    gsap.set(navbarContainerRef.value, {
        opacity: 0, y: -16, pointerEvents: 'none',
        position: 'fixed', top: '16px', right: '16px',
        left: 'auto', width: 'auto', zIndex: 1000,
    })

    const trigger = props.triggerEl
        ? (typeof props.triggerEl === 'string' ? document.querySelector(props.triggerEl) : props.triggerEl)
        : document.body

    ScrollTrigger.create({
        trigger,
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

const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId)
    if (!element) return
    const offset = isNavbarVisible.value ? 80 : 0
    window.scrollTo({ top: element.getBoundingClientRect().top + window.scrollY - offset, behavior: 'smooth' })
    emit('nav-click', sectionId)
    closePlatform()
}

</script>

<template>
    <div ref="navbarContainerRef" class="z-[1000]" style="overflow: visible;">
        <div ref="wrapperRef" class="relative" style="overflow: visible;">

            <!-- ── NAVBAR PILL ── -->
            <div class="bg-white backdrop-blur-sm  rounded-full relative z-20">
                <nav>
                    <div class="flex items-center gap-1 py-2.5 px-4">
                        <div class="hidden md:flex gap-1 items-center">
                            <a
                                :href="redirectLink('aboutUs')"
                                class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5"
                                @click.prevent="scrollToSection('about-us')"
                            >About Us</a>

                            <button
                                ref="platformBtnRef"
                                class="font-bold text-black text-sm px-3 py-1.5 rounded-full bg-[#fee100] hover:bg-yellow-300 transition-colors duration-200 flex items-center gap-1 select-none"
                                @click.stop="togglePlatform"
                            >
                                Platform
                                <svg
                                    class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="platformOpen ? 'rotate-180' : 'rotate-0'"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <a
                                href="#"
                                class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5"
                                @click.prevent="scrollToSection('shop')"
                            >Shop</a>

                            <a
                                :href="redirectLink('contact')"
                                class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5"
                                @click.prevent="scrollToSection('contact')"
                            >Contact</a>
                        </div>

                        <div class="hidden md:block w-px h-5 bg-gray-200 mx-1"></div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button
                                class="bg-[#4dfa03] hover:bg-green-400 font-extrabold text-black px-4 py-1.5 rounded-full flex items-center gap-1 transition-all whitespace-nowrap text-sm border border-black/10"
                                @click="emit('cta-click')"
                            >
                                FREE SKETCH!
                                <ChevronRightIcon class="w-3.5 h-3.5 text-black inline-block" />
                            </button>

                            <button
                                class="relative p-1 hover:text-green-600 transition-colors"
                                @click="handleCartClick"
                            >
                                <img src="/public/assets/image/banner/cart-icon.svg" alt="" class="w-5 h-5" />
                                <Transition name="badge">
                                    <span
                                        v-if="cart.count.value > 0"
                                        class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] bg-[#4dfa03] text-black text-[10px] font-black rounded-full flex items-center justify-center px-[3px] shadow-sm"
                                    >
                                        {{ cart.count.value > 99 ? '99+' : cart.count.value }}
                                    </span>
                                </Transition>
                            </button>

                            <span class="hidden md:inline-block border border-gray-200 rounded-full px-3 py-1 text-black text-sm font-medium">
                                {{ formatPrice(cart.subtotal.value) }}
                            </span>

                            <div class="w-7 h-7 overflow-hidden flex items-center justify-center flex-shrink-0">
                                <UserCircleIcon alt="user" class="w-full h-full object-cover" />
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
            <!-- ── DROPDOWN ── -->
            <div
                v-if="platformOpen"
                ref="dropdownRef"
                class="dropdown-curved absolute left-[290px] -translate-x-1/2 top-full mt-0 bg-white backdrop-blur-sm py-5 px-5 z-10"
                style="width: 380px;"
            >
                <div class="flex gap-6">
                    <!-- Kolom 1: Service + Portofolio -->
                    <div class="flex flex-col flex-1">
                        <!-- Service -->
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.12em]">Service</span>
                        <hr class="border-gray-200 my-1">
                        <a 
                            v-for="item in platformMenu[0].items" 
                            :key="item.label"
                            :href="item.href" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="group menu-item no-underline font-bold text-black text-sm py-1.5 px-2 rounded-lg flex items-center gap-2 transition-all duration-200 hover:bg-[#fee100] hover:-ml-2 hover:pl-4"
                            :class="item.active ? 'bg-[#fee100] -ml-2 pl-4' : ''"
                            @click="closePlatform"
                        >
                            <span 
                                class="text-xs transition-opacity duration-200"
                                :class="item.active ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
                            >→</span>
                            {{ item.label }}
                        </a>
                        
                        <!-- Portofolio -->
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.12em] mt-3">Portofolio</span>
                        <hr class="border-gray-200 my-1">
                        <a 
                            v-for="item in platformMenu[1].items" 
                            :key="item.label"
                            :href="item.href" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="group menu-item no-underline font-bold text-black text-sm py-1.5 px-2 rounded-lg flex items-center gap-2 transition-all duration-200 hover:bg-[#fee100] hover:-ml-2 hover:pl-4"
                            @click="closePlatform"
                        >
                            <span class="text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200">→</span>
                            {{ item.label }}
                        </a>
                    </div>
                    
                    <!-- Kolom 2: Microstock -->
                    <div class="flex flex-col flex-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.12em]">Microstock</span>
                        <hr class="border-gray-200 my-1">
                        <a 
                            v-for="item in platformMenu[1].items" 
                            :key="item.label"
                            :href="item.href" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="group menu-item no-underline font-bold text-black text-sm py-1.5 px-2 rounded-lg flex items-center gap-2 transition-all duration-200 hover:bg-[#fee100] hover:-ml-2 hover:pl-4"
                            @click="closePlatform"
                        >
                            <span class="text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200">→</span>
                            {{ item.label }}
                        </a>
                    </div>
                </div>
            </div>
            <!-- ── END NAVBAR PILL ── -->
        </div>
    </div>

    <!-- ── CART SIDEBAR ── -->
    <Teleport to="body">
        <CartSidebar :open="cartOpen" @close="cartOpen = false" />
    </Teleport>
</template>

<style scoped>
/* ─── DROPDOWN DENGAN LENGKUNGAN KIRI-KANAN ─── */
.dropdown-curved {
    border-radius: 0 0 20px 20px;
    position: relative;
    animation: dropdownFade 0.25s ease-out;
    transform-origin: top center;
    box-shadow: 0 20px 60px -12px rgba(0, 0, 0, 0.2);
}

/* Lengkungan Sisi Kiri */
.dropdown-curved::before {
    content: '';
    position: absolute;
    top: 0px;
    left: -20px;
    width: 20px;
    height: 30px;
    background-color: transparent;
    border-top-right-radius: 15px;
    box-shadow: 6px -6px 0 0 rgba(255, 255, 255, 1);
    pointer-events: none;
    z-index: 1;
}

/* Lengkungan Sisi Kanan */
.dropdown-curved::after {
    content: '';
    position: absolute;
    top: 0;
    right: -20px;
    width: 20px;
    height: 30px;
    background-color: transparent;
    border-top-left-radius: 15px;
    box-shadow: -6px -6px 0 0 rgba(255, 255, 255, 1);
    pointer-events: none;
    z-index: 1;
}

/* HR line */
hr {
    border: none;
    border-top: 1px solid #f0f0f0;
}

/* Menu item active dengan background kuning */
.menu-item.bg-\\[\\#fee100\\] {
    background-color: #fee100;
}

.menu-item.bg-\\[\\#fee100\\]:hover {
    background-color: #e6c900;
}

@keyframes dropdownFade {
    from {
        opacity: 0;
        transform: translateX(-50%) scale(0.95) translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) scale(1) translateY(0);
    }
}

/* Badge pop animation */
.badge-enter-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.badge-leave-active {
    transition: all 0.15s ease-in;
}
.badge-enter-from {
    opacity: 0;
    transform: scale(0.3);
}
.badge-leave-to {
    opacity: 0;
    transform: scale(0.5);
}
</style>