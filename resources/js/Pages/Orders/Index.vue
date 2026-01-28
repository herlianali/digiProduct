<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Management Order">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard
                    title="Total Order"
                    :value="meta.total || orderList.length"
                    :icon="ShoppingBagIcon"
                />
                <StatCard
                    title="Paid"
                    :value="paidCount"
                    :icon="CheckCircleIcon"
                />
                <StatCard
                    title="Pending"
                    :value="pendingCount"
                    :icon="ClockIcon"
                />
                <StatCard
                    title="Completed"
                    :value="completedCount"
                    :icon="TruckIcon"
                />
            </div>

            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                <div class="flex gap-3">
                    <select
                        v-model="statusFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200"
                        @change="filterOrder"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <button
                    @click="refreshData"
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2"
                >
                    <ArrowPathIcon class="w-4 h-4" />
                    Refresh
                </button>
            </div>

            <!-- Table -->
            <TableComponent
                v-if="orderList.length"
                :columns="columns"
                :rows="orderList"
                :per-page="10"
            >
                <template #payment_status="{ value }">
                    <span :class="paymentBadge(value)">
                        {{ value }}
                    </span>
                </template>

                <template #status="{ value }">
                    <span :class="orderBadge(value)">
                        {{ value }}
                    </span>
                </template>

                <template #actions="{ row }">
                    <Link
                        :href="`/orders/${row.id}`"
                        class="px-3 py-1 bg-brand-100 text-brand-700 rounded-md hover:bg-brand-200 flex items-center gap-1 text-sm"
                    >
                        <EyeIcon class="w-4 h-4" />
                        Detail
                    </Link>
                </template>
            </TableComponent>

            <!-- Empty -->
            <div v-else class="text-center py-12 border-2 border-dashed rounded-lg">
                <p class="text-gray-500">Belum ada order</p>
            </div>
        </CardComponent>
    </DashboardLayout>
</template>
<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import TableComponent from '@/Components/Admin/TableComponent.vue'
import StatCard from '@/Components/Admin/StatCard.vue'

import {
  ShoppingBagIcon,
  CheckCircleIcon,
  ClockIcon,
  TruckIcon,
  EyeIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    orders: Array,
    meta: Object
})

const statusFilter = ref('')

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Orders', active: true }
]

const columns = [
    { key: 'order_number', label: 'Order ID' },
    { key: 'customer_name', label: 'Customer' },
    { key: 'total', label: 'Total' },
    { key: 'payment_status', label: 'Payment' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Tanggal' },
    { key: 'actions', label: 'Aksi' }
]

const orderList = computed(() => props.orders || [])

const paidCount = computed(() =>
    orderList.value.filter(o => o.payment_status === 'paid').length
)

const pendingCount = computed(() =>
    orderList.value.filter(o => o.payment_status === 'pending').length
)

const completedCount = computed(() =>
    orderList.value.filter(o => o.status === 'completed').length
)

const paymentBadge = (status) => {
    return {
        paid: 'bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs',
        pending: 'bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs',
        failed: 'bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs'
    }[status] || 'bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs'
}

const orderBadge = (status) => {
    return {
        pending: 'bg-yellow-50 text-yellow-700',
        shipped: 'bg-blue-50 text-blue-700',
        completed: 'bg-green-50 text-green-700',
        cancelled: 'bg-red-50 text-red-700'
    }[status] + ' px-3 py-1 rounded-full text-xs'
}

const filterOrder = () => {
    router.get('/orders', { status: statusFilter.value }, {
        preserveState: true,
        preserveScroll: true
    })
}

const refreshData = () => {
    router.reload({ preserveScroll: true })
}
</script>
