<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent title="Management Articles">
            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                <div class="flex gap-3">
                    <select
                        v-model="statusFilter"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200"
                        @change="filterArticles"
                    >
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                    
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari artikel..."
                        class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-brand-200 w-full md:w-64"
                        @keyup.enter="searchArticles"
                    />
                </div>

                <div class="flex gap-3">
                    <button
                        @click="refreshData"
                        class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2"
                    >
                        <ArrowPathIcon class="w-4 h-4" />
                        Refresh
                    </button>
                    
                    <Link
                        href="/articles/create"
                        class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 flex items-center gap-2"
                    >
                        <PlusIcon class="w-4 h-4" />
                        Buat Artikel
                    </Link>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Total Artikel</p>
                            <p class="text-2xl font-semibold">{{ totalCount }}</p>
                        </div>
                        <DocumentTextIcon class="w-8 h-8 text-blue-500" />
                    </div>
                </div>
                
                <div class="bg-white border rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Published</p>
                            <p class="text-2xl font-semibold">{{ publishedCount }}</p>
                        </div>
                        <CheckCircleIcon class="w-8 h-8 text-green-500" />
                    </div>
                </div>
                
                <div class="bg-white border rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Draft</p>
                            <p class="text-2xl font-semibold">{{ draftCount }}</p>
                        </div>
                        <PencilSquareIcon class="w-8 h-8 text-yellow-500" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <TableComponent
                v-if="articleList.length"
                :columns="columns"
                :rows="articleList"
                :per-page="10"
            >
                <template #thumbnail="{ value }">
                    <img 
                        v-if="value" 
                        :src="value" 
                        alt="Thumbnail" 
                        class="w-16 h-10 object-cover rounded"
                    />
                    <span v-else class="text-gray-400 text-sm">No image</span>
                </template>

                <template #status="{ value }">
                    <span :class="statusBadge(value)">
                        {{ getStatusLabel(value) }}
                    </span>
                </template>

                <template #published_at="{ value }">
                    <span v-if="value">
                        {{ formatDate(value) }}
                    </span>
                    <span v-else class="text-gray-400">Belum dipublikasi</span>
                </template>

                <template #author="{ value }">
                    <div v-if="value" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                            <UserIcon class="w-4 h-4 text-gray-600" />
                        </div>
                        <span>{{ value.name }}</span>
                    </div>
                    <span v-else class="text-gray-400">-</span>
                </template>

                <template #actions="{ row }">
                    <div class="flex gap-2">
                        <Link
                            :href="`/articles/${row.slug}`"
                            target="_blank"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 flex items-center gap-1 text-sm"
                        >
                            <EyeIcon class="w-4 h-4" />
                            View
                        </Link>
                        
                        <Link
                            :href="`/articles/${row.id}/edit`"
                            class="px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 flex items-center gap-1 text-sm"
                        >
                            <PencilIcon class="w-4 h-4" />
                            Edit
                        </Link>
                        
                        <button
                            @click="deleteArticle(row.id)"
                            class="px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 flex items-center gap-1 text-sm"
                        >
                            <TrashIcon class="w-4 h-4" />
                            Delete
                        </button>
                    </div>
                </template>
            </TableComponent>

            <!-- Empty -->
            <div v-else class="text-center py-12 border-2 border-dashed rounded-lg">
                <DocumentTextIcon class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <p class="text-gray-500">Belum ada artikel</p>
                <Link
                    href="/articles/create"
                    class="mt-4 inline-block px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700"
                >
                    Buat Artikel Pertama
                </Link>
            </div>
            
            <!-- Pagination -->
            <div v-if="meta && meta.total > perPage" class="mt-6 flex justify-center">
                <div class="flex gap-2">
                    <button
                        v-for="link in meta.links"
                        :key="link.label"
                        @click="gotoPage(link.url)"
                        :disabled="!link.url"
                        :class="[
                            'px-3 py-1 rounded border',
                            link.active 
                                ? 'bg-brand-600 text-white border-brand-600' 
                                : link.url 
                                ? 'border-gray-300 hover:bg-gray-50' 
                                : 'border-gray-200 text-gray-400 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </CardComponent>
        
        <!-- Delete Confirmation Modal -->
        <ModalComponent 
            v-if="showDeleteModal" 
            @close="showDeleteModal = false"
            @confirm="confirmDelete"
            title="Konfirmasi Hapus"
        >
            <p>Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.</p>
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

import {
    EyeIcon,
    ArrowPathIcon,
    PlusIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    PencilSquareIcon,
    PencilIcon,
    TrashIcon,
    UserIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    articles: Array,
    meta: Object,
    filters: Object
})

const statusFilter = ref(props.filters?.status || '')
const searchQuery = ref(props.filters?.search || '')
const showDeleteModal = ref(false)
const articleToDelete = ref(null)
const perPage = ref(10)

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Articles', active: true }
]

const columns = [
    { key: 'thumbnail', label: 'Thumbnail', width: '100px' },
    { key: 'title', label: 'Judul' },
    { key: 'author', label: 'Penulis' },
    { key: 'status', label: 'Status', width: '120px' },
    { key: 'published_at', label: 'Tanggal Publikasi', width: '150px' },
    { key: 'actions', label: 'Aksi', width: '200px' }
]

const articleList = computed(() => props.articles || [])

const totalCount = computed(() => articleList.value.length)
const publishedCount = computed(() => 
    articleList.value.filter(a => a.status === 'published').length
)
const draftCount = computed(() => 
    articleList.value.filter(a => a.status === 'draft').length
)

const getStatusLabel = (status) => {
    const labels = {
        draft: 'Draft',
        published: 'Published',
        archived: 'Archived'
    }
    return labels[status] || status
}

const statusBadge = (status) => {
    const baseClasses = 'px-3 py-1 rounded-full text-xs font-medium'
    
    switch(status) {
        case 'published':
            return `${baseClasses} bg-green-100 text-green-700`
        case 'draft':
            return `${baseClasses} bg-yellow-100 text-yellow-700`
        case 'archived':
            return `${baseClasses} bg-gray-100 text-gray-700`
        default:
            return `${baseClasses} bg-gray-100 text-gray-700`
    }
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

const filterArticles = () => {
    router.get('/articles', {
        status: statusFilter.value,
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const searchArticles = () => {
    filterArticles()
}

const refreshData = () => {
    router.reload({ preserveScroll: true })
}

const deleteArticle = (id) => {
    articleToDelete.value = id
    showDeleteModal.value = true
}

const confirmDelete = () => {
    if (articleToDelete.value) {
        router.delete(`/articles/${articleToDelete.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false
                articleToDelete.value = null
            }
        })
    }
}

const gotoPage = (url) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true
        })
    }
}
</script>