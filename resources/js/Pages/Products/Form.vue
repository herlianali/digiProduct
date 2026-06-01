<template>
    <DashboardLayout>
        <NotificationContainer ref="notification" />
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- ── Kiri: Form ───────────────────────────────── -->
                <CardComponent :title="mode === 'create' ? 'Tambah Produk' : 'Edit Produk'">
                    <form @submit.prevent="submit" class="space-y-6" novalidate>

                        <!-- Upload Image -->
                        <UploadInputComponent
                            v-model="form.images"
                            label="Upload Gambar Produk"
                            hint="Seret dan lepas gambar di sini atau klik untuk memilih"
                            accept="image/*"
                            accept-hint="JPG, PNG, GIF, WebP maksimal 10MB"
                            :multiple="true"
                            :max-files="10"
                            @files-selected="handleFilesSelected"
                            @upload-progress="handleUploadProgress"
                            ref="uploadInput"
                        />

                        <!-- Nama Produk + Kategori -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <InputComponent
                                v-model="form.title"
                                label="Nama Produk"
                                placeholder="Masukkan nama produk"
                                :error="form.errors.title"
                                :error-message="form.errors.title"
                            />
                            <SelectComponent
                                v-model="form.category_id"
                                label="Kategori"
                                placeholder="Pilih kategori produk"
                                :options="categoryOptions"
                                :error="form.errors.category_id"
                                :error-message="form.errors.category_id"
                            />
                        </div>

                        <!-- Style -->
                        <SelectComponent
                            v-model="form.style_id"
                            label="Style"
                            placeholder="Pilih style produk"
                            :options="styleOptions"
                            :error="form.errors.style_id"
                            :error-message="form.errors.style_id"
                        />

                        <!-- Tags -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Tags
                            </label>

                            <div v-if="tagGroups.length > 0" class="space-y-3">
                                <div
                                    v-for="group in tagGroups"
                                    :key="group.id"
                                    class="border border-gray-200 dark:border-gray-700 rounded-xl p-4"
                                >
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                                        {{ group.name }}
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="tag in group.tags"
                                            :key="tag.id"
                                            type="button"
                                            @click="toggleTag(tag.id)"
                                            class="px-3 py-1 rounded-full text-xs font-bold transition-all"
                                            :class="form.tags.includes(tag.id)
                                                ? 'bg-black text-white'
                                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'"
                                        >
                                            {{ tag.label }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <p v-else class="text-sm text-gray-400 italic">
                                Belum ada tag. Tambahkan lewat halaman Catalog.
                            </p>

                            <!-- Preview tag terpilih -->
                            <div v-if="form.tags.length > 0" class="mt-3 flex flex-wrap gap-1 items-center">
                                <span class="text-xs text-gray-400 mr-1">Terpilih:</span>
                                <span
                                    v-for="tagId in form.tags"
                                    :key="tagId"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-black text-white"
                                >
                                    {{ getTagLabel(tagId) }}
                                    <button
                                        type="button"
                                        @click="toggleTag(tagId)"
                                        class="hover:opacity-60 transition-opacity leading-none"
                                    >
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </button>
                                </span>
                            </div>

                            <p v-if="form.errors.tags" class="mt-1 text-xs text-red-500">
                                {{ form.errors.tags }}
                            </p>
                        </div>

                        <!-- Harga + Toggle Gratis -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <InputComponent
                                v-model="form.price"
                                label="Harga"
                                type="number"
                                placeholder="0"
                                prefix="$"
                                :disabled="form.is_free"
                                :min="0"
                                :error="form.errors.price"
                                :error-message="form.errors.price"
                            />
                            <ToggleSwitchComponent
                                v-model="form.is_free"
                                label="Produk Gratis"
                                active-label="Gratis"
                                inactive-label="Berbayar"
                            />
                        </div>

                        <!-- Deskripsi -->
                        <TextareaComponent
                            v-model="form.description"
                            label="Deskripsi"
                            placeholder="Masukkan deskripsi produk"
                            :rows="4"
                            hint="Deskripsi akan ditampilkan pada halaman produk"
                            :error="form.errors.description"
                            :error-message="form.errors.description"
                        />

                        <!-- Status -->
                        <RadioGroupComponent
                            v-model="form.status"
                            label="Status"
                            :options="statusOptions"
                            :error="form.errors.status"
                            :error-message="form.errors.status"
                        />

                        <!-- Actions -->
                        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Link href="/products" class="btn-secondary">Batal</Link>
                            <button
                                type="submit"
                                class="btn-primary"
                                :disabled="form.processing || uploadInProgress"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else-if="uploadInProgress">Menunggu upload...</span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </form>
                </CardComponent>

                <!-- ── Kanan: Preview Gambar ─────────────────────── -->
                <div class="flex flex-col gap-6">
                    <CardComponent title="Preview Gambar">
                        <ImagePreviewComponent
                            :images="imagePreviews"
                            title="Gambar Produk"
                            @remove-image="removeImage"
                            @set-primary="setPrimaryImage"
                            @clear-all="clearAllImages"
                        />

                        <!-- Upload Progress -->
                        <div
                            v-if="uploadInProgress"
                            class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-blue-900 dark:text-blue-200">Mengunggah gambar...</span>
                                <span class="text-sm text-blue-700 dark:text-blue-300">{{ uploadProgress }}%</span>
                            </div>
                            <div class="w-full bg-blue-200 dark:bg-blue-700 rounded-full h-2">
                                <div
                                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    :style="{ width: uploadProgress + '%' }"
                                />
                            </div>
                            <div class="mt-2 flex justify-between text-xs text-blue-700 dark:text-blue-300">
                                <span>{{ imagesUploaded }} terunggah</span>
                                <span>{{ imagesPending }} tersisa</span>
                            </div>
                        </div>
                    </CardComponent>

                    <!-- Tips -->
                    <CardComponent title="Tips">
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                            <li v-for="tip in tips" :key="tip" class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ tip }}
                            </li>
                        </ul>
                    </CardComponent>
                </div>

            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import DashboardLayout       from '@/Layouts/DashboardLayout.vue'
