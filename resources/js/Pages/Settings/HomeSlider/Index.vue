<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Home Sliders">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <StatCard title="Total Slider" :value="sliders.length"/>
                <StatCard title="Active" :value="activeCount"/>
                <StatCard title="Inactive" :value="inactiveCount"/>
            </div>

            <!-- Action Bar -->
            <div class="flex justify-between mb-6">
                <Link
                    href="/admin/home-sliders/create"
                    class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm"
                >
                    + Add Slider
                </Link>
            </div>

            <TableComponent :columns="columns" :rows="sliders">
                <template #image="{ value }">
                    <img :src="value" class="w-20 h-12 object-cover rounded"/>
                </template>

                <template #is_active="{ value }">
                    <span :class="badge(value)">
                        {{ value ? 'Active' : 'Inactive' }}
                    </span>
                </template>

                <template #actions="{ row }">
                    <Link
                        :href="`/admin/home-sliders/${row.id}/edit`"
                        class="text-brand-600 text-sm"
                    >
                        Edit
                    </Link>
                </template>
            </TableComponent>
        </CardComponent>
    </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({ sliders: Array })

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Home Sliders', active: true }
]

const columns = [
    { key: 'title', label: 'Title' },
    { key: 'image', label: 'Image' },
    { key: 'sort_order', label: 'Order' },
    { key: 'is_active', label: 'Status' },
    { key: 'actions', label: 'Aksi' }
]

const activeCount = computed(() =>
    props.sliders.filter(s => s.is_active).length
)

const inactiveCount = computed(() =>
    props.sliders.filter(s => !s.is_active).length
)

const badge = (active) =>
    active
        ? 'bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs'
        : 'bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs'
</script>
