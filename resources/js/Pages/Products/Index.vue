<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Management Produk">
            <div class="flex justify-end mb-4">
                <Link
                    href="/products/create"
                    class="px-4 py-2 bg-indigo-600 text-white rounded"
                >
                    + Tambah Produk
                </Link>
            </div>

            <TableComponent
                :columns="columns"
                :rows="products"
                :per-page="5"
            />
        </CardComponent>
    </DashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import TableComponent from '@/Components/Admin/TableComponent.vue'

defineProps({
    products: Array
})

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Produk', active: true },
]

const columns = [
    { key: 'title', label: 'Nama Produk', sortable: true },
    { key: 'type', label: 'Tipe', sortable: true },
    { key: 'price', label: 'Harga', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
]
</script>
