<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Management Produk">
            <!-- Header dengan statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard 
                    title="Total Produk" 
                    :value="meta.total" 
                    icon="📦"
                    class="bg-blue-50"
                />
                <StatCard 
                    title="Published" 
                    :value="publishedCount" 
                    icon="✅"
                    class="bg-green-50"
                />
                <StatCard 
                    title="Draft" 
                    :value="draftCount" 
                    icon="📝"
                    class="bg-yellow-50"
                />
                <StatCard 
                    title="Archived" 
                    :value="archivedCount" 
                    icon="📁"
                    class="bg-gray-50"
                />
            </div>

            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <Link
                        href="/products/create"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2"
                    >
                        <PlusIcon class="w-5 h-5" />
                        Tambah Produk
                    </Link>
                    
                    <!-- Status Filter -->
                    <select 
                        v-model="statusFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-indigo-200"
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
                :columns="columns"
                :rows="products"
                :per-page="10"
                :search="filters.search"
                @search="handleSearch"
            >
                <!-- Custom slot untuk status dengan badge -->
                <template #status="{ value }">
                    <span :class="statusBadgeClass(value)">
                        {{ value }}
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
                            class="px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition flex items-center gap-1 text-sm"
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

            <!-- Info Pagination -->
            <div class="mt-4 text-sm text-gray-600">
                Menampilkan {{ products.length }} dari {{ meta.total }} produk
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
import { ref, computed } from 'vue'
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
    ExclamationTriangleIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
    products: Array,
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

// Hitung statistik status
const publishedCount = computed(() => {
    return props.products.filter(p => p.status.toLowerCase() === 'published').length
})

const draftCount = computed(() => {
    return props.products.filter(p => p.status.toLowerCase() === 'draft').length
})

const archivedCount = computed(() => {
    return props.products.filter(p => p.status.toLowerCase() === 'archived').length
})

const statusBadgeClass = (status) => {
    const statusLower = status.toLowerCase()
    const baseClass = 'px-3 py-1 text-xs font-medium rounded-full inline-flex items-center gap-1'
    
    const variants = {
        published: 'bg-green-100 text-green-800 border border-green-200',
        draft: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        archived: 'bg-gray-100 text-gray-800 border border-gray-200'
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
</script>