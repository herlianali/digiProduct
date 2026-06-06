<template>
    <Component :is="component" v-bind="pageProps" />
</template>

<script setup>
import { computed } from 'vue'
import { usePage }  from '@inertiajs/vue3'
import { provideCart } from '@/Composables/useCart'

provideCart()

const page      = usePage()
const component = computed(() => page.component)
const pageProps = computed(() => page.props)
</script>