import CardComponent         from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent  from '@/Components/Admin/BreadcrumbsComponent.vue'
import InputComponent        from '@/Components/Admin/InputComponent.vue'
import SelectComponent       from '@/Components/Admin/SelectComponent.vue'
import TextareaComponent     from '@/Components/Admin/TextareaComponent.vue'
import ToggleSwitchComponent from '@/Components/Admin/ToggleSwitchComponent.vue'
import RadioGroupComponent   from '@/Components/Admin/RadioGroupComponent.vue'
import UploadInputComponent  from '@/Components/Admin/UploadInputComponent.vue'
import ImagePreviewComponent from '@/Components/Admin/ImagePreviewComponent.vue'
import NotificationContainer from '@/Components/Admin/NotificationContainer.vue'

// ─── Refs ─────────────────────────────────────────────────────────
const notification = ref()
const uploadInput  = ref()

// ─── Props dari controller ────────────────────────────────────────
const props = defineProps({
    mode:           { type: String,  default: 'create' },
    product:        { type: Object,  default: null },
    productId:      { type: Number,  default: null }, // Add this
    existingImages: { type: Array,   default: () => [] },
    categories:     { type: Array,   default: () => [] },
    styles:         { type: Array,   default: () => [] },
    tagGroups:      { type: Array,   default: () => [] },
})

// ─── Form state ───────────────────────────────────────────────────
const form = useForm({
    title:         props.product?.title        ?? '',
    category_id:   props.product?.category_id  ?? null,
    style_id:      props.product?.style_id     ?? null,
    tags:          props.product?.tag_ids      ?? [],    // array of tag IDs
    price:         props.product?.price        ?? 0,
    is_free:       props.product?.is_free      ?? false,
    description:   props.product?.description  ?? '',
    status:        props.product?.status       ?? 'draft',
    images:        [],
    delete_images: [],
})

// ─── Static options ───────────────────────────────────────────────
const statusOptions = [
    { value: 'draft',     label: 'Draft' },
    { value: 'published', label: 'Published' },
    { value: 'archived',  label: 'Archived' },
]

const tips = [
    'Gambar pertama akan menjadi gambar utama produk',
    'Format gambar yang disarankan: JPG atau PNG',
    'Rasio aspek optimal: 1:1 (persegi)',
    'Maksimal 10 gambar per produk, ukuran max 10MB per file',
]

// ─── Computed options ─────────────────────────────────────────────
const categoryOptions = computed(() =>
    props.categories.map(c => ({ value: c.id, label: c.name }))
)

const styleOptions = computed(() =>
    props.styles.map(s => ({ value: s.id, label: s.name }))
)

// ─── Tag helpers ──────────────────────────────────────────────────
const allTags = computed(() => props.tagGroups.flatMap(g => g.tags ?? []))

const getTagLabel = (id) => allTags.value.find(t => t.id === id)?.label ?? String(id)

const toggleTag = (id) => {
    const idx = form.tags.indexOf(id)
    idx === -1 ? form.tags.push(id) : form.tags.splice(idx, 1)
}

// ─── Image preview state ──────────────────────────────────────────
const imagePreviews = ref([])
const uploadActive  = ref(false)

// ─── Upload computed ──────────────────────────────────────────────
const uploadInProgress = computed(() =>
    uploadActive.value || imagePreviews.value.some(img => img.progress < 100 && img.progress > 0)
)

const uploadProgress = computed(() => {
    const uploading = imagePreviews.value.filter(img => img.file && img.progress < 100)
    if (!uploading.length) return 0
    return Math.round(uploading.reduce((s, img) => s + img.progress, 0) / uploading.length)
})

const imagesUploaded = computed(() =>
    imagePreviews.value.filter(img => img.progress === 100).length
)
const imagesPending = computed(() =>
    imagePreviews.value.filter(img => img.progress < 100 && img.file).length
)

// ─── Upload handlers ──────────────────────────────────────────────
const handleFilesSelected = async (selectedFiles) => {
    uploadActive.value = true

    for (const file of selectedFiles) {
        await addFileToPreview(file)
    }

    syncFormImages()
    startUploadSimulation()
}

const addFileToPreview = (file) =>
    new Promise(resolve => {
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreviews.value.push({
                id:        Date.now() + Math.random(),
                file,
                name:      file.name,
                size:      file.size,
                type:      file.type,
                preview:   e.target.result,
                isPrimary: !imagePreviews.value.length && !props.existingImages?.length,
                progress:  0,
            })
            resolve()
        }
        reader.readAsDataURL(file)
    })

const startUploadSimulation = () => {
    const pending = imagePreviews.value.filter(img => img.file && img.progress < 100)
    if (!pending.length) { uploadActive.value = false; return }
    pending.forEach(simulateUpload)
}

const simulateUpload = (image) => {
    let progress = 0
    const iv = setInterval(() => {
        progress += 20
        image.progress = Math.min(progress, 100)
        if (progress >= 100) {
            clearInterval(iv)
            syncFormImages()
            const allDone = imagePreviews.value.filter(i => i.file).every(i => i.progress === 100)
            if (allDone) uploadActive.value = false
        }
    }, 200)
}

const handleUploadProgress = ({ fileName, progress }) => {
    const img = imagePreviews.value.find(i => i.name === fileName)
    if (img) { img.progress = progress; syncFormImages() }
}

// Satu-satunya fungsi yang sync ke form.images — tidak ada duplikasi
const syncFormImages = () => {
    form.images = imagePreviews.value
        .filter(img => img.file instanceof File && img.progress === 100)
        .map(img => img.file)
        // Deduplicate berdasarkan name + size + lastModified
        .filter((file, i, self) =>
            i === self.findIndex(f =>
                f.name === file.name && f.size === file.size && f.lastModified === file.lastModified
            )
        )
}

// ─── Remove / set primary / clear ────────────────────────────────
const removeImage = (index) => {
    const img = imagePreviews.value[index]
    if (img.id && !img.file && !form.delete_images.includes(img.id)) {
        form.delete_images.push(img.id)
    }
    imagePreviews.value.splice(index, 1)
    if (img.isPrimary && imagePreviews.value.length) {
        imagePreviews.value[0].isPrimary = true
    }
    syncFormImages()
}

const setPrimaryImage = async (index) => {
    const [selectedImage] = imagePreviews.value.splice(index, 1)
    imagePreviews.value.unshift(selectedImage)

    imagePreviews.value.forEach((img, idx) => {
        img.isPrimary = idx === 0
    })

    if (props.mode === 'edit' && props.productId) {
        const order = imagePreviews.value.map(img => img.id).filter(id => id)

        try {
            await router.post(`/admin/products/${props.productId}/reorder-images`, {
                image_order: order
            })
            notification.value?.success('Urutan gambar berhasil diupdate', 'Sukses')
        } catch (error) {
            notification.value?.error('Gagal mengupdate urutan gambar', 'Error')
        }
    }
}

