<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs" />

        <CardComponent title="Company Slides">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard
                    title="Total Slides"
                    :value="slides.length"
                />
                <StatCard
                    title="Image Type"
                    :value="imageCount"
                />
                <StatCard
                    title="Text Type"
                    :value="textCount"
                />
                <StatCard
                    title="Active"
                    :value="activeCount"
                />
            </div>

            <!-- Action Bar -->
            <div class="flex justify-between items-center mb-6">
                <Link
                    href="/admin/company-slides/create"
                    class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm"
                >
                    + Add Slide
                </Link>

                <button
                    @click="refreshData"
                    class="px-3 py-2 border rounded-lg flex items-center gap-2"
                >
                    <ArrowPathIcon class="w-4 h-4" />
                    Refresh
                </button>
            </div>

            <!-- Table -->
            <TableComponent
                v-if="slides.length"
                :columns="columns"
                :rows="slides"
            >
                <template #type="{ value }">
                    <span class="px-3 py-1 rounded-full text-xs"
                          :class="typeBadge(value)">
                        {{ value }}
                    </span>
                </template>

                <template #is_active="{ value }">
                    <span class="px-3 py-1 rounded-full text-xs"
                          :class="statusBadge(value)">
                        {{ value ? 'Active' : 'Inactive' }}
                    </span>
                </template>

                <template #actions="{ row }">
                    <Link
                        :href="`/admin/company-slides/${row.id}/edit`"
                        class="px-3 py-1 bg-brand-100 text-brand-700 rounded-md text-sm"
                    >
                        Edit
                    </Link>
                </template>
            </TableComponent>

            <!-- Empty State -->
            <div
                v-else
                class="text-center py-12 border-2 border-dashed rounded-lg text-gray-500"
            >
                Belum ada company slide
            </div>
        </CardComponent>
    </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import TableComponent from '@/Components/Admin/TableComponent.vue'
import StatCard from '@/Components/Admin/StatCard.vue'

import { ArrowPathIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    slides: Array
})

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Company Slides', active: true }
]

const columns = [
    { key: 'type', label: 'Type' },
    { key: 'title', label: 'Title' },
    { key: 'sort_order', label: 'Order' },
    { key: 'is_active', label: 'Status' },
    { key: 'actions', label: 'Aksi' }
]

const imageCount = computed(() =>
    props.slides.filter(s => s.type === 'image').length
)

const textCount = computed(() =>
    props.slides.filter(s => s.type === 'text').length
)

const activeCount = computed(() =>
    props.slides.filter(s => s.is_active).length
)

const typeBadge = (type) => {
    return type === 'image'
        ? 'bg-blue-100 text-blue-700'
        : 'bg-purple-100 text-purple-700'
}

const statusBadge = (active) => {
    return active
        ? 'bg-green-100 text-green-700'
        : 'bg-gray-100 text-gray-600'
}

const refreshData = () => {
    router.reload({ preserveScroll: true })
}
</script>
