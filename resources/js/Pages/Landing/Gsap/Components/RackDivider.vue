<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    color:      { type: String,  default: '#000000' },
    topColor:   { type: String,  default: '#ffffff' },
    midRadius:  { type: Number,  default: 0 },
    height:     { type: Number,  default: 48 },
    tabHeight:  { type: Number,  default: 14 },
    notchDepth: { type: Number,  default: 12 },
    tabWidth:   { type: Number,  default: 42 },
    radius:     { type: Number,  default: 6 },
    direction:  { type: String,  default: 'up' },
    flip:       { type: Boolean, default: false },
    pattern:    { type: String,  default: 'random' },
    seed:       { type: Number,  default: 42 },
    tabCount:   { type: Number,  default: 7 },
    responsive: { type: Boolean, default: true },
    minTabWidth: { type: Number, default: 30 },
    maxTabCount: { type: Number, default: 12 },
    manualPositions: { type: Array, default: null }
})

// Responsive detection
const viewportWidth = ref(1200)

const getViewportWidth = () => {
    if (typeof window === 'undefined') return 1200
    return window.innerWidth
}

const getResponsiveTabCount = () => {
    if (!props.responsive) return props.tabCount

    const width = viewportWidth.value
    let count = props.tabCount

    if (width < 480) count = Math.min(props.tabCount, 4)
    else if (width < 768) count = Math.min(props.tabCount, 5)
    else if (width < 1024) count = Math.min(props.tabCount, 6)
    else if (width < 1280) count = Math.min(props.tabCount, 7)

    return Math.min(count, props.maxTabCount)
}

const getResponsiveTabWidth = () => {
    if (!props.responsive) return props.tabWidth

    const width = viewportWidth.value
    const count = getResponsiveTabCount()
    const maxWidth = (width - 48) / count - 8
    return Math.min(props.tabWidth, Math.max(props.minTabWidth, maxWidth))
}

const updateViewport = () => {
    viewportWidth.value = getViewportWidth()
}

// Watch for resize
if (typeof window !== 'undefined') {
    onMounted(() => {
        updateViewport()
        window.addEventListener('resize', updateViewport)
    })

    onUnmounted(() => {
        window.removeEventListener('resize', updateViewport)
    })
}

// Computed values
const VW = computed(() => viewportWidth.value)
const currentTabCount = computed(() => getResponsiveTabCount())
const currentTabWidth = computed(() => getResponsiveTabWidth())

const seededRandom = (seed) => {
    let s = seed
    return () => { s = (s * 16807) % 2147483647; return (s - 1) / 2147483646 }
}

const mr = computed(() => props.midRadius)
const r = computed(() => props.radius)
const tw = computed(() => currentTabWidth.value)
const barY = computed(() => Math.max(mr.value, props.tabHeight) + props.tabHeight + 4)
const barBottom = computed(() => barY.value + props.height)
const isDown = computed(() => props.direction === 'down')
const tabEndY = computed(() => isDown.value ? barY.value + props.tabHeight : barY.value - props.tabHeight)
const notchEndY = computed(() => isDown.value ? barY.value - props.notchDepth : barY.value + props.notchDepth)
const totalH = computed(() => barBottom.value)

const generateTabs = () => {
    const rand = seededRandom(props.seed)
    const pad = 24 + mr.value
    const count = currentTabCount.value
    const zone = (VW.value - pad * 2) / count

    return Array.from({ length: count }, (_, i) => {
        const zoneStart = pad + i * zone
        const maxJitter = zone - tw.value - 8
        const xl = Math.round(zoneStart + rand() * Math.max(0, maxJitter))
        const xr = xl + tw.value
        let type = 'out'

        if (props.pattern === 'random') type = rand() > 0.4 ? 'out' : 'in'
        else if (props.pattern === 'alternate') type = i % 2 === 0 ? 'out' : 'in'
        else if (props.pattern === 'in-only') type = 'in'

        return { xl, xr, type }
    })
}

const tabs = computed(() => {
    if (props.manualPositions) {
        return props.manualPositions.map(t => ({
            xl: Math.round((t.xPct / 100) * VW.value),
            xr: Math.round((t.xPct / 100) * VW.value) + tw.value,
            type: t.type
        }))
    }
    return generateTabs()
})

const s = (val) => isDown.value ? 1 - val : val

