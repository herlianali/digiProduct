<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ShoppingCartIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

gsap.registerPlugin(ScrollTrigger)

const props = defineProps({
    triggerEl: {
        type: [Object, String],
        default: null,
    },
    balance: {
        type: String,
        default: '$100',
    },
    avatarUrl: {
        type: String,
        default: '',
    },
    staticMode: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['nav-click', 'cart-click', 'cta-click'])

const navbarContainerRef = ref(null)
const isNavbarVisible    = ref(false)
const platformOpen       = ref(false)
const dropdownRef        = ref(null)

// ── Dropdown data ──
const platformMenu = [
    {
        group: 'Service',
        items: [
            { label: 'Fiverr',    href: 'https://fiverr.com',    highlight: true },
            { label: 'Upwork',    href: 'https://upwork.com',    highlight: false },
        ],
    },
    {
        group: 'Portofolio',
        items: [
            { label: 'Behance',   href: 'https://behance.net',   highlight: false },
            { label: 'Pinterest', href: 'https://pinterest.com', highlight: false },
        ],
    },
    {
        group: 'Microstock',
        items: [
            { label: 'Canva',        href: 'https://canva.com',        highlight: false },
            { label: 'Shutterstock', href: 'https://shutterstock.com', highlight: false },
            { label: 'Freepik',      href: 'https://freepik.com',      highlight: false },
        ],
    },
]

const togglePlatform = () => {
    platformOpen.value = !platformOpen.value
}

const closePlatform = () => {
    platformOpen.value = false
}

// Close dropdown when clicking outside
const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        closePlatform()
    }
}

onMounted(() => {
    if (!props.staticMode) initNavbarAnimation()
    document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
    ScrollTrigger.getAll().forEach(t => t.kill())
    document.removeEventListener('mousedown', handleClickOutside)
})

const initNavbarAnimation = () => {
    gsap.set(navbarContainerRef.value, {
        opacity:         0,
        y:               -16,
        pointerEvents:   'none',
        position:        'fixed',
        top:             '16px',
        right:           '16px',
        left:            'auto',
        width:           'auto',
        zIndex:          1000,
        borderRadius:    '50px',
        padding:         '0 16px',
        border:          '1px solid rgba(0,0,0,0.08)',
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
            gsap.to(navbarContainerRef.value, {
                opacity:       1,
                y:             0,
                pointerEvents: 'auto',
                duration:      0.45,
                ease:          'power3.out',
            })
        },
        onLeaveBack: () => {
            if (!isNavbarVisible.value) return
            isNavbarVisible.value = false
            gsap.to(navbarContainerRef.value, {
                opacity:       0,
                y:             -16,
                pointerEvents: 'none',
                duration:      0.3,
                ease:          'power2.in',
            })
        },
    })
}

const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId)
    if (!element) return
    const offset = isNavbarVisible.value ? 80 : 0
    window.scrollTo({
        top:      element.getBoundingClientRect().top + window.scrollY - offset,
        behavior: 'smooth',
    })
    emit('nav-click', sectionId)
    closePlatform()
}
</script>

