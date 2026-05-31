<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs" />

        <CardComponent title="Management Voucher">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <StatCard title="Total Voucher"    :value="voucherList.length"  :icon="TicketIcon" />
                <StatCard title="Aktif"            :value="activeCount"         :icon="CheckCircleIcon" />
                <StatCard title="Tidak Aktif"      :value="inactiveCount"       :icon="XCircleIcon" />
            </div>

            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex flex-wrap items-center gap-3">
                    <Link
                        href="/admin/vouchers/create"
                        class="px-4 py-2 bg-brand-500 text-black rounded-lg hover:bg-brand-600 transition flex items-center gap-2 text-sm font-medium"
                    >
                        <PlusIcon class="w-4 h-4" />
                        Tambah Voucher
                    </Link>

                    <select
                        v-model="activeFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200 focus:border-brand-400"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status</option>
                        <option value="true">Aktif</option>
                        <option value="false">Tidak Aktif</option>
                    </select>

                    <button
                        v-if="activeFilter"
                        @click="resetFilters"
                        class="text-xs text-gray-400 hover:text-red-500 transition underline"
                    >
                        Reset
                    </button>
                </div>

                <button
                    @click="refreshData"
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm"
                >
                    <ArrowPathIcon class="w-4 h-4" />
                    Refresh
                </button>
            </div>

            <!-- Table -->
            <TableComponent
                v-if="voucherList.length > 0"
                :columns="columns"
                :rows="voucherList"
                :per-page="10"
                :search="filters.search"
                @search="handleSearch"
            >
                <template #is_active="{ value }">
                    <span :class="value ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200'"
                          class="px-2.5 py-1 text-xs font-semibold rounded-full inline-flex items-center">
                        {{ value ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </template>

                <template #discount_pct="{ value }">
                    <span class="font-semibold text-brand-600">{{ value ?? '-' }}{{ value ? '%' : '' }}</span>
                </template>

                <template #expires_at="{ value }">
                    <span :class="isExpired(value) ? 'text-red-500' : 'text-gray-700'">
                        {{ value ? formatDate(value) : 'Tidak ada batas' }}
                    </span>
                </template>

                <template #actions="{ row }">
                    <div class="flex gap-2">
                        <Link
                            :href="`/admin/vouchers/${row.id}/edit`"
                            class="px-3 py-1 bg-brand-100 text-brand-700 rounded-md hover:bg-brand-200 transition flex items-center gap-1 text-sm"
                            title="Edit"
                        >
                            <PencilIcon class="w-4 h-4" />
                        </Link>
                        <button
                            @click="confirmDelete(row)"
                            class="px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition flex items-center gap-1 text-sm"
                            title="Hapus"
                        >
                            <TrashIcon class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </TableComponent>

            <!-- Empty State -->
            <div v-else class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                <p class="text-gray-500">Belum ada voucher</p>
                <Link
                    href="/admin/vouchers/create"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-brand-500 text-black rounded-lg hover:bg-brand-600 transition font-medium text-sm"
                >
                    <PlusIcon class="w-4 h-4" />
                    Tambah Voucher Pertama
                </Link>
            </div>

            <div v-if="voucherList.length > 0" class="mt-4 text-sm text-gray-500">
                Menampilkan {{ voucherList.length }} dari {{ meta.total || voucherList.length }} voucher
            </div>
        </CardComponent>

        <!-- Modal Hapus -->
        <ModalComponent :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus Voucher</h3>
                </div>
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus voucher
                    <strong>"{{ selectedVoucher?.code }}"</strong>?
                    <br>
                    <span class="text-red-500 font-medium">Tindakan ini tidak dapat dibatalkan.</span>
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        Batal
                    </button>
                    <button
                        @click="deleteVoucher"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition flex items-center gap-2"
                        :disabled="isDeleting"
                    >
                        <TrashIcon v-if="!isDeleting" class="w-4 h-4" />
                        {{ isDeleting ? 'Menghapus...' : 'Ya, Hapus' }}
                    </button>
                </div>
            </div>
        </ModalComponent>
    </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout      from '@/Layouts/DashboardLayout.vue'
import CardComponent        from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import TableComponent       from '@/Components/Admin/TableComponent.vue'
import ModalComponent       from '@/Components/Admin/ModalComponent.vue'
import StatCard             from '@/Components/Admin/StatCard.vue'
import { PlusIcon, PencilIcon, TrashIcon, ArrowPathIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import { TicketIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    vouchers: { type: [Array, Object], default: () => [] },
    filters:  { type: Object,          default: () => ({}) },
    meta:     { type: Object,          default: () => ({}) },
})

const showDeleteModal = ref(false)
const selectedVoucher = ref(null)
const isDeleting      = ref(false)
const activeFilter    = ref(props.filters.is_active ?? '')

const breadcrumbs = [
    { label: 'Dashboard', href: '/admin/dashboard' },
    { label: 'Vouchers',  active: true },
]

const columns = [
    { key: 'code',         label: 'Kode',         sortable: true  },
    { key: 'discount_pct', label: 'Diskon',        sortable: true  },
    { key: 'expires_at',   label: 'Kadaluarsa',    sortable: true  },
    { key: 'is_active',    label: 'Status',        sortable: true  },
    { key: 'created_at',   label: 'Dibuat',        sortable: true  },
    { key: 'actions',      label: 'Aksi',          sortable: false },
]

const voucherList = computed(() => {
    const raw = props.vouchers
    if (!raw) return []
    return Array.isArray(raw) ? raw : Object.values(raw)
})

const activeCount   = computed(() => voucherList.value.filter(v => v.is_active).length)
const inactiveCount = computed(() => voucherList.value.filter(v => !v.is_active).length)

const isExpired  = (date) => date && new Date(date) < new Date()
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })

const applyFilters = () => {
    router.get('/admin/vouchers', {
        is_active: activeFilter.value !== '' ? activeFilter.value : undefined,
        search:    props.filters.search || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

const resetFilters = () => { activeFilter.value = ''; applyFilters() }
const refreshData  = () => router.reload({ preserveScroll: true })

const handleSearch = (val) => {
    router.get('/admin/vouchers', {
        search:    val || undefined,
        is_active: activeFilter.value !== '' ? activeFilter.value : undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

const confirmDelete = (voucher) => { selectedVoucher.value = voucher; showDeleteModal.value = true }

const deleteVoucher = async () => {
    if (!selectedVoucher.value) return
    isDeleting.value = true
    try {
        await router.delete(`/admin/vouchers/${selectedVoucher.value.id}`, {
            preserveScroll: true,
            onSuccess: () => { showDeleteModal.value = false; selectedVoucher.value = null },
            onError:   (e) => console.error(e),
        })
    } finally {
        isDeleting.value = false
    }
}
</script>
