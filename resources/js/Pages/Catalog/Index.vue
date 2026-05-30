<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs" />

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <StatCard title="Kategori Produk" :value="stats.categories" :icon="FolderIcon"  @click="activeTab = 'categories'" class="cursor-pointer" />
            <StatCard title="Tag Group"        :value="stats.tagGroups"  :icon="TagIcon"     @click="activeTab = 'taggroups'"  class="cursor-pointer" />
            <StatCard title="Total Tag"        :value="stats.tags"       :icon="HashtagIcon" @click="activeTab = 'taggroups'"  class="cursor-pointer" />
            <StatCard title="Style"            :value="stats.styles"     :icon="SwatchIcon"  @click="activeTab = 'styles'"     class="cursor-pointer" />
        </div>

        <CardComponent>
            <!-- Tab Nav -->
            <div class="flex gap-1 border-b border-gray-200 mb-6">
                <button
                    v-for="t in tabs" :key="t.key"
                    @click="activeTab = t.key"
                    class="px-5 py-2.5 text-sm font-semibold rounded-t-lg transition-colors border-b-2 -mb-px"
                    :class="activeTab === t.key
                        ? 'border-brand-500 text-brand-700 bg-brand-50'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                >
                    {{ t.label }}
                    <span
                        class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full"
                        :class="activeTab === t.key ? 'bg-brand-500 text-black' : 'bg-gray-200 text-gray-600'"
                    >{{ t.count }}</span>
                </button>
            </div>

            <!-- ══════════════════════════════════════════════ -->
            <!-- TAB 1: KATEGORI PRODUK                        -->
            <!-- ══════════════════════════════════════════════ -->
            <div v-show="activeTab === 'categories'">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Kategori Produk</h3>
                    <button @click="openModal('category')" class="btn-add">+ Tambah Kategori</button>
                </div>

                <table v-if="categories.length > 0" class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs text-gray-500 uppercase tracking-wide">
                            <th class="pb-3 pr-4 font-semibold">Nama</th>
                            <th class="pb-3 pr-4 font-semibold">Slug</th>
                            <th class="pb-3 pr-4 font-semibold">Produk</th>
                            <th class="pb-3 pr-4 font-semibold">Dibuat</th>
                            <th class="pb-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 pr-4 font-medium text-gray-800">{{ cat.name }}</td>
                            <td class="py-3 pr-4">
                                <code class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-mono">{{ cat.slug }}</code>
                            </td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="cat.products_count > 0 ? 'bg-brand-100 text-brand-800 border border-brand-200' : 'bg-gray-100 text-gray-500'">
                                    {{ cat.products_count }} produk
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-gray-500 text-xs">{{ formatDate(cat.created_at) }}</td>
                            <td class="py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button @click="openModal('category', cat)" class="btn-edit">
                                        <PencilIcon class="w-3.5 h-3.5" />
                                    </button>
                                    <button
                                        @click="confirmDelete('category', cat)"
                                        :disabled="cat.products_count > 0"
                                        :title="cat.products_count > 0 ? 'Kategori masih digunakan produk' : 'Hapus'"
                                        class="btn-delete"
                                        :class="cat.products_count > 0 ? 'opacity-40 cursor-not-allowed' : ''"
                                    >
                                        <TrashIcon class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-else class="empty-state">
                    <FolderIcon class="w-10 h-10 mx-auto mb-2 opacity-30" />
                    <p class="text-sm text-gray-400">Belum ada kategori produk</p>
                    <button @click="openModal('category')" class="mt-2 text-xs text-brand-600 hover:underline font-medium">+ Tambah sekarang</button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════ -->
            <!-- TAB 2: TAG GROUP + TAG                        -->
            <!-- ══════════════════════════════════════════════ -->
            <div v-show="activeTab === 'taggroups'">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Kiri: Tag Groups -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-gray-800">Tag Group</h3>
                            <button @click="openModal('taggroup')" class="btn-add">+ Tambah Group</button>
                        </div>

                        <table v-if="tagGroups.length > 0" class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 text-left text-xs text-gray-500 uppercase tracking-wide">
                                    <th class="pb-3 pr-4 font-semibold">Nama</th>
                                    <th class="pb-3 pr-4 font-semibold">Tag</th>
                                    <th class="pb-3 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="group in tagGroups" :key="group.id"
                                    class="transition-colors cursor-pointer"
                                    :class="activeTagGroup?.id === group.id ? 'bg-brand-50' : 'hover:bg-gray-50'"
                                    @click="activeTagGroup = group"
                                >
                                    <td class="py-3 pr-4">
                                        <p class="font-medium text-gray-800">{{ group.name }}</p>
                                        <code class="text-[10px] text-gray-400 font-mono">{{ group.slug }}</code>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="group.tags_count > 0 ? 'bg-brand-100 text-brand-800 border border-brand-200' : 'bg-gray-100 text-gray-500'">
                                            {{ group.tags_count }} tag
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex justify-end gap-1" @click.stop>
                                            <button @click="openModal('taggroup', group)" class="btn-edit">
                                                <PencilIcon class="w-3.5 h-3.5" />
                                            </button>
                                            <button
                                                @click="confirmDelete('taggroup', group)"
                                                :disabled="group.tags_count > 0"
                                                :title="group.tags_count > 0 ? 'Hapus semua tag di group ini dulu' : 'Hapus'"
                                                class="btn-delete"
                                                :class="group.tags_count > 0 ? 'opacity-40 cursor-not-allowed' : ''"
                                            >
                                                <TrashIcon class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-else class="empty-state">
                            <TagIcon class="w-10 h-10 mx-auto mb-2 opacity-30" />
                            <p class="text-sm text-gray-400">Belum ada tag group</p>
                            <button @click="openModal('taggroup')" class="mt-2 text-xs text-brand-600 hover:underline font-medium">+ Tambah sekarang</button>
                        </div>
                    </div>

                    <!-- Kanan: Tags dalam group aktif -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-gray-800">
                                <span v-if="activeTagGroup">Tag dalam "{{ activeTagGroup.name }}"</span>
                                <span v-else>Pilih Tag Group</span>
                            </h3>
                            <button
                                v-if="activeTagGroup"
                                @click="openModal('tag', null, activeTagGroup)"
                                class="btn-add"
                            >+ Tambah Tag</button>
                        </div>

                        <div v-if="!activeTagGroup" class="empty-state">
                            <HashtagIcon class="w-10 h-10 mx-auto mb-2 opacity-30" />
                            <p class="text-sm text-gray-400">Pilih tag group di sebelah kiri</p>
                        </div>

                        <template v-else>
                            <table v-if="activeTagGroup.tags?.length > 0" class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 text-left text-xs text-gray-500 uppercase tracking-wide">
                                        <th class="pb-3 pr-4 font-semibold">Label</th>
                                        <th class="pb-3 pr-4 font-semibold">Slug</th>
                                        <th class="pb-3 font-semibold text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="tag in activeTagGroup.tags" :key="tag.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 pr-4 font-medium text-gray-800">{{ tag.label }}</td>
                                        <td class="py-3 pr-4">
                                            <code class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-mono">{{ tag.slug }}</code>
                                        </td>
                                        <td class="py-3 text-right">
                                            <div class="flex justify-end gap-1">
                                                <button @click="openModal('tag', tag, activeTagGroup)" class="btn-edit">
                                                    <PencilIcon class="w-3.5 h-3.5" />
                                                </button>
                                                <button @click="confirmDelete('tag', tag)" class="btn-delete">
                                                    <TrashIcon class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div v-else class="empty-state">
                                <HashtagIcon class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                <p class="text-sm text-gray-400">Belum ada tag di group ini</p>
                                <button @click="openModal('tag', null, activeTagGroup)" class="mt-2 text-xs text-brand-600 hover:underline font-medium">+ Tambah tag pertama</button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════ -->
            <!-- TAB 3: STYLES                                 -->
            <!-- ══════════════════════════════════════════════ -->
            <div v-show="activeTab === 'styles'">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Style</h3>
                    <button @click="openModal('style')" class="btn-add">+ Tambah Style</button>
                </div>

                <table v-if="styles.length > 0" class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs text-gray-500 uppercase tracking-wide">
                            <th class="pb-3 pr-4 font-semibold">Nama Style</th>
                            <th class="pb-3 pr-4 font-semibold">Digunakan</th>
                            <th class="pb-3 pr-4 font-semibold">Dibuat</th>
                            <th class="pb-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="style in styles" :key="style.id" class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 pr-4 font-medium text-gray-800">{{ style.name }}</td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="style.products_count > 0 ? 'bg-brand-100 text-brand-800 border border-brand-200' : 'bg-gray-100 text-gray-500'">
                                    {{ style.products_count }} produk
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-gray-500 text-xs">{{ formatDate(style.created_at) }}</td>
                            <td class="py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button @click="openModal('style', style)" class="btn-edit">
                                        <PencilIcon class="w-3.5 h-3.5" />
                                    </button>
                                    <button
                                        @click="confirmDelete('style', style)"
                                        :disabled="style.products_count > 0"
                                        :title="style.products_count > 0 ? 'Style masih digunakan produk' : 'Hapus'"
                                        class="btn-delete"
                                        :class="style.products_count > 0 ? 'opacity-40 cursor-not-allowed' : ''"
                                    >
                                        <TrashIcon class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-else class="empty-state">
                    <SwatchIcon class="w-10 h-10 mx-auto mb-2 opacity-30" />
                    <p class="text-sm text-gray-400">Belum ada style</p>
                    <button @click="openModal('style')" class="mt-2 text-xs text-brand-600 hover:underline font-medium">+ Tambah sekarang</button>
                </div>
            </div>
        </CardComponent>

        <!-- ══════════════════════════════════════════════════ -->
        <!-- MODAL CREATE / EDIT                               -->
        <!-- ══════════════════════════════════════════════════ -->
        <ModalComponent :show="modal.open" @close="closeModal" max-width="sm">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">{{ modal.title }}</h3>

                <form @submit.prevent="submitModal" class="space-y-4" novalidate>

                    <!-- Name / Label -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ modal.type === 'tag' ? 'Label Tag' : 'Nama' }}
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="modal.form.name"
                            type="text"
                            :placeholder="modalPlaceholder"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-300 focus:border-brand-400 outline-none transition"
                            :class="modal.errors.name ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                            autofocus
                        />
                        <p v-if="modal.errors.name" class="text-xs text-red-500 mt-1">{{ modal.errors.name }}</p>
                    </div>

                    <!-- Slug preview -->
                    <p v-if="modal.type !== 'style' && modal.type !== 'tag' && modal.form.name" class="text-xs text-gray-400">
                        Slug: <code class="font-mono text-gray-600">{{ previewSlug }}</code>
                    </p>

                    <!-- Tag Group selector (tag only) -->
                    <div v-if="modal.type === 'tag'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tag Group <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="modal.form.category_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-300 outline-none"
                            :class="modal.errors.category_id ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                        >
                            <option value="">-- Pilih Group --</option>
                            <option v-for="g in tagGroups" :key="g.id" :value="g.id">{{ g.name }}</option>
                        </select>
                        <p v-if="modal.errors.category_id" class="text-xs text-red-500 mt-1">{{ modal.errors.category_id }}</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="modal.processing"
                            class="px-4 py-2 text-sm font-semibold bg-brand-500 text-black rounded-lg hover:bg-brand-600 transition flex items-center gap-2 disabled:opacity-60">
                            <svg v-if="modal.processing" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ modal.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </ModalComponent>

        <!-- ══════════════════════════════════════════════════ -->
        <!-- MODAL DELETE                                      -->
        <!-- ══════════════════════════════════════════════════ -->
        <ModalComponent :show="deleteModal.open" @close="deleteModal.open = false" max-width="sm">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-lg flex-shrink-0">
                        <ExclamationTriangleIcon class="w-5 h-5 text-red-600" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Hapus {{ deleteModal.typeLabel }}?</h3>
                </div>
                <p class="text-sm text-gray-500 mb-5">
                    Yakin ingin menghapus <strong>"{{ deleteModal.name }}"</strong>?
                    Tindakan ini tidak bisa dibatalkan.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="deleteModal.open = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="submitDelete" :disabled="deleteModal.processing"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg flex items-center gap-2 disabled:opacity-60">
                        <TrashIcon class="w-4 h-4" />
                        {{ deleteModal.processing ? 'Menghapus...' : 'Hapus' }}
                    </button>
                </div>
            </div>
        </ModalComponent>

    </DashboardLayout>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import ModalComponent from '@/Components/Admin/ModalComponent.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import {
    FolderIcon, TagIcon, HashtagIcon,
    TrashIcon, PencilIcon, ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import { SwatchIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    tab:        { type: String, default: 'categories' },
    categories: { type: Array,  default: () => [] },
    tagGroups:  { type: Array,  default: () => [] },
    styles:     { type: Array,  default: () => [] },
    stats:      { type: Object, default: () => ({ categories: 0, tagGroups: 0, tags: 0, styles: 0 }) },
})

