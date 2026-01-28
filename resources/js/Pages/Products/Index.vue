<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Management Produk">
            <!-- Header dengan statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard 
                    title="Total Produk" 
                    :value="meta.total || productList.length" 
                    :icon="CubeIcon"
                />
                <StatCard 
                    title="Published" 
                    :value="publishedCount" 
                    :icon="CheckCircleIcon"
                />
                <StatCard 
                    title="Draft" 
                    :value="draftCount" 
                    :icon="PencilSquareIcon"
                />
                <StatCard 
                    title="Archived" 
                    :value="archivedCount" 
                    :icon="ArchiveBoxIcon"
                />
            </div>

            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <Link
                        href="/products/create"
                        class="px-4 py-2 bg-brand-500 text-black rounded-lg hover:bg-brand-600 transition flex items-center gap-2"
                    >
                        <PlusIcon class="w-5 h-5" />
                        Tambah Produk
                    </Link>
                    
                    <!-- Status Filter -->
                    <select 
                        v-model="statusFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200 focus:border-brand-400"
                        @change="filterByStatus"
                    >
                        <option value="">Semua Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                
                <!-- Refresh Button -->
                <button
                    @click="refreshData"
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2"
                >
                    <ArrowPathIcon class="w-4 h-4" />
                    Refresh
                </button>
            </div>

            <!-- Table Component -->
            <TableComponent
                v-if="productList.length > 0"
                :columns="columns"
                :rows="productList"
                :per-page="10"
                :search="filters.search"
                @search="handleSearch"
            >
                <!-- Custom slot untuk status dengan badge -->
                <template #status="{ value }">
                    <span :class="statusBadgeClass(value)">
                        {{ value || '-' }}
                    </span>
                </template>

                <!-- Custom slot untuk actions -->
                <template #actions="{ row }">
                    <div class="flex gap-2">
                        <!-- View Button -->
                        <Link
                            :href="`/products/${row.id}`"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-1 text-sm"
                            title="Lihat Detail"
                        >
                            <EyeIcon class="w-4 h-4" />
                        </Link>
                        
                        <!-- Edit Button -->
                        <Link
                            :href="`/products/${row.id}/edit`"
                            class="px-3 py-1 bg-brand-100 text-brand-700 rounded-md hover:bg-brand-200 transition flex items-center gap-1 text-sm"
                            title="Edit"
                        >
                            <PencilIcon class="w-4 h-4" />
                        </Link>
                        
                        <!-- Delete Button -->
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
                    href="/products/create"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-brand-500 text-black rounded-lg hover:bg-brand-600 transition font-medium"
                >
                    <PlusIcon class="w-4 h-4" />
                    Tambah Produk Pertama
                </Link>
            </div>

            <!-- Info Pagination -->
            <div v-if="productList.length > 0" class="mt-4 text-sm text-gray-600">
                Menampilkan {{ productList.length }} dari {{ meta.total || productList.length }} produk
            </div>
        </CardComponent>

        <!-- Delete Confirmation Modal -->
        <ModalComponent 
            :show="showDeleteModal" 
            @close="showDeleteModal = false"
            max-width="md"
        >
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Konfirmasi Hapus Produk
                    </h3>
                </div>
                
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus produk <strong>"{{ selectedProduct?.title }}"</strong>?
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
                        <span v-if="isDeleting">Menghapus...</span>
                        <span v-else>Ya, Hapus</span>
                    </button>
                </div>
            </div>
        </ModalComponent>
    </DashboardLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import TableComponent from '@/Components/Admin/TableComponent.vue'
import ModalComponent from '@/Components/Admin/ModalComponent.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import { 
    PlusIcon, 
    EyeIcon, 
    PencilIcon, 
    TrashIcon,
    ArrowPathIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import {
  CubeIcon,
  CheckCircleIcon,
  PencilSquareIcon,
  ArchiveBoxIcon,
} from '@heroicons/vue/24/solid'


const props = defineProps({
    products: {
        type: [Array, Object],
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    meta: {
        type: Object,
        default: () => ({})
    }
})

const showDeleteModal = ref(false)
const selectedProduct = ref(null)
const isDeleting = ref(false)
const statusFilter = ref('')

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Produk', active: true },
]

const columns = [
    { key: 'title', label: 'Nama Produk', sortable: true },
    { key: 'category', label: 'Kategori', sortable: true },
    { key: 'price', label: 'Harga', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Dibuat', sortable: true },
    { key: 'actions', label: 'Aksi', sortable: false }
]

// Pastikan products selalu array
const productList = computed(() => {
    if (!props.products) return []
    
    if (Array.isArray(props.products)) {
        return props.products.filter(product => 
            product && typeof product === 'object' && product.id
        )
    } else if (props.products && typeof props.products === 'object') {
        // Jika object (misalnya dari Laravel paginate), convert ke array
        const values = Object.values(props.products)
        return values.filter(product => 
            product && typeof product === 'object' && product.id
        )
    }
    return []
})

// Hitung statistik status
const publishedCount = computed(() => {
    return productList.value.filter(p => 
        p && typeof p === 'object' && p.status && p.status.toLowerCase() === 'published'
    ).length
})

const draftCount = computed(() => {
    return productList.value.filter(p => 
        p && typeof p === 'object' && p.status && p.status.toLowerCase() === 'draft'
    ).length
})

const archivedCount = computed(() => {
    return productList.value.filter(p => 
        p && typeof p === 'object' && p.status && p.status.toLowerCase() === 'archived'
    ).length
})

const statusBadgeClass = (status) => {
    if (!status) return 'px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
    
    const statusLower = status.toLowerCase()
    const baseClass = 'px-3 py-1 text-xs font-medium rounded-full inline-flex items-center gap-1'
    
    const variants = {
        published: 'bg-brand-100 text-brand-800 border border-brand-200',
        draft: 'bg-brand-50 text-brand-700 border border-brand-200',
        archived: 'bg-gray-100 text-gray-700 border border-gray-200'
    }
    
    return `${baseClass} ${variants[statusLower] || 'bg-gray-100 text-gray-800'}`
}

const handleSearch = (searchValue) => {
    router.get('/products', { search: searchValue }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    })
}

const filterByStatus = () => {
    router.get('/products', { 
        status: statusFilter.value,
        search: props.filters.search 
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    })
}

const refreshData = () => {
    router.reload({ preserveScroll: true })
}

const confirmDelete = (product) => {
    selectedProduct.value = product
    showDeleteModal.value = true
}

const deleteProduct = async () => {
    if (!selectedProduct.value) return
    
    isDeleting.value = true
    
    try {
        await router.delete(`/products/${selectedProduct.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false
                selectedProduct.value = null
            },
            onError: (errors) => {
                console.error('Delete error:', errors)
            }
        })
    } catch (error) {
        console.error('Delete error:', error)
    } finally {
        isDeleting.value = false
    }
}

// Debug log
watch(() => props.products, (newProducts) => {
    console.log('Products data:', newProducts)
    console.log('Type:', typeof newProducts)
    console.log('Is array:', Array.isArray(newProducts))
}, { immediate: true })
</script>