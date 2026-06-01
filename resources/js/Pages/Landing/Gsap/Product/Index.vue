<!-- Product/Index.vue -->
<!-- PERUBAHAN: tambah tombol Add to Cart di setiap card, inject useCart -->
<template>
    <LoadingScreen />
    <div class="p-4 bg-[#e6e6e6] min-h-screen font-sans">

        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
            <h1 class="font-extrabold text-4xl tracking-widest uppercase w-5">Official Supplaybox Shop</h1>
            <NavbarFloating
                static-mode
                balance="$100"
                avatar-url=""
                @nav-click="(id) => console.log('navigated to', id)"
                @cta-click="() => console.log('free sketch clicked')"
            />
        </div>

        <DropDownCategory
            :styles="styles"
            :tag-groups="tagGroups"
            :initial-style="activeStyle"
            :initial-tags="activeTags"
            @update:style="onStyleChange"
            @update:tags="onTagsChange"
        />

        <!-- Product Grid -->
        <div ref="productSectionRef" class="mt-6">

            <!-- Result info -->
            <div class="flex justify-between items-center mb-4">
                <p class="text-sm text-gray-500 font-medium">
                    <span class="text-black font-bold">{{ visibleProducts.length }}</span>
                    product{{ visibleProducts.length !== 1 ? 's' : '' }} found
                    <span v-if="activeStyle || activeTags.length > 0" class="text-gray-400">
                        · filtered by
                        <span v-if="activeStyle" class="text-black font-semibold">{{ activeStyle }}</span>
                        <span v-if="activeStyle && activeTags.length > 0"> + </span>
                        <span v-if="activeTags.length > 0" class="text-black font-semibold">{{ activeTags.join(', ') }}</span>
                    </span>
                    <span v-if="isLoading" class="ml-2 text-gray-400">
                        <svg class="inline w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        memuat...
                    </span>
                </p>
            </div>

            <!-- Empty state -->
            <Transition name="fade-empty">
                <div v-if="visibleProducts.length === 0 && !isLoading" class="flex flex-col items-center justify-center py-24 gap-4">
                    <div class="text-6xl">🎨</div>
                    <p class="text-xl font-bold text-gray-400">No products match your filters</p>
                    <p class="text-sm text-gray-400">Try changing the style or removing some tags</p>
                </div>
            </Transition>

            <!-- Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
                <div
                    v-for="product in paginatedProducts"
                    :key="product.id"
                    class="product-card group cursor-pointer border border-black/10 transition-all duration-300 ease-out rounded-2xl overflow-hidden"
                >
                    <!-- Image — klik ke detail -->
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

                    <!-- Footer -->
                    <div class="product-card-footer px-4 py-3 flex flex-col items-center justify-center gap-2 transition-colors duration-300">
                        <span
                            class="text-xl font-semibold text-black text-center cursor-pointer hover:underline"
                            @click="redirectToProduct(product.id)"
                        >
                            {{ product.name }}
                        </span>

                        <div class="flex flex-wrap gap-1 justify-center">
                            <span
                                v-for="tag in product.tags"
                                :key="tag"
                                class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                :class="activeTags.includes(tag) ? 'bg-black text-white' : 'bg-black/10 text-black/50'"
                            >
                                {{ tag }}
                            </span>
                        </div>

                        <!-- Add to Cart button — muncul saat hover -->
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

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="flex justify-center items-center gap-1 pb-10">
                <button
                    class="pagination-btn"
                    :class="currentPage === 1 ? 'pagination-disabled' : 'pagination-nav'"
                    :disabled="currentPage === 1"
                    @click="goToPage(currentPage - 1)"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
                </button>

                <template v-for="page in pageButtons" :key="page">
                    <span v-if="page === '...'" class="pagination-dots">···</span>
                    <button
                        v-else
                        class="pagination-btn"
                        :class="page === currentPage ? 'pagination-active' : 'pagination-default'"
                        @click="goToPage(page)"
                    >
                        {{ page }}
                    </button>
                </template>

                <button
                    class="pagination-btn"
                    :class="currentPage === totalPages ? 'pagination-disabled' : 'pagination-nav'"
                    :disabled="currentPage === totalPages"
                    @click="goToPage(currentPage + 1)"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>

        </div>
    </div>
    <FooterSection />
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import LoadingScreen    from '../ComponentsV2/LoadingScreen.vue'
import NavbarFloating   from '../ComponentsV2/NavbarFloating.vue'
import DropDownCategory from '../ComponentsV2/DropDownCategory.vue'
import FooterSection    from '../ComponentsV2/FooterSection.vue'
import gsap             from '@/plugins/gsap'
import { useCart }      from '@/composables/useCart'

// ─── Props dari Inertia ───────────────────────────────────────────
const props = defineProps({
    products:      { type: Array,  default: () => [] },
    styles:        { type: Array,  default: () => [] },
    tagGroups:     { type: Array,  default: () => [] },
    activeFilters: { type: Object, default: () => ({ style: '', tags: [] }) },
})

// ─── Cart ─────────────────────────────────────────────────────────
const cart    = useCart()
const addedIds = ref(new Set())   // Set product IDs yang baru di-add (untuk feedback)

const handleAddToCart = (product) => {
    cart.add({
        id:        product.id,
        name:      product.name,
        price:     product.is_free ? 0 : (parseFloat(product.final_price ?? product.price) || 0),
        is_free:   product.is_free ?? false,
        thumbnail: product.image ?? product.thumbnail ?? null,
    })

    // Feedback 1.5 detik
    addedIds.value = new Set(addedIds.value).add(product.id)
    setTimeout(() => {
        const next = new Set(addedIds.value)
        next.delete(product.id)
        addedIds.value = next
    }, 1500)
}

// ─── Filter state ─────────────────────────────────────────────────
const activeStyle = ref(props.activeFilters?.style ?? '')
const activeTags  = ref(props.activeFilters?.tags  ?? [])
const isLoading   = ref(false)

watch(() => props.activeFilters, (newFilters) => {
    if (newFilters) {
        activeStyle.value = newFilters.style ?? ''
        activeTags.value  = newFilters.tags  ?? []
    }
}, { deep: true })

const onStyleChange = (style) => {
    activeStyle.value = style
    currentPage.value = 1
    fetchFiltered()
}

const onTagsChange = (tags) => {
    activeTags.value  = tags
    currentPage.value = 1
    fetchFiltered()
}

const fetchFiltered = () => {
    const params = {}
    if (activeStyle.value)           params.style = activeStyle.value
    if (activeTags.value?.length > 0) params.tags  = activeTags.value

    isLoading.value = true
    router.get('/product-place', params, {
        preserveScroll: true,
        preserveState:  true,
        only:           ['products', 'activeFilters'],
        onFinish:       () => { isLoading.value = false },
    })
}

// ─── Animasi ──────────────────────────────────────────────────────
const productSectionRef = ref(null)
let isAnimating = false

const animateIn = () => {
    const cards = productSectionRef.value?.querySelectorAll('.product-card')
    if (!cards?.length) return
    gsap.fromTo(cards,
        { opacity: 0, y: 18, scale: 0.96 },
        {
            opacity: 1, y: 0, scale: 1,
            duration: 0.38,
            stagger:  { each: 0.045, from: 'start' },
            ease:     'power2.out',
            onComplete: () => { isAnimating = false },
        }
    )
}

watch(() => props.products, async () => {
    currentPage.value = 1
    const cards = productSectionRef.value?.querySelectorAll('.product-card')
    if (cards?.length) gsap.set(cards, { opacity: 0 })
    await nextTick()
    isAnimating = true
    animateIn()
}, { flush: 'post' })

// ─── Computed & Pagination ────────────────────────────────────────
const visibleProducts = computed(() => props.products ?? [])
const PER_PAGE    = 8
const currentPage = ref(1)

const totalPages = computed(() => Math.ceil(visibleProducts.value.length / PER_PAGE))

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * PER_PAGE
    return visibleProducts.value.slice(start, start + PER_PAGE)
})

const pageButtons = computed(() => {
    const total   = totalPages.value
    const current = currentPage.value
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)

    const pages = []
    const left  = Math.max(2, current - 1)
    const right = Math.min(total - 1, current + 1)

    pages.push(1)
    if (left > 2) pages.push('...')
    for (let i = left; i <= right; i++) pages.push(i)
    if (right < total - 1) pages.push('...')
    pages.push(total)

    return pages
})

const goToPage = async (page) => {
    if (isAnimating) return
    if (page < 1 || page > totalPages.value || page === currentPage.value) return

    isAnimating = true
    const cards = productSectionRef.value?.querySelectorAll('.product-card')
    if (cards?.length) {
        await gsap.to(cards, { opacity: 0, y: -10, scale: 0.97, duration: 0.15, ease: 'power2.in' })
    }

    currentPage.value = page
    await nextTick()
    animateIn()
}

const redirectToProduct = (id) => {
    window.location.href = `/product-place/detail/${id}`
}
</script>

<style scoped>
.product-card {
    background-color: #ffffff;
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                background-color 0.3s ease-out;
    will-change: transform, box-shadow;
}
.product-card-image-wrap { background-color: #e6e6e6; transition: background-color 0.3s ease-out; }
.product-card-footer     { background-color: #e6e6e6; transition: background-color 0.3s ease-out; }

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.18);
    background-color: #b4f000;
    z-index: 10;
}
.product-card:hover .product-card-image-wrap { background-color: #b4f000; }
.product-card:hover .product-card-footer     { background-color: #b4f000; border-top-color: rgba(0,0,0,0.1); }

/* Cart button tersembunyi, muncul saat hover card */
.add-to-cart-btn {
    opacity: 0;
    transform: translateY(6px);
    transition: opacity 0.2s ease, transform 0.2s ease, background-color 0.15s ease;
}
.product-card:hover .add-to-cart-btn {
    opacity: 1;
    transform: translateY(0);
}

.fade-empty-enter-active, .fade-empty-leave-active { transition: opacity 0.3s ease; }
.fade-empty-enter-from,   .fade-empty-leave-to     { opacity: 0; }

.pagination-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 2.25rem; height: 2.25rem; border-radius: 20%;
    font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer;
    transition: background 0.18s ease, color 0.18s ease, transform 0.15s ease;
}
.pagination-active   { background-color: #000; color: #fff; transform: scale(1.1); }
.pagination-default  { background-color: transparent; color: #555; }
.pagination-default:hover { background-color: #000; color: #fff; transform: scale(1.05); }
.pagination-nav      { background-color: #fff; color: #000; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
.pagination-nav:hover { background-color: #000; color: #fff; transform: scale(1.05); }
.pagination-disabled { background-color: transparent; color: #ccc; cursor: not-allowed; }
.pagination-dots {
    display: inline-flex; align-items: center; justify-content: center;
    width: 2.25rem; height: 2.25rem; font-size: 0.75rem;
    color: #aaa; letter-spacing: 0.05em; user-select: none;
}
</style>
