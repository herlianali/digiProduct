<!-- Pages/Orders/Show.vue -->
<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <!-- ── KOLOM KIRI: Info Order + Items ── -->
            <div class="xl:col-span-2 space-y-6">

                <!-- Header Card -->
                <CardComponent>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-400 font-medium mb-1">ORDER NUMBER</p>
                            <h2 class="text-2xl font-black text-gray-800 tracking-tight font-mono">
                                {{ order.order_number }}
                            </h2>
                            <p class="text-sm text-gray-400 mt-1">{{ order.created_at }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <!-- Payment badge -->
                            <span :class="paymentBadge(order.payment_status)" class="text-sm px-4 py-1.5 rounded-full font-bold">
                                💳 {{ order.payment_status }}
                            </span>
                            <!-- Order status badge -->
                            <span :class="orderBadge(order.status)" class="text-sm px-4 py-1.5 rounded-full font-bold">
                                📦 {{ order.status }}
                            </span>
                        </div>
                    </div>
                </CardComponent>

                <!-- Order Items -->
                <CardComponent title="Item Pesanan">
                    <div class="space-y-3">
                        <div
                            v-for="(item, i) in order.items"
                            :key="i"
                            class="flex items-center justify-between gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-brand-100 flex items-center justify-center flex-shrink-0">
                                    <CubeIcon class="w-5 h-5 text-brand-600" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ item.product_title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        $ {{ formatNumber(item.price) }} × {{ item.quantity }}
                                    </p>
                                </div>
                            </div>
                            <p class="font-bold text-gray-800 text-sm whitespace-nowrap">
                                $ {{ formatNumber(item.total) }}
                            </p>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="mt-5 pt-4 border-t space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span>$ {{ formatNumber(order.subtotal) }}</span>
                        </div>
                        <div v-if="order.discount > 0" class="flex justify-between text-sm text-green-600">
                            <span>Diskon</span>
                            <span>− $ {{ formatNumber(order.discount) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-black text-gray-800 pt-2 border-t">
                            <span>Total</span>
                            <span class="text-brand-700">$ {{ formatNumber(order.total) }}</span>
                        </div>
                    </div>
                </CardComponent>

                <!-- Update Status -->
                <CardComponent title="Update Status Order">
                    <form @submit.prevent="submitUpdate" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    Status Pesanan
                                </label>
                                <select
                                    v-model="updateForm.status"
                                    class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring focus:ring-brand-200 focus:border-brand-400 outline-none"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    Status Pembayaran
                                </label>
                                <select
                                    v-model="updateForm.payment_status"
                                    class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring focus:ring-brand-200 focus:border-brand-400 outline-none"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                :disabled="isUpdating"
                                class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white rounded-lg text-sm font-semibold flex items-center gap-2 transition-colors"
                            >
                                <svg v-if="isUpdating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <CheckIcon v-else class="w-4 h-4" />
                                {{ isUpdating ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>

                            <Transition name="fade">
                                <span v-if="updateSuccess" class="text-green-600 text-sm font-medium flex items-center gap-1">
                                    <CheckCircleIcon class="w-4 h-4" />
                                    Berhasil diperbarui!
                                </span>
                            </Transition>
                        </div>
                    </form>
                </CardComponent>
            </div>

            <!-- ── KOLOM KANAN: Data Customer + Aksi ── -->
            <div class="space-y-6">

                <!-- Customer Info -->
                <CardComponent title="Data Pemesan">
                    <div class="space-y-4">
                        <!-- Avatar placeholder -->
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-brand-700 font-black text-lg">
                                    {{ order.customer?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                </span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ order.customer?.name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">Customer</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2 border-t">
                            <InfoRow label="Email" :value="order.customer?.email" icon="✉️" />
                            <InfoRow label="Phone" :value="order.customer?.phone" icon="📱" />
                            <InfoRow label="WhatsApp" :value="order.customer?.whatsapp" icon="💬" />
                            <InfoRow label="Perusahaan" :value="order.customer?.company" icon="🏢" />
                            <InfoRow label="Kota" :value="order.customer?.city" icon="📍" />
                            <InfoRow
                                v-if="order.customer?.notes"
                                label="Catatan"
                                :value="order.customer?.notes"
                                icon="📝"
                            />
                        </div>
                    </div>
                </CardComponent>

                <!-- Quick Actions -->
                <CardComponent title="Quick Actions">
                    <div class="space-y-3">
                        <!-- WA Customer -->
                        <a
                            v-if="order.customer?.whatsapp || order.customer?.phone"
                            :href="waCustomerUrl"
                            target="_blank"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-green-50 hover:bg-green-100 border border-green-200 rounded-xl transition-colors text-sm font-semibold text-green-700"
                        >
                            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Chat Customer via WA
                        </a>

                        <!-- Email customer -->
                        <a
                            v-if="order.customer?.email"
                            :href="`mailto:${order.customer.email}?subject=Order ${order.order_number}`"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition-colors text-sm font-semibold text-blue-700"
                        >
                            <EnvelopeIcon class="w-5 h-5 flex-shrink-0" />
                            Kirim Email
                        </a>

                        <!-- Konfirmasi pembayaran -->
                        <button
                            v-if="order.payment_status !== 'paid'"
                            @click="confirmPayment"
                            :disabled="isConfirming"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-xl transition-colors text-sm font-semibold text-brand-700 disabled:opacity-50"
                        >
                            <CheckBadgeIcon class="w-5 h-5 flex-shrink-0" />
                            {{ isConfirming ? 'Memproses...' : 'Konfirmasi Pembayaran' }}
                        </button>

                        <!-- Mark completed -->
                        <button
                            v-if="order.status !== 'completed' && order.status !== 'cancelled'"
                            @click="markCompleted"
                            :disabled="isCompleting"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition-colors text-sm font-semibold text-emerald-700 disabled:opacity-50"
                        >
                            <TruckIcon class="w-5 h-5 flex-shrink-0" />
                            {{ isCompleting ? 'Memproses...' : 'Tandai Selesai' }}
                        </button>

                        <!-- Cancel -->
                        <button
                            v-if="order.status !== 'cancelled' && order.status !== 'completed'"
                            @click="showCancelConfirm = true"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl transition-colors text-sm font-semibold text-red-600"
                        >
                            <XCircleIcon class="w-5 h-5 flex-shrink-0" />
                            Batalkan Order
                        </button>
                    </div>
                </CardComponent>

                <!-- Delete -->
                <CardComponent>
                    <button
                        @click="showDeleteConfirm = true"
                        class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 font-medium transition-colors"
                    >
                        <TrashIcon class="w-4 h-4" />
                        Hapus Order Ini
                    </button>
                </CardComponent>
            </div>
        </div>

        <!-- ── Modal Konfirmasi Cancel ── -->
        <ConfirmModal
            v-if="showCancelConfirm"
            title="Batalkan Order?"
            :message="`Order ${order.order_number} akan dibatalkan. Tindakan ini tidak dapat diurungkan.`"
            confirm-label="Ya, Batalkan"
            confirm-class="bg-red-600 hover:bg-red-700 text-white"
            @confirm="cancelOrder"
            @cancel="showCancelConfirm = false"
        />

        <!-- ── Modal Konfirmasi Delete ── -->
        <ConfirmModal
            v-if="showDeleteConfirm"
            title="Hapus Order?"
            :message="`Data order ${order.order_number} beserta semua item akan dihapus permanen.`"
            confirm-label="Ya, Hapus"
            confirm-class="bg-red-600 hover:bg-red-700 text-white"
            @confirm="deleteOrder"
            @cancel="showDeleteConfirm = false"
        />
    </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardLayout      from '@/Layouts/DashboardLayout.vue'
import CardComponent        from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import ConfirmModal         from '@/Components/Admin/ConfirmModal.vue'
import {
    CubeIcon, CheckIcon, CheckCircleIcon, CheckBadgeIcon,
    TruckIcon, XCircleIcon, TrashIcon, EnvelopeIcon,
} from '@heroicons/vue/24/outline'

// ── Komponen kecil inline ──────────────────────────────────────────
const InfoRow = {
    props: ['label', 'value', 'icon'],
    template: `
        <div v-if="value" class="flex items-start gap-3">
            <span class="text-base leading-none mt-0.5 flex-shrink-0">{{ icon }}</span>
            <div class="min-w-0">
                <p class="text-xs text-gray-400 font-medium">{{ label }}</p>
                <p class="text-sm text-gray-700 font-medium break-words">{{ value }}</p>
            </div>
        </div>
    `,
}

const props = defineProps({
    order: Object,
})

const breadcrumbs = [
    { label: 'Dashboard', href: '/admin/dashboard' },
    { label: 'Orders',    href: '/admin/orders' },
    { label: props.order?.order_number ?? 'Detail', active: true },
]

// ── Update form ────────────────────────────────────────────────────
const updateForm = ref({
    status:         props.order?.status         ?? 'pending',
    payment_status: props.order?.payment_status ?? 'pending',
})

const isUpdating     = ref(false)
const updateSuccess  = ref(false)
const isConfirming   = ref(false)
const isCompleting   = ref(false)
const showCancelConfirm = ref(false)
const showDeleteConfirm = ref(false)

// ── WA URL untuk chat customer ─────────────────────────────────────
const waCustomerUrl = computed(() => {
    const raw = props.order?.customer?.whatsapp ?? props.order?.customer?.phone ?? ''
    if (!raw) return '#'
    let n = raw.replace(/\D/g, '')
    if (n.startsWith('0')) n = '62' + n.slice(1)
    const msg = `Halo ${props.order?.customer?.name ?? 'Kak'}, terkait pesanan Anda *${props.order?.order_number}* — `
    return `https://wa.me/${n}?text=${encodeURIComponent(msg)}`
})

// ── Helpers ────────────────────────────────────────────────────────
const formatNumber = (val) =>
    Number(val ?? 0).toLocaleString('id-ID')

const paymentBadge = (s) => ({
    paid:     'bg-green-100 text-green-700',
    pending:  'bg-yellow-100 text-yellow-700',
    failed:   'bg-red-100 text-red-700',
    refunded: 'bg-gray-100 text-gray-600',
}[s] ?? 'bg-gray-100 text-gray-600')

const orderBadge = (s) => ({
    pending:    'bg-yellow-50 text-yellow-700',
    processing: 'bg-blue-50 text-blue-700',
    shipped:    'bg-indigo-50 text-indigo-700',
    completed:  'bg-green-50 text-green-700',
    cancelled:  'bg-red-50 text-red-700',
}[s] ?? 'bg-gray-50 text-gray-600')

// ── Actions ────────────────────────────────────────────────────────
const submitUpdate = () => {
    isUpdating.value = true
    router.put(`/admin/orders/${props.order.id}`, updateForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            updateSuccess.value = true
            setTimeout(() => { updateSuccess.value = false }, 2500)
        },
        onFinish: () => { isUpdating.value = false },
    })
}

const confirmPayment = () => {
    isConfirming.value = true
    router.put(`/admin/orders/${props.order.id}`, {
        status:         updateForm.value.status,
        payment_status: 'paid',
    }, {
        preserveScroll: true,
        onSuccess: () => { updateForm.value.payment_status = 'paid' },
        onFinish:  () => { isConfirming.value = false },
    })
}

const markCompleted = () => {
    isCompleting.value = true
    router.put(`/admin/orders/${props.order.id}`, {
        status:         'completed',
        payment_status: updateForm.value.payment_status,
    }, {
        preserveScroll: true,
        onSuccess: () => { updateForm.value.status = 'completed' },
        onFinish:  () => { isCompleting.value = false },
    })
}

const cancelOrder = () => {
    showCancelConfirm.value = false
    router.put(`/admin/orders/${props.order.id}`, {
        status:         'cancelled',
        payment_status: updateForm.value.payment_status,
    }, { preserveScroll: true })
}

const deleteOrder = () => {
    showDeleteConfirm.value = false
    router.delete(`/admin/orders/${props.order.id}`)
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
