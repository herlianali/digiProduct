<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs" />

        <CardComponent title="Testimonials">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard
                    title="Total Testimonials"
                    :value="testimonials.length"
                />
                <StatCard
                    title="Active"
                    :value="activeCount"
                />
                <StatCard
                    title="Inactive"
                    :value="inactiveCount"
                />
                <StatCard
                    title="Rating 5★"
                    :value="ratingFiveCount"
                />
            </div>

            <!-- Action Bar -->
            <div class="flex justify-between items-center mb-6">
                <Link
                    href="/admin/testimonials/create"
                    class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm"
                >
                    + Add Testimonial
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
                v-if="testimonials.length"
                :columns="columns"
                :rows="testimonials"
            >
                <template #avatar="{ value }">
                    <img
                        v-if="value"
                        :src="value"
                        class="w-10 h-10 rounded-full object-cover"
                    />
                    <span v-else class="text-gray-400 text-xs">
                        No Image
                    </span>
                </template>

                <template #rating="{ value }">
                    <span class="text-yellow-500 font-semibold">
                        ⭐ {{ value }}/5
                    </span>
                </template>

                <template #is_active="{ value }">
                    <span
                        class="px-3 py-1 rounded-full text-xs"
                        :class="statusBadge(value)"
                    >
                        {{ value ? 'Active' : 'Inactive' }}
                    </span>
                </template>

                <template #actions="{ row }">
                    <Link
                        :href="`/admin/testimonials/${row.id}/edit`"
                        class="px-3 py-1 bg-brand-100 text-brand-700 rounded-md text-sm"
                    >
                        Edit
                    </Link>
                </template>
            </TableComponent>

            <!-- Empty -->
            <div
                v-else
                class="text-center py-12 border-2 border-dashed rounded-lg text-gray-500"
            >
                Belum ada testimonial
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
    testimonials: Array
})

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Testimonials', active: true }
]

const columns = [
    { key: 'avatar', label: 'Avatar' },
    { key: 'name', label: 'Name' },
    { key: 'rating', label: 'Rating' },
    { key: 'is_active', label: 'Status' },
    { key: 'actions', label: 'Aksi' }
]

const activeCount = computed(() =>
    props.testimonials.filter(t => t.is_active).length
)

const inactiveCount = computed(() =>
    props.testimonials.filter(t => !t.is_active).length
)

const ratingFiveCount = computed(() =>
    props.testimonials.filter(t => t.rating === 5).length
)

const statusBadge = (active) => {
    return active
        ? 'bg-green-100 text-green-700'
        : 'bg-gray-100 text-gray-600'
}

const refreshData = () => {
    router.reload({ preserveScroll: true })
}
</script>