// ── Tabs ──────────────────────────────────────────────────────────
const activeTab      = ref(props.tab)
const activeTagGroup = ref(props.tagGroups[0] ?? null)

watch(() => props.tagGroups, (groups) => {
    activeTagGroup.value = activeTagGroup.value
        ? (groups.find(g => g.id === activeTagGroup.value.id) ?? groups[0] ?? null)
        : (groups[0] ?? null)
})

const tabs = computed(() => [
    { key: 'categories', label: 'Kategori Produk', count: props.stats.categories },
    { key: 'taggroups',  label: 'Tag & Tag Group', count: props.stats.tags       },
    { key: 'styles',     label: 'Style',           count: props.stats.styles     },
])

const breadcrumbs = [
    { label: 'Dashboard',        href: 'admin/dashboard' },
    { label: 'Catalog Management', active: true },
]

// ── Helpers ───────────────────────────────────────────────────────
const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
    : '-'

// ── Modal CRUD ──────────────────────────────categories──────────────────────
const modal = reactive({
    open: false, type: '', mode: 'create', title: '', row: null,
    processing: false,
    form:   { name: '', category_id: '' },
    errors: {},
})

const modalTitles = {
    category: { create: 'Tambah Kategori Produk', edit: 'Edit Kategori Produk' },
    taggroup: { create: 'Tambah Tag Group',        edit: 'Edit Tag Group'       },
    tag:      { create: 'Tambah Tag',              edit: 'Edit Tag'             },
    style:    { create: 'Tambah Style',            edit: 'Edit Style'           },
}