const clearAllImages = () => {
    imagePreviews.value.forEach(img => {
        if (img.id && !img.file) form.delete_images.push(img.id)
    })
    imagePreviews.value = []
    form.images        = []
    uploadActive.value = false
    uploadInput.value?.clearFiles?.()
}

// ─── Init existing images (edit mode) ────────────────────────────
onMounted(() => {
    if (props.existingImages?.length) {
        imagePreviews.value = props.existingImages.map((img, i) => ({
            id:        img.id,
            name:      img.filename ?? 'image',
            size:      img.size ?? 0,
            type:      'image/' + (img.extension ?? 'jpg'),
            url:       img.url ?? '/storage/' + img.path,
            isPrimary: i === 0,
            progress:  100,
            // file: undefined → bukan file baru, dari server
        }))

        // Tandai thumbnail sebagai isPrimary
        if (props.product?.thumbnail) {
            const thumbIdx = props.existingImages.findIndex(img =>
                img.path === props.product.thumbnail ||
                'storage/' + img.path === props.product.thumbnail
            )
            if (thumbIdx > -1) {
                imagePreviews.value.forEach(img => { img.isPrimary = false })
                imagePreviews.value[thumbIdx].isPrimary = true
            }
        }
    }

    // Default category jika kosong
    if (!form.category_id && props.categories.length) {
        form.category_id = props.categories[0].id
    }
})

// ─── Submit ───────────────────────────────────────────────────────
const submit = async () => {
    if (uploadInProgress.value) {
        notification.value?.warning('Harap tunggu sampai semua gambar selesai diunggah.', 'Upload Berjalan')
        return
    }

    syncFormImages()

    if (props.mode === 'create' && !form.images.length && !props.existingImages?.length) {
        notification.value?.error('Minimal satu gambar produk wajib diupload.', 'Validasi')
        return
    }

    // Build FormData
    const fd = new FormData()
    fd.append('title',       form.title)
    fd.append('category_id', form.category_id ?? '')
    fd.append('style_id',    form.style_id ?? '')
    fd.append('price',       form.price)
    fd.append('is_free',     form.is_free ? '1' : '0')
    fd.append('description', form.description)
    fd.append('status',      form.status)

    form.tags.forEach((id, i)     => fd.append(`tags[${i}]`, id))
    form.images.forEach((file, i) => {
        if (file instanceof File) fd.append(`images[${i}]`, file)
    })

    if (form.delete_images.length) {
        fd.append('delete_images', JSON.stringify(form.delete_images))
    }

    const onSuccess = () => {
        notification.value?.success(
            props.mode === 'create' ? 'Produk berhasil ditambahkan!' : 'Produk berhasil diperbarui!',
            'Sukses'
        )
    }

    const onError = (errors) => {
        console.error('Error:', errors)
        const msg = errors.error ?? errors.message ?? Object.values(errors)[0] ?? 'Terjadi kesalahan.'
        notification.value?.error(msg, 'Error', { duration: 5000 })
    }

    if (props.mode === 'create') {
        router.post('/admin/products', fd, {
            preserveScroll: true,
            onSuccess,
            onError,
        })
    } else {
        // Use productId if available, otherwise product.id
        const id = props.productId || props.product?.id
        router.post(`/admin/products/${id}`, fd, {
            preserveScroll: true,
            onSuccess,
            onError,
        })
    }
}

// ─── Breadcrumbs ──────────────────────────────────────────────────
const breadcrumbs = [
    { label: 'Dashboard', href: '/admin/dashboard' },
    { label: 'Produk',    href: '/admin/products' },
    { label: props.mode === 'create' ? 'Tambah' : 'Edit', active: true },
]
</script>

<style scoped>
.btn-primary {
    @apply px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700
           transition-colors disabled:opacity-50 disabled:cursor-not-allowed
           dark:bg-blue-500 dark:hover:bg-blue-600;
}
.btn-secondary {
    @apply px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium
           hover:bg-gray-50 transition-colors
           dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700;
}
</style>
