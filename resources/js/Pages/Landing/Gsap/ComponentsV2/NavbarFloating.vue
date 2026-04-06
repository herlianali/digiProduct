<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from '@/plugins/gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ShoppingCartIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

gsap.registerPlugin(ScrollTrigger)

const props = defineProps({
    /**
     * Ref atau selector elemen yang dijadikan trigger scroll navbar muncul.
     * Contoh: pass ref dari parent, atau string CSS selector '#bento-grid'
     */
    triggerEl: {
        type: [Object, String],
        default: null,
    },
    /**
     * Kredit / saldo yang ditampilkan di navbar
     */
    balance: {
        type: String,
        default: '$100',
    },
    /**
     * URL avatar user
     */
    avatarUrl: {
        type: String,
        default: '',
    },
    /**
     * Jika true, navbar tampil inline (tanpa fixed GSAP scroll trigger).
     * Cocok untuk dipakai dalam layout flex/header biasa.
     */
    staticMode: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['nav-click', 'cart-click', 'cta-click'])

const navbarContainerRef = ref(null)
const isNavbarVisible    = ref(false)

onMounted(() => {
    if (!props.staticMode) initNavbarAnimation()
})

onUnmounted(() => {
    ScrollTrigger.getAll().forEach(t => t.kill())
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
        backgroundColor: 'rgba(255,255,255,0.97)',
        backdropFilter:  'blur(12px)',
        padding:         '0 16px',
        border:          '1px solid rgba(0,0,0,0.08)',
        boxShadow:       '0 8px 24px -4px rgba(0,0,0,0.12)',
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
}
</script>

<template>
    <!--
        staticMode  → wrapper tanpa style apapun, ikut flow layout normal
        default     → wrapper tetap ada tapi GSAP yang atur posisi fixed-nya
    -->
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

                    <a
                        href="#"
                        class="font-bold text-black text-sm px-3 py-1.5 rounded-full bg-[#fee100] hover:bg-yellow-300 transition-colors duration-200 no-underline"
                        @click.prevent="scrollToSection('platform')"
                    >Platform ▾</a>

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
