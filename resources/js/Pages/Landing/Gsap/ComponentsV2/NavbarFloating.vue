<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ShoppingCartIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

gsap.registerPlugin(ScrollTrigger)

const props = defineProps({
    triggerEl:  { type: [Object, String], default: null },
    balance:    { type: String,  default: '$100' },
    avatarUrl:  { type: String,  default: '' },
    staticMode: { type: Boolean, default: false },
})

const emit = defineEmits(['nav-click', 'cart-click', 'cta-click'])

const navbarContainerRef = ref(null)
const isNavbarVisible    = ref(false)
const platformOpen       = ref(false)
const wrapperRef         = ref(null)   // wrapper luar yang jadi anchor dropdown
const platformWrapperRef = ref(null)
const platformBtnRef = ref(null)
const dropdownLeft   = ref(0)
const dropdownTop    = ref(0)

const platformMenu = [
    {
        group: 'Service',
        items: [
            { label: 'Fiverr',    href: 'https://fiverr.com',    highlight: true  },
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
    if (!platformOpen.value && platformBtnRef.value) {
        const btnRect = platformBtnRef.value.getBoundingClientRect()
        dropdownLeft.value = btnRect.left
        dropdownTop.value  = btnRect.bottom + 6
    }
    platformOpen.value = !platformOpen.value
}

const closePlatform      = () => { platformOpen.value = false }

const handleClickOutside = (e) => {
    if (
        platformBtnRef.value && !platformBtnRef.value.contains(e.target) &&
        !e.target.closest('.dropdown-panel')
    ) {
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
            <div class="bg-white/95 backdrop-blur-sm border border-black/10 shadow-md rounded-full">
                <nav>
                    <div class="flex items-center gap-1 py-2.5 px-4">
                        <div class="hidden md:flex gap-1 items-center">
                            <a
                                :href="redirectLink('aboutUs')"
                                class="text-black font-medium hover:text-emerald-500 transition-colors duration-200 no-underline text-sm px-3 py-1.5 rounded-full hover:bg-black/5"
                                @click.prevent="scrollToSection('about-us')"
                            >About Us</a>

                            <!-- Tombol Platform SAJA di sini, tanpa wrapper dropdown -->
                            <button
                                ref="platformBtnRef"
                                class="font-bold text-black text-sm px-3 py-1.5 rounded-full bg-[#fee100] hover:bg-yellow-300 transition-colors duration-200 flex items-center gap-1 select-none"
                                @click="togglePlatform"
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
            <!-- ── END NAVBAR PILL ── -->

            <!-- ── DROPDOWN — di luar navbar pill, sibling-nya ── -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 scale-y-95 origin-top"
                enter-to-class="opacity-100 scale-y-100 origin-top"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 scale-y-100 origin-top"
                leave-to-class="opacity-0 scale-y-95 origin-top"
            >
                <div
                    v-if="platformOpen"
                    class="dropdown-panel bg-white/95 backdrop-blur-sm border border-black/10 py-4 px-4 min-w-[340px]"
                    :style="{
                        position: 'fixed',
                        left: dropdownLeft + 'px',
                        top:  dropdownTop  + 'px',
                        zIndex: 9999,
                    }"
                >
                    <div class="grid grid-cols-2 gap-x-6">
                        <!-- Left: Service + Portofolio -->
                        <div class="flex flex-col">
                            <template v-for="group in platformMenu.slice(0, 2)" :key="group.group">
                                <div class="flex items-center gap-2 mb-1 mt-3 first:mt-0">
                                    <span class="text-[10px] text-gray-400 font-medium italic tracking-widest">{{ group.group }}</span>
                                    <div class="flex-1 h-px bg-gray-200"></div>
                                </div>
                                <a v-for="item in group.items" :key="item.label" :href="item.href" target="_blank" rel="noopener noreferrer" class="no-underline" @click="closePlatform">
                                    <div :class="['flex items-center gap-2 px-3 py-1.5 rounded-xl font-black text-base text-black transition-all duration-150', item.highlight ? 'bg-[#fee100] hover:bg-yellow-300' : 'hover:bg-black/5']">
                                        <span v-if="item.highlight" class="font-black">→</span>
                                        {{ item.label }}
                                    </div>
                                </a>
                            </template>
                        </div>
                        <!-- Right: Microstock -->
                        <div class="flex flex-col">
                            <template v-for="group in platformMenu.slice(2)" :key="group.group">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] text-gray-400 font-medium italic tracking-widest">{{ group.group }}</span>
                                    <div class="flex-1 h-px bg-gray-200"></div>
                                </div>
                                <a v-for="item in group.items" :key="item.label" :href="item.href" target="_blank" rel="noopener noreferrer" class="no-underline" @click="closePlatform">
                                    <div :class="['flex items-center gap-2 px-3 py-1.5 rounded-xl font-black text-base text-black transition-all duration-150', item.highlight ? 'bg-[#fee100] hover:bg-yellow-300' : 'hover:bg-black/5']">
                                        <span v-if="item.highlight" class="font-black">→</span>
                                        {{ item.label }}
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </Transition>
            <!-- ── END DROPDOWN ── -->

        </div>
    </div>
</template>

<style scoped>
.dropdown-panel {
    border-radius: 0 0 16px 16px;  /* ← ubah: kiri-atas DAN kanan-atas = 0 */
}

/* Concave corner KIRI atas */
.dropdown-panel::before {
    content: '';
    position: absolute;
    top: -4px;
    left: -8px;
    width: 16px;
    height: 16px;
    background: transparent;
    box-shadow: -4px 4px 0 4px rgb(255 255 255 / 0.95);
    border-bottom-left-radius: 16px;
    pointer-events: none;
}

/* Concave corner KANAN atas */
.dropdown-panel::after {
    content: '';
    position: absolute;
    top: -4px;
    right: -8px;
    width: 16px;
    height: 16px;
    background: transparent;
    box-shadow: 4px 4px 0 4px rgb(255 255 255 / 0.95);  /* ← arah box-shadow dibalik */
    border-bottom-right-radius: 16px;
    pointer-events: none;
}
</style>
