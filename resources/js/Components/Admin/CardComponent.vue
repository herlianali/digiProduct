<!--
|--------------------------------------------------------------------------
| CARD COMPONENT (Reusable)
|--------------------------------------------------------------------------
|
| CARA PENGGUNAAN:
|
| 1. Card sederhana
| --------------------------------------------------
| <Card title="Statistik">
|   <p>Total penjualan hari ini</p>
| </Card>
|
| 2. Card dengan footer
| --------------------------------------------------
| <Card title="Produk">
|   <p>Jumlah produk: 120</p>
|
|   <template #footer>
|     <button class="text-indigo-600 text-sm hover:underline">
|       Lihat detail
|     </button>
|   </template>
| </Card>
|
| 3. Card dengan header custom
| --------------------------------------------------
| <Card>
|   <template #header>
|     <div class="flex justify-between items-center">
|       <h3 class="font-semibold text-gray-800">Order</h3>
|       <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">
|         Active
|       </span>
|     </div>
|   </template>
|
|   <p>Order aktif: 24</p>
| </Card>
|
| CATATAN:
| - Gunakan prop `title` untuk header sederhana
| - Gunakan slot `header` untuk header custom
| - Gunakan slot `footer` untuk action / tombol
|
|--------------------------------------------------------------------------
-->

<template>
    <div
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition"
    >
        <!-- Header -->
        <div v-if="title || $slots.header" class="mb-3">
            <slot name="header">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ title }}
                </h3>
            </slot>
        </div>

        <!-- Content -->
        <div class="text-gray-600">
            <slot />
        </div>

        <!-- Footer -->
        <div
            v-if="$slots.footer"
            class="mt-4 pt-4 border-t border-gray-100"
        >
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
})
</script>