const modalPlaceholder = computed(() => ({
    category: 'Contoh: Artwork, Font...',
    taggroup: 'Contoh: Sport, Music...',
    tag:      'Contoh: BMX, Rock...',
    style:    'Contoh: Popart, Retro...',
}[modal.type] ?? ''))

const previewSlug = computed(() =>
    modal.form.name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-').replace(/-+/g, '-')
)

const openModal = (type, row = null, parentGroup = null) => {
    // Guard: pastikan row ada ID saat mode edit
    if (row && !row.id) {
        console.error('openModal edit mode tapi row tidak punya ID:', row)
        return
    }

    Object.assign(modal, {
        open: true, type, mode: row ? 'edit' : 'create',
        title: modalTitles[type][row ? 'edit' : 'create'],
        row, processing: false, errors: {},
        form: {
            name:        row ? (row.name ?? row.label) : '',
            category_id: row?.category_id ?? parentGroup?.id ?? '',
        },
    })
}

const closeModal = () => { modal.open = false }

const routeMap = {
    category: { 
        store: '/admin/catalog/categories',  
        update: (id) => `/admin/catalog/categories/${id}`,  
        destroy: (id) => `/admin/catalog/categories/${id}`  
    },
    taggroup: { 
        store: '/admin/catalog/tag-groups',  
        update: (id) => `/admin/catalog/tag-groups/${id}`,  
        destroy: (id) => `/admin/catalog/tag-groups/${id}`  
    },
    tag: { 
        store: '/admin/catalog/tags',        
        update: (id) => `/admin/catalog/tags/${id}`,        
        destroy: (id) => `/admin/catalog/tags/${id}`        
    },
    style: { 
        store: '/admin/catalog/styles',      
        update: (id) => `/admin/catalog/styles/${id}`,      
        destroy: (id) => `/admin/catalog/styles/${id}`      
    },
}

