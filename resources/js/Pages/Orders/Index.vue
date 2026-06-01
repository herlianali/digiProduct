<!-- Pages/Orders/Index.vue -->
<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Management Order">
            <!-- Statistik -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <StatCard title="Total Order"  :value="meta.total || orderList.length" :icon="ShoppingBagIcon" />
                <StatCard title="Paid"         :value="paidCount"      :icon="CheckCircleIcon" />
                <StatCard title="Pending"      :value="pendingCount"   :icon="ClockIcon" />
                <StatCard title="Completed"    :value="completedCount" :icon="TruckIcon" />
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
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2 text-sm"
                >
                    <ArrowPathIcon class="w-4 h-4" />
                    Refresh
                </button>
            </div>

            <!-- Table -->
            <div v-if="orderList.length" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Order ID</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Customer</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Kontak</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Total</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Payment</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="order in orderList"
                            :key="order.id"
                            class="border-b hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-4 py-3 font-mono font-semibold text-brand-700 text-xs">
                                {{ order.order_number }}
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800">{{ order.customer_name ?? '—' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ order.customer_email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <a
                                    v-if="order.customer_phone"
                                    :href="`https://wa.me/${normalizePhone(order.customer_phone)}`"
                                    target="_blank"
                                    class="flex items-center gap-1 text-green-600 hover:text-green-700 text-xs font-medium"
                                >
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    {{ order.customer_phone }}
                                </a>
                                <span v-else class="text-gray-300 text-xs">—</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-800">
                                $ {{ order.total }}
                            </td>
                            <td class="px-4 py-3">
                                <span :class="paymentBadge(order.payment_status)">
                                    {{ order.payment_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="orderBadge(order.status)">
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                                {{ order.created_at }}
                            </td>
                            <td class="px-4 py-3">
                                <Link
                                    :href="`/admin/orders/${order.id}`"
                                    class="px-3 py-1.5 bg-brand-100 text-brand-700 rounded-md hover:bg-brand-200 flex items-center gap-1 text-xs font-medium w-fit"
                                >
                                    <EyeIcon class="w-3.5 h-3.5" />
                                    Detail
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty -->
            <div v-else class="text-center py-12 border-2 border-dashed rounded-lg">
                <ShoppingBagIcon class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500">Belum ada order</p>
            </div>
        </CardComponent>
    </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout      from '@/Layouts/DashboardLayout.vue'
import CardComponent        from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import StatCard             from '@/Components/Admin/StatCard.vue'
import {
    ShoppingBagIcon, CheckCircleIcon, ClockIcon,
    TruckIcon, EyeIcon, ArrowPathIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    orders: Array,
    meta:   Object,
})

const statusFilter = ref('')
const breadcrumbs  = [
    { label: 'Dashboard', href: '/admin/dashboard' },
    { label: 'Orders', active: true },
]

const orderList = computed(() => props.orders || [])

const paidCount      = computed(() => orderList.value.filter(o => o.payment_status === 'paid').length)
const pendingCount   = computed(() => orderList.value.filter(o => o.payment_status === 'pending').length)
const completedCount = computed(() => orderList.value.filter(o => o.status === 'completed').length)

const paymentBadge = (status) => ({
    paid:    'bg-green-100 text-green-700 px-2.5 py-0.5 rounded-full text-xs font-semibold',
    pending: 'bg-yellow-100 text-yellow-700 px-2.5 py-0.5 rounded-full text-xs font-semibold',
    failed:  'bg-red-100 text-red-700 px-2.5 py-0.5 rounded-full text-xs font-semibold',
}[status] ?? 'bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full text-xs font-semibold')

const orderBadge = (status) => ({
    pending:    'bg-yellow-50 text-yellow-700',
    processing: 'bg-blue-50 text-blue-700',
    shipped:    'bg-indigo-50 text-indigo-700',
    completed:  'bg-green-50 text-green-700',
    cancelled:  'bg-red-50 text-red-700',
}[status] ?? 'bg-gray-50 text-gray-600') + ' px-2.5 py-0.5 rounded-full text-xs font-semibold'

const normalizePhone = (phone) => {
    if (!phone) return ''
    let n = phone.replace(/\D/g, '')
    if (n.startsWith('0')) n = '62' + n.slice(1)
    return n
}

const filterOrder = () => {
    router.get('/admin/orders', { status: statusFilter.value }, {
        preserveState: true, preserveScroll: true,
    })
}

const refreshData = () => router.reload({ preserveScroll: true })
</script>