const tabOut = (xl, xr) => {
    const tY = tabEndY.value, bY = barY.value, dy = isDown.value ? r.value : -r.value
    return (
        `L${xl - r.value},${bY} A${r.value},${r.value} 0 0 ${s(0)} ${xl},${bY + dy} ` +
        `L${xl},${tY + (isDown.value ? -r.value : r.value)} A${r.value},${r.value} 0 0 ${s(1)} ${xl + r.value},${tY} ` +
        `L${xr - r.value},${tY} A${r.value},${r.value} 0 0 ${s(1)} ${xr},${tY + (isDown.value ? -r.value : r.value)} ` +
        `L${xr},${bY + dy} A${r.value},${r.value} 0 0 ${s(0)} ${xr + r.value},${bY} `
    )
}

const notchIn = (xl, xr) => {
    const nY = notchEndY.value, bY = barY.value, dy = isDown.value ? -r.value : r.value
    return (
        `L${xl - r.value},${bY} A${r.value},${r.value} 0 0 ${s(1)} ${xl},${bY + dy} ` +
        `L${xl},${nY + (isDown.value ? r.value : -r.value)} A${r.value},${r.value} 0 0 ${s(0)} ${xl + r.value},${nY} ` +
        `L${xr - r.value},${nY} A${r.value},${r.value} 0 0 ${s(0)} ${xr},${nY + (isDown.value ? r.value : -r.value)} ` +
        `L${xr},${bY + dy} A${r.value},${r.value} 0 0 ${s(1)} ${xr + r.value},${bY} `
    )
}

const buildTopPath = () => {
    let d = ''
    if (mr.value > 0) {
        d = (
            `M0,0 L${VW.value},0 ` +
            `L${VW.value},${barY.value - mr.value} ` +
            `A${mr.value},${mr.value} 0 0 1 ${VW.value - mr.value},${barY.value} ` +
            `L${mr.value},${barY.value} ` +
            `A${mr.value},${mr.value} 0 0 1 0,${barY.value - mr.value} ` +
            `Z `
        )
    } else {
        d = `M0,0 L${VW.value},0 L${VW.value},${barY.value} L0,${barY.value} Z `
    }

    tabs.value.forEach(({ xl, xr, type }) => {
        if (type === 'in') {
            const nY = notchEndY.value
            const x1 = xl - 1, x2 = xr + 1
            const y1 = barY.value - 1
            const y2 = isDown.value ? nY + 1 : nY - 1
            d += `M${x1},${y1} L${x2},${y1} L${x2},${y2} L${x1},${y2} Z `
        }
    })
    return d
}

const buildRackPath = () => {
    let d = ''
    if (mr.value > 0) {
        d = `M0,${barY.value + mr.value} A${mr.value},${mr.value} 0 0 1 ${mr.value},${barY.value} `
    } else {
        d = `M0,${barY.value} `
    }

    tabs.value.forEach(({ xl, xr, type }) => {
        d += type === 'out' ? tabOut(xl, xr) : notchIn(xl, xr)
    })

    if (mr.value > 0) {
        d += `L${VW.value - mr.value},${barY.value} A${mr.value},${mr.value} 0 0 1 ${VW.value},${barY.value + mr.value} `
    } else {
        d += `L${VW.value},${barY.value} `
    }

    d += `L${VW.value},${barBottom.value} L0,${barBottom.value} Z`
    return d
}

const topPath = computed(() => buildTopPath())
const rackPath = computed(() => buildRackPath())
const viewBox = computed(() => `0 0 ${VW.value} ${totalH.value}`)
const transform = computed(() => props.flip ? `scale(1,-1) translate(0,-${totalH.value})` : undefined)
</script>

<template>
    <div class="rack-divider-wrapper">
        <svg
            :viewBox="viewBox"
            xmlns="http://www.w3.org/2000/svg"
            class="rack-divider"
            aria-hidden="true"
            preserveAspectRatio="none"
        >
            <g :transform="transform">
                <path :d="topPath" :fill="topColor" />
                <path :d="rackPath" :fill="color" />
            </g>
        </svg>
    </div>
</template>

<style scoped>
.rack-divider-wrapper {
    display: block;
    width: 100%;
    margin: 0;
    padding: 0;
    line-height: 0;
    font-size: 0;
    overflow: hidden;
}

.rack-divider {
    display: block;
    width: 100%;
    height: auto;
    margin: 0;
    padding: 0;
    line-height: 0;
    font-size: 0;
    overflow: hidden;
    vertical-align: top;
}

/* Smooth transitions for responsive changes */
.rack-divider path {
    transition: d 0.2s ease-out;
}

/* Responsive media queries for container */
@media (max-width: 640px) {
    .rack-divider-wrapper {
        transform: scaleY(0.9);
    }
}
</style>