const submitModal = () => {
    modal.errors = {}
    if (!modal.form.name.trim()) { modal.errors.name = 'Nama wajib diisi.'; return }
    if (modal.type === 'tag' && !modal.form.category_id) { modal.errors.category_id = 'Pilih tag group.'; return }

    modal.processing = true

    const { store, update } = routeMap[modal.type]

    const payload = modal.type === 'tag'
        ? { label: modal.form.name.trim(), category_id: modal.form.category_id }
        : { name: modal.form.name.trim() }

    if (modal.mode === 'create') {
        // POST biasa untuk create
        router.post(store, payload, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError:   (errors) => { modal.errors = errors },
            onFinish:  () => { modal.processing = false },
        })
    } else {
        // Spoofing PUT — kirim sebagai POST dengan _method: 'PUT'
        router.post(update(modal.row.id), { ...payload, _method: 'PUT' }, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError:   (errors) => { modal.errors = errors },
            onFinish:  () => { modal.processing = false },
        })
    }
}

// ── Delete modal ──────────────────────────────────────────────────
const deleteModal = reactive({
    open: false, type: '', typeLabel: '', name: '', id: null, processing: false,
})

const typeLabels = { category: 'Kategori', taggroup: 'Tag Group', tag: 'Tag', style: 'Style' }

const confirmDelete = (type, row) => {
    Object.assign(deleteModal, {
        open: true, type, processing: false,
        typeLabel: typeLabels[type],
        name: row.name ?? row.label,
        id:   row.id,
    })
}

const submitDelete = () => {
    deleteModal.processing = true

    // Spoofing DELETE — kirim sebagai POST dengan _method: 'DELETE'
    router.post(routeMap[deleteModal.type].destroy(deleteModal.id), { _method: 'DELETE' }, {
        preserveScroll: true,
        onSuccess: () => { deleteModal.open = false },
        onError:   (e) => console.error(e),
        onFinish:  () => { deleteModal.processing = false },
    })
}
</script>

<style scoped>
.btn-add {
    @apply px-3 py-1.5 bg-brand-500 text-black text-xs font-semibold rounded-lg hover:bg-brand-600 transition;
}
.btn-edit {
    @apply p-1.5 bg-brand-100 text-brand-700 hover:bg-brand-200 rounded-md transition flex items-center;
}
.btn-delete {
    @apply p-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-md transition flex items-center;
}
.empty-state {
    @apply text-center py-12 text-gray-400;
}
</style>