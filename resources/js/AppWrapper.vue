<!-- resources/js/AppWrapper.vue -->
<!--
  Wrapper root yang mendaftarkan cart provider.
  Di-set sebagai resolve target di app.js.
-->
<template>
    <Component :is="component" v-bind="pageProps" />
</template>

<script setup>
import { computed } from 'vue'
import { usePage }  from '@inertiajs/vue3'
import { provideCart } from '@/composables/useCart'

// Daftarkan cart di root — semua komponen child bisa useCart()
provideCart()

const page      = usePage()
const component = computed(() => page.component)
const pageProps = computed(() => page.props)
</script>
