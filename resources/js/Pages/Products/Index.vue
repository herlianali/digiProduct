<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Management Produk">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard title="Total Produk" :value="meta.total || productList.length" :icon="CubeIcon" />
                <StatCard title="Published"    :value="publishedCount"  :icon="CheckCircleIcon" />
                <StatCard title="Draft"        :value="draftCount"      :icon="PencilSquareIcon" />
                <StatCard title="Archived"     :value="archivedCount"   :icon="ArchiveBoxIcon" />
            </div>

            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex flex-wrap items-center gap-3">
                    <Link
                        href="/admin/products/create"
                        class="px-4 py-2 bg-brand-500 text-black rounded-lg hover:bg-brand-600 transition flex items-center gap-2 text-sm font-medium"
                    >
                        <PlusIcon class="w-4 h-4" />
                        Tambah Produk
                    </Link>

                    <!-- Filter Status -->
                    <select
                        v-model="statusFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200 focus:border-brand-400"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>

                    <!-- Filter Style -->
                    <select
                        v-model="styleFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200 focus:border-brand-400"
                        @change="applyFilters"
                    >
                        <option value="">Semua Style</option>
                        <option v-for="s in styles" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>

                    <button
                        v-if="statusFilter || styleFilter"
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
                v-if="productList.length > 0"
                :columns="columns"
                :rows="productList"
                :per-page="10"
                :search="filters.search"
                @search="handleSearch"
            >
                <!-- Badge Status — gunakan nilai lowercase dari controller -->
                <template #status="{ value }">
                    <span :class="statusBadgeClass(value)">
                        {{ statusLabel(value) }}
                    </span>
                </template>

                <!-- Aksi -->
                <template #actions="{ row }">
                    <div class="flex gap-2">
                        <Link
                            :href="`/admin/products/${row.id}`"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-1 text-sm"
                            title="Lihat Detail"
                        >
                            <EyeIcon class="w-4 h-4" />
                        </Link>
                        <Link
                            :href="`/admin/products/${row.id}/edit`"
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
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-gray-500">Belum ada produk</p>
                <p class="text-sm text-gray-400 mt-1">Mulai dengan menambahkan produk pertama Anda</p>
                <Link
                    href="/admin/products/create"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-brand-500 text-black rounded-lg hover:bg-brand-600 transition font-medium text-sm"
                >
                    <PlusIcon class="w-4 h-4" />
                    Tambah Produk Pertama
                </Link>
            </div>

            <div v-if="productList.length > 0" class="mt-4 text-sm text-gray-500">
                Menampilkan {{ productList.length }} dari {{ meta.total || productList.length }} produk
            </div>
        </CardComponent>

        <!-- Modal Hapus -->
        <ModalComponent :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus Produk</h3>
                </div>
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus produk
                    <strong>"{{ selectedProduct?.title }}"</strong>?
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
                        @click="deleteProduct"
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
import {
    PlusIcon, EyeIcon, PencilIcon, TrashIcon,
    ArrowPathIcon, ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import {
    CubeIcon, CheckCircleIcon, PencilSquareIcon, ArchiveBoxIcon,
} from '@heroicons/vue/24/solid'

const props = defineProps({
    products: { type: [Array, Object], default: () => [] },
    filters:  { type: Object, default: () => ({}) },
    styles:   { type: Array,  default: () => [] },
    meta:     { type: Object, default: () => ({}) },
})

// ─── State ────────────────────────────────────────────────────────
const showDeleteModal = ref(false)
const selectedProduct = ref(null)
const isDeleting      = ref(false)
const statusFilter    = ref(props.filters.status   ?? '')
const styleFilter     = ref(props.filters.style_id ?? '')

const breadcrumbs = [
    { label: 'Dashboard', href: '/admin/dashboard' },
    { label: 'Produk', active: true },
]

// ─── Kolom tabel — tambah style & tags ────────────────────────────
const columns = [
    { key: 'title',      label: 'Nama Produk', sortable: true  },
    { key: 'category',   label: 'Kategori',    sortable: true  },
    { key: 'style',      label: 'Style',       sortable: true  },
    { key: 'tags',       label: 'Tags',        sortable: false },
    { key: 'price',      label: 'Harga',       sortable: true  },
    { key: 'status',     label: 'Status',      sortable: true  },
    { key: 'created_at', label: 'Dibuat',      sortable: true  },
    { key: 'actions',    label: 'Aksi',        sortable: false },
]

// ─── Data list ────────────────────────────────────────────────────
const productList = computed(() => {
    const raw = props.products
    if (!raw) return []
    const arr = Array.isArray(raw) ? raw : Object.values(raw)
    return arr.filter(p => p && typeof p === 'object' && p.id)
})

// ─── Statistik ────────────────────────────────────────────────────
const publishedCount = computed(() => productList.value.filter(p => p.status === 'published').length)
const draftCount     = computed(() => productList.value.filter(p => p.status === 'draft').length)
const archivedCount  = computed(() => productList.value.filter(p => p.status === 'archived').length)

// ─── Status badge ─────────────────────────────────────────────────
// Controller kirim lowercase: 'published' | 'draft' | 'archived'
const statusLabel = (status) => ({
    published: 'Published',
    draft:     'Draft',
    archived:  'Archived',
}[status] ?? status ?? '-')

const statusBadgeClass = (status) => {
    const base = 'px-2.5 py-1 text-xs font-semibold rounded-full inline-flex items-center'
    const map  = {
        published: 'bg-green-100 text-green-800 border border-green-200',
        draft:     'bg-yellow-100 text-yellow-800 border border-yellow-200',
        archived:  'bg-gray-100 text-gray-600 border border-gray-200',
    }
    return `${base} ${map[status] ?? 'bg-gray-100 text-gray-600'}`
}

// ─── Filter & search ──────────────────────────────────────────────
const applyFilters = () => {
    router.get('/admin/products', {
        status:   statusFilter.value  || undefined,
        style_id: styleFilter.value   || undefined,
        search:   props.filters.search || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

const resetFilters = () => {
    statusFilter.value = ''
    styleFilter.value  = ''
    applyFilters()
}

const handleSearch = (val) => {
    router.get('/admin/products', {
        search:   val || undefined,
        status:   statusFilter.value  || undefined,
        style_id: styleFilter.value   || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
}

const refreshData = () => router.reload({ preserveScroll: true })

// ─── Delete ───────────────────────────────────────────────────────
const confirmDelete = (product) => {
    selectedProduct.value = product
    showDeleteModal.value  = true
}

const deleteProduct = async () => {
    if (!selectedProduct.value) return
    isDeleting.value = true
    try {
        await router.delete(`/admin/products/${selectedProduct.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false
                selectedProduct.value = null
            },
            onError: (errors) => console.error('Delete error:', errors),
        })
    } finally {
        isDeleting.value = false
    }
}
</script>