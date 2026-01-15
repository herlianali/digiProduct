<!--
|--------------------------------------------------------------------------
| BREADCRUMBS COMPONENT
|--------------------------------------------------------------------------
|
| CARA PENGGUNAAN:
|
| <Breadcrumbs :items="breadcrumbs" />
|
| const breadcrumbs = [
|   { label: 'Dashboard', href: '/dashboard' },
|   { label: 'Produk', href: '/products' },
|   { label: 'Tambah Produk', active: true },
| ]
|
|--------------------------------------------------------------------------
-->

<template>
    <nav class="flex items-center text-sm text-gray-500 pb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1">

            <li
                v-for="(item, index) in items"
                :key="index"
                class="inline-flex items-center"
            >
                <!-- Link -->
                <template v-if="!item.active">
                    <Link
                        :href="item.href"
                        class="inline-flex items-center hover:text-indigo-600 transition"
                    >
                        {{ item.label }}
                    </Link>

                    <!-- Separator -->
                    <svg
                        class="w-4 h-4 mx-2 text-gray-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </template>

                <!-- Active -->
                <span
                    v-else
                    class="font-medium text-gray-800"
                    aria-current="page"
                >
                    {{ item.label }}
                </span>
            </li>

        </ol>
    </nav>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';
    defineProps({
        items: {
            type: Array,
            required: true,
            // Format:
            // [
            //   { label: String, href?: String, active?: Boolean }
            // ]
        },
    })
</script>
