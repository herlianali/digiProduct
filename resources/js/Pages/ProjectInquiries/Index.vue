<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs" />

        <CardComponent title="Management Project Inquiry">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard title="Total Inquiry" :value="inquiryList.length"  :icon="InboxIcon" />
                <StatCard title="Baru"          :value="newCount"            :icon="SparklesIcon" />
                <StatCard title="In Review"     :value="inReviewCount"       :icon="ClockIcon" />
                <StatCard title="Diterima"      :value="acceptedCount"       :icon="CheckCircleIcon" />
            </div>

            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Filter Status -->
                    <select
                        v-model="statusFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200 focus:border-brand-400"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status</option>
                        <option value="new">New</option>
                        <option value="in_review">In Review</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <button
                        v-if="statusFilter"
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
                v-if="inquiryList.length > 0"
                :columns="columns"
                :rows="inquiryList"
                :per-page="10"
                :search="filters.search"
                @search="handleSearch"
            >
                <template #status="{ value }">
                    <span :class="statusBadgeClass(value)" class="px-2.5 py-1 text-xs font-semibold rounded-full inline-flex items-center">
                        {{ statusLabel(value) }}
                    </span>
                </template>

                <template #scopes="{ value }">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="scope in (value ?? [])"
                            :key="scope"
                            class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium"
                        >
                            {{ scopeLabel(scope) }}
                        </span>
                    </div>
                </template>

                <template #timelines="{ value }">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="t in (value ?? [])"
                            :key="t"
                            class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full font-medium"
                        >
                            {{ timelineLabel(t) }}
                        </span>
                    </div>
                </template>

                <template #actions="{ row }">
                    <div class="flex gap-2">
                        <Link
                            :href="`/admin/inquiries/${row.id}`"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-1 text-sm"
                            title="Lihat Detail"
                        >
                            <EyeIcon class="w-4 h-4" />
                        </Link>
                        <Link
                            :href="`/admin/inquiries/${row.id}/edit`"
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
                <p class="text-gray-500">Belum ada inquiry masuk</p>
                <p class="text-sm text-gray-400 mt-1">Inquiry akan muncul saat klien mengisi form kontak</p>
            </div>

            <div v-if="inquiryList.length > 0" class="mt-4 text-sm text-gray-500">
                Menampilkan {{ inquiryList.length }} dari {{ meta.total || inquiryList.length }} inquiry
            </div>
        </CardComponent>

        <!-- Modal Hapus -->
        <ModalComponent :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus Inquiry</h3>
                </div>
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus inquiry dari
                    <strong>"{{ selectedInquiry?.full_name }}"</strong>?
                    <br>
                    <span class="text-red-500 font-medium">Tindakan ini tidak dapat dibatalkan.</span>
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Batal
                    </button>
                    <button @click="deleteInquiry" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition flex items-center gap-2" :disabled="isDeleting">
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
import { Link, router }  from '@inertiajs/vue3'
import DashboardLayout      from '@/Layouts/DashboardLayout.vue'
import CardComponent        from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import TableComponent       from '@/Components/Admin/TableComponent.vue'
import ModalComponent       from '@/Components/Admin/ModalComponent.vue'
import StatCard             from '@/Components/Admin/StatCard.vue'
import { EyeIcon, PencilIcon, TrashIcon, ArrowPathIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import { InboxIcon, SparklesIcon, ClockIcon, CheckCircleIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    inquiries: { type: [Array, Object], default: () => [] },
    filters:   { type: Object,          default: () => ({}) },
    meta:      { type: Object,          default: () => ({}) },
})

const showDeleteModal  = ref(false)
const selectedInquiry  = ref(null)
const isDeleting       = ref(false)
const statusFilter     = ref(props.filters.status ?? '')

const breadcrumbs = [
    { label: 'Dashboard',        href: '/admin/dashboard' },
    { label: 'Project Inquiry',  active: true },
]

const columns = [
    { key: 'full_name',    label: 'Nama Klien',   sortable: true  },
    { key: 'company_name', label: 'Perusahaan',   sortable: true  },
    { key: 'email',        label: 'Email',         sortable: true  },
    { key: 'budget',       label: 'Budget',        sortable: false },
    { key: 'scopes',       label: 'Scope',         sortable: false },
    { key: 'timelines',    label: 'Timeline',      sortable: false },
    { key: 'status',       label: 'Status',        sortable: true  },
    { key: 'created_at',   label: 'Masuk',         sortable: true  },
    { key: 'actions',      label: 'Aksi',          sortable: false },
]

const inquiryList = computed(() => {
    const raw = props.inquiries
    if (!raw) return []
    return Array.isArray(raw) ? raw : Object.values(raw)
})

const newCount      = computed(() => inquiryList.value.filter(i => i.status === 'new').length)
const inReviewCount = computed(() => inquiryList.value.filter(i => i.status === 'in_review').length)
const acceptedCount = computed(() => inquiryList.value.filter(i => i.status === 'accepted').length)

const statusLabel = (s) => ({ new: 'New', in_review: 'In Review', accepted: 'Accepted', rejected: 'Rejected' }[s] ?? s ?? '-')
const scopeLabel  = (s) => ({ design_service: 'Design', illustration_service: 'Illustration' }[s] ?? s)
const timelineLabel = (t) => ({ asap: 'ASAP', '1_2_weeks': '1-2 Minggu', long_term: 'Long Term', flexible: 'Flexible' }[t] ?? t)

const statusBadgeClass = (s) => ({
    new:       'bg-blue-100 text-blue-800 border border-blue-200',
    in_review: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    accepted:  'bg-green-100 text-green-800 border border-green-200',
    rejected:  'bg-red-100 text-red-800 border border-red-200',
}[s] ?? 'bg-gray-100 text-gray-600')

const applyFilters = () => {
    router.get('/admin/inquiries', {
        status: statusFilter.value || undefined,
        search: props.filters.search || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

const resetFilters = () => { statusFilter.value = ''; applyFilters() }
const refreshData  = () => router.reload({ preserveScroll: true })

const handleSearch = (val) => {
    router.get('/admin/inquiries', {
        search: val || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

const confirmDelete = (inquiry) => { selectedInquiry.value = inquiry; showDeleteModal.value = true }

const deleteInquiry = async () => {
    if (!selectedInquiry.value) return
    isDeleting.value = true
    try {
        await router.delete(`/admin/inquiries/${selectedInquiry.value.id}`, {
            preserveScroll: true,
            onSuccess: () => { showDeleteModal.value = false; selectedInquiry.value = null },
            onError:   (e) => console.error(e),
        })
    } finally {
        isDeleting.value = false
    }
}
</script>
