<template>
    <div class="relative" ref="wrapperRef">

        <!-- Trigger Search Bar (collapsed state) -->
        <div
            v-if="!isOpen"
            class="flex w-full bg-white rounded-full overflow-hidden shadow-sm cursor-pointer"
            @click="openDropdown"
        >
            <div class="bg-black rounded-l-full px-6 py-2.5 text-white flex items-center gap-2">
                <span class="text-sm font-medium">Style</span>
                <svg class="w-4 h-4" fill="#ffffff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                    <path d="M300.3 440.8C312.9 451 331.4 450.3 343.1 438.6L471.1 310.6C480.3 301.4 483 287.7 478 275.7C473 263.7 461.4 256 448.5 256L192.5 256C179.6 256 167.9 263.8 162.9 275.8C157.9 287.8 160.7 301.5 169.9 310.6L297.9 438.6L300.3 440.8z"/>
                </svg>
            </div>
            <div class="flex flex-1 items-center gap-2 px-3 py-1.5 flex-wrap">
                <span
                    v-for="tag in activeTags"
                    :key="tag.id"
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold"
                    :style="{ backgroundColor: tag.color, color: tag.textColor }"
                >
                    {{ tag.label }}
                </span>
                <span class="text-sm text-gray-400 ml-1" v-if="activeTags.length === 0">Search...</span>
            </div>
        </div>

        <!-- Backdrop (behind panel, closes on click outside) -->
        <Transition name="fade">
            <div
                v-if="isOpen"
                class="fixed inset-0 z-40"
                @click="closeDropdown"
            />
        </Transition>

        <!-- Dropdown Panel (expanded state) -->
        <Transition name="expand">
            <div
                v-if="isOpen"
                class="relative w-full bg-white rounded-2xl overflow-hidden shadow-xl border border-gray-200 z-50"
                @click.stop
            >
                <div class="flex">

                    <!-- LEFT: Black Sidebar -->
                    <div
                        class="flex-shrink-0 bg-black flex flex-col"
                        style="min-width: 130px; border-radius: 0 0 0 1rem;"
                    >
                        <!-- Style toggle button -->
                        <button
                            @click="closeDropdown"
                            class="flex items-center gap-2 px-5 py-3 text-white transition-colors hover:bg-white/10"
                        >
                            <span class="text-sm font-bold tracking-wide">Style</span>
                            <svg class="w-4 h-4 rotate-180" fill="#ffffff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                <path d="M300.3 440.8C312.9 451 331.4 450.3 343.1 438.6L471.1 310.6C480.3 301.4 483 287.7 478 275.7C473 263.7 461.4 256 448.5 256L192.5 256C179.6 256 167.9 263.8 162.9 275.8C157.9 287.8 160.7 301.5 169.9 310.6L297.9 438.6L300.3 440.8z"/>
                            </svg>
                        </button>

                        <!-- Style items -->
                        <div class="flex flex-col gap-0.5 px-2 pb-3">
                            <button
                                v-for="style in styles"
                                :key="style"
                                @click="selectStyle(style)"
                                class="w-full text-left px-4 py-2 text-sm font-bold rounded-lg transition-colors"
                                :style="activeStyle === style
                                    ? { backgroundColor: '#b5f000', color: '#000000' }
                                    : { color: '#ffffff' }"
                                @mouseenter="e => { if (activeStyle !== style) e.currentTarget.style.backgroundColor = 'rgba(255,255,255,0.1)' }"
                                @mouseleave="e => { if (activeStyle !== style) e.currentTarget.style.backgroundColor = 'transparent' }"
                            >
                                {{ style }}
                            </button>
                        </div>
                    </div>

                    <!-- RIGHT: Tags + Categories -->
                    <div class="flex-1 flex flex-col min-w-0">

                        <!-- Top Row: Tags + Search + Close -->
                        <div class="flex items-center gap-2 px-4 pt-3 pb-2 flex-wrap">
                            <TransitionGroup name="tag">
                                <span
                                    v-for="tag in activeTags"
                                    :key="tag.id"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold flex-shrink-0"
                                    :style="{ backgroundColor: tag.color, color: tag.textColor }"
                                >
                                    {{ tag.label }}
                                    <button
                                        @click.stop="removeTag(tag.id)"
                                        class="hover:opacity-60 transition-opacity flex items-center"
                                        :style="{ color: tag.textColor }"
                                    >
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                    </button>
                                </span>
                            </TransitionGroup>

                            <input
                                ref="searchInputRef"
                                type="text"
                                v-model="searchQuery"
                                placeholder="Search..."
                                class="flex-1 min-w-[80px] outline-none text-sm bg-transparent py-1"
                            />

                            <!-- Close X -->
                            <button
                                @click="closeDropdown"
                                class="flex-shrink-0 w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors ml-auto"
                            >
                                <svg class="w-3.5 h-3.5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-100 mx-3" />

                        <!-- Category Grid -->
                        <div class="px-4 py-3 overflow-x-auto">
                            <div class="flex gap-8 min-w-max">
                                <div v-for="category in filteredCategories" :key="category.name">
                                    <p class="text-xs font-black text-gray-800 uppercase tracking-widest mb-2">
                                        {{ category.name }}
                                    </p>
                                    <div class="flex flex-col gap-0.5">
                                        <button
                                            v-for="item in category.items"
                                            :key="item"
                                            @click="toggleItem(item)"
                                            class="text-left px-2 py-0.5 rounded text-sm transition-colors whitespace-nowrap"
                                            :style="isItemActive(item)
                                                ? { backgroundColor: '#ffe500', color: '#000000', fontWeight: '600' }
                                                : { color: '#6b7280' }"
                                            @mouseenter="e => { if (!isItemActive(item)) e.currentTarget.style.backgroundColor = '#f3f4f6'; e.currentTarget.style.color = '#000' }"
                                            @mouseleave="e => { if (!isItemActive(item)) { e.currentTarget.style.backgroundColor = 'transparent'; e.currentTarget.style.color = '#6b7280' } }"
                                        >
                                            {{ item }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </Transition>

    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

const isOpen = ref(false)
const searchQuery = ref('')
const activeStyle = ref('Popart')
const searchInputRef = ref(null)

const tagColorPalette = [
    { color: '#b5f000', textColor: '#1a2e00' },
    { color: '#ff6eb4', textColor: '#5a0027' },
    { color: '#ff7a00', textColor: '#3d1a00' },
    { color: '#00d4ff', textColor: '#002a3d' },
    { color: '#a259ff', textColor: '#1a0040' },
]

const activeTags = ref([
    { id: 'BMX',     label: 'BMX',     ...tagColorPalette[0] },
    { id: 'Surfing', label: 'Surfing', ...tagColorPalette[1] },
    { id: 'Reggae',  label: 'Reggae',  ...tagColorPalette[2] },
])

const styles = ['Retro', 'Popart', 'Dark Art', 'Brutalism']

const categories = [
    { name: 'Sport', items: ['GYM', 'MMA', 'Skateboard', 'Football', 'Basketball', 'Snowboard', 'Padel', 'Badminton', 'Tennis', 'BMX', 'Offroad', 'Surfing', 'Kite Surfing'] },
    { name: 'Music', items: ['Rock', 'Pop', 'Jazz', 'Reggae'] },
    { name: 'Pet Lovers', items: ['Dog', 'Cat', 'Mamalia', 'Reptile'] },
    { name: 'Hoby', items: ['Anime', 'NFT', 'Game', 'Otomotive'] },
    { name: 'Website', items: ['Web Illustration'] },
]

const filteredCategories = computed(() => {
    if (!searchQuery.value.trim()) return categories
    const q = searchQuery.value.toLowerCase()
    return categories
        .map(cat => ({ ...cat, items: cat.items.filter(item => item.toLowerCase().includes(q)) }))
        .filter(cat => cat.items.length > 0)
})

async function openDropdown() {
    isOpen.value = true
    await nextTick()
    searchInputRef.value?.focus()
}

function closeDropdown() {
    isOpen.value = false
    searchQuery.value = ''
}

function selectStyle(style) {
    activeStyle.value = style
}

function isItemActive(item) {
    return activeTags.value.some(t => t.id === item)
}

function toggleItem(item) {
    const idx = activeTags.value.findIndex(t => t.id === item)
    if (idx !== -1) {
        activeTags.value.splice(idx, 1)
    } else {
        const colorIdx = activeTags.value.length % tagColorPalette.length
        activeTags.value.push({ id: item, label: item, ...tagColorPalette[colorIdx] })
    }
}

function removeTag(id) {
    const idx = activeTags.value.findIndex(t => t.id === id)
    if (idx !== -1) activeTags.value.splice(idx, 1)
}
</script>

<style scoped>
.expand-enter-active {
    transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.expand-leave-active {
    transition: opacity 0.2s ease, transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.expand-enter-from {
    opacity: 0;
    transform: scaleY(0.92) translateY(-8px);
    transform-origin: top center;
}
.expand-leave-to {
    opacity: 0;
    transform: scaleY(0.95) translateY(-4px);
    transform-origin: top center;
}

.fade-enter-active,
.fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; }

.tag-enter-active,
.tag-leave-active { transition: opacity 0.18s ease, transform 0.18s ease; }
.tag-enter-from,
.tag-leave-to { opacity: 0; transform: scale(0.75); }
</style>