<template>
    <div
        ref="navbarContainerRef"
        :class="[
            'z-[1000]',
            staticMode
                ? 'bg-white/95 backdrop-blur-sm border border-black/10 rounded-full shadow-md'
                : ''
        ]"
    >
        <nav>
            <div class="flex items-center gap-1 py-2.5 px-4">

                <!-- Nav Links -->
                <div class="hidden md:flex gap-1 items-center">
                    <a
                        href="#"
                        class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5"
                        @click.prevent="scrollToSection('about-us')"
                    >About Us</a>

                    <!-- Platform dropdown trigger -->
                    <div ref="dropdownRef" class="relative">
                        <button
                            class="font-bold text-black text-sm px-3 py-1.5 rounded-full bg-[#fee100] hover:bg-yellow-300 transition-colors duration-200 flex items-center gap-1 select-none"
                            @click="togglePlatform"
                        >
                            Platform
                            <!-- Chevron rotates when open -->
                            <svg
                                class="w-3.5 h-3.5 transition-transform duration-200"
                                :class="platformOpen ? 'rotate-180' : 'rotate-0'"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- ── Dropdown panel ── -->
                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 translate-y-1 scale-95"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 translate-y-1 scale-95"
                        >
                            <div
                                v-if="platformOpen"
                                class="absolute top-[calc(100%+10px)] left-0 bg-white rounded-2xl shadow-xl border border-black/8 py-4 px-2 min-w-[480px] z-50"
                            >
                                <!-- Grid: 2 columns -->
                                <div class="grid grid-cols-2 gap-x-2">
                                    <!-- Left column: Service + Portofolio -->
                                    <div class="flex flex-col gap-1 px-2">
                                        <template v-for="group in platformMenu.slice(0, 2)" :key="group.group">
                                            <!-- Group label -->
                                            <div class="flex items-center gap-2 mt-2 mb-0.5 first:mt-0">
                                                <span class="text-[11px] text-gray-400 font-medium italic tracking-wide">
                                                    {{ group.group }}
                                                </span>
                                                <div class="flex-1 h-px bg-gray-200"></div>
                                            </div>
                                            <!-- Items -->
                                            <a
                                                v-for="item in group.items"
                                                :key="item.label"
                                                :href="item.href"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="no-underline"
                                                @click="closePlatform"
                                            >
                                                <div
                                                    :class="[
                                                        'flex items-center gap-2 px-3 py-2 rounded-xl font-bold text-sm text-black transition-all duration-150',
                                                        item.highlight
                                                            ? 'bg-[#fee100] hover:bg-yellow-300'
                                                            : 'hover:bg-black/5'
                                                    ]"
                                                >
                                                    <!-- Arrow only for highlighted item -->
                                                    <span v-if="item.highlight" class="text-black">→</span>
                                                    {{ item.label }}
                                                </div>
                                            </a>
                                        </template>
                                    </div>

                                    <!-- Right column: Microstock -->
                                    <div class="flex flex-col gap-1 px-2">
                                        <template v-for="group in platformMenu.slice(2)" :key="group.group">
                                            <div class="flex items-center gap-2 mt-2 mb-0.5 first:mt-0">
                                                <span class="text-[11px] text-gray-400 font-medium italic tracking-wide">
                                                    {{ group.group }}
                                                </span>
                                                <div class="flex-1 h-px bg-gray-200"></div>
                                            </div>
                                            <a
                                                v-for="item in group.items"
                                                :key="item.label"
                                                :href="item.href"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="no-underline"
                                                @click="closePlatform"
                                            >
                                                <div
                                                    :class="[
                                                        'flex items-center gap-2 px-3 py-2 rounded-xl font-bold text-sm text-black transition-all duration-150',
                                                        item.highlight
                                                            ? 'bg-[#fee100] hover:bg-yellow-300'
                                                            : 'hover:bg-black/5'
                                                    ]"
                                                >
                                                    <span v-if="item.highlight" class="text-black">→</span>
                                                    {{ item.label }}
                                                </div>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                    <!-- end Platform dropdown -->

                    <a
                        href="#"
                        class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5"
                        @click.prevent="scrollToSection('shop')"
                    >Shop</a>

                    <a
                        href="#"
                        class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5"
                        @click.prevent="scrollToSection('contact')"
                    >Contact</a>
                </div>

                <!-- Divider -->
                <div class="hidden md:block w-px h-5 bg-gray-200 mx-1"></div>

                <!-- Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button
                        class="bg-[#4dfa03] hover:bg-green-400 font-extrabold text-black px-4 py-1.5 rounded-full flex items-center gap-1 transition-all whitespace-nowrap text-sm border border-black/10"
                        @click="emit('cta-click')"
                    >
                        FREE SKETCH!
                        <ChevronRightIcon class="w-3.5 h-3.5 text-black inline-block" />
                    </button>

                    <ShoppingCartIcon
                        class="w-5 h-5 text-black cursor-pointer hover:text-green-600 transition-colors"
                        @click="emit('cart-click')"
                    />

                    <span class="hidden md:inline-block border border-gray-200 rounded-full px-3 py-1 text-black text-sm font-medium">
                        {{ balance }}
                    </span>

                    <div class="w-7 h-7 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center flex-shrink-0">
                        <img :src="avatarUrl" alt="user" class="w-full h-full object-cover" />
                    </div>
                </div>

            </div>
        </nav>
    </div>
</template>