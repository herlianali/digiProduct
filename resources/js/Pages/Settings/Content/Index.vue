<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Content Settings">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <StatCard title="Total Settings" :value="settings.length"/>
                <StatCard title="Text Type" :value="textCount"/>
                <StatCard title="Other Type" :value="otherCount"/>
            </div>

            <!-- Action Bar -->
            <div class="flex justify-between mb-6">
                <Link
                    href="/admin/content-settings/create"
                    class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm"
                >
                    + Add Setting
                </Link>

                <button
                    @click="refreshData"
                    class="px-3 py-2 border rounded-lg flex items-center gap-2"
                >
                    <ArrowPathIcon class="w-4 h-4"/>
                    Refresh
                </button>
            </div>

            <TableComponent
                :columns="columns"
                :rows="settings"
            >
                <template #actions="{ row }">
                    <Link
                        :href="`/admin/content-settings/${row.id}/edit`"
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
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import TableComponent from '@/Components/Admin/TableComponent.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import { ArrowPathIcon } from '@heroicons/vue/24/outline'

const props = defineProps({ settings: Array })

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Content Settings', active: true }
]

const columns = [
    { key: 'key', label: 'Key' },
    { key: 'value', label: 'Value' },
    { key: 'type', label: 'Type' },
    { key: 'actions', label: 'Aksi' }
]

const textCount = computed(() =>
    props.settings.filter(s => s.type === 'text').length
)

const otherCount = computed(() =>
    props.settings.filter(s => s.type !== 'text').length
)

const refreshData = () => router.reload()
</script>
