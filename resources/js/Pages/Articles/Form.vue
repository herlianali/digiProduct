<template>
    <DashboardLayout>
        <NotificationContainer ref="notification" />

        <BreadcrumbsComponent :items="breadcrumbs" />

        <div class="max-w-7xl mx-auto">
            <!-- Grid Container -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Form Input (2/3) -->
                <div class="lg:col-span-2">
                    <CardComponent :title="formTitle">
                        <form @submit.prevent="submit" class="space-y-8" novalidate>
                            <!-- Title & Slug -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <InputComponent
                                    v-model="form.title"
                                    label="Judul Artikel"
                                    placeholder="Masukkan judul artikel"
                                    :error="form.errors.title"
                                    :error-message="form.errors.title"
                                    @input="generateSlug"
                                />
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                        Slug URL
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 text-sm">/articles/</span>
                                        <input
                                            type="text"
                                            v-model="form.slug"
                                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                            placeholder="slug-artikel"
                                            :class="{ 'border-red-500': form.errors.slug }"
                                        />
                                        <button
                                            type="button"
                                            @click="generateSlug"
                                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300"
                                        >
                                            Generate
                                        </button>
                                    </div>
                                    <p v-if="form.errors.slug" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.slug }}
                                    </p>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <TextareaComponent
                                v-model="form.excerpt"
                                label="Kutipan (Excerpt)"
                                placeholder="Ringkasan singkat artikel..."
                                :rows="3"
                                hint="Ditampilkan di halaman daftar artikel"
                                :error="form.errors.excerpt"
                                :error-message="form.errors.excerpt"
                            />

                            <!-- Content Editor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                    Konten Artikel *
                                </label>
                                <div class="border border-gray-300 rounded-lg overflow-hidden dark:border-gray-600">
                                    <!-- Toolbar -->
                                    <div class="bg-gray-50 border-b px-4 py-2 flex flex-wrap gap-2 dark:bg-gray-700 dark:border-gray-600">
                                        <button
                                            type="button"
                                            @click="formatText('bold')"
                                            class="px-2 py-1 hover:bg-gray-200 rounded dark:hover:bg-gray-600"
                                            title="Bold"
                                        >
                                            <BoldIcon class="w-4 h-4 text-gray-700 dark:text-gray-300" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="formatText('italic')"
                                            class="px-2 py-1 hover:bg-gray-200 rounded dark:hover:bg-gray-600"
                                            title="Italic"
                                        >
                                            <ItalicIcon class="w-4 h-4 text-gray-700 dark:text-gray-300" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="formatText('h1')"
                                            class="px-2 py-1 hover:bg-gray-200 rounded text-sm font-bold dark:hover:bg-gray-600 dark:text-gray-300"
                                            title="Heading 1"
                                        >
                                            H1
                                        </button>
                                        <button
                                            type="button"
                                            @click="formatText('h2')"
                                            class="px-2 py-1 hover:bg-gray-200 rounded text-sm font-bold dark:hover:bg-gray-600 dark:text-gray-300"
                                            title="Heading 2"
                                        >
                                            H2
                                        </button>
                                        <button
                                            type="button"
                                            @click="formatText('ul')"
                                            class="px-2 py-1 hover:bg-gray-200 rounded dark:hover:bg-gray-600"
                                            title="Bullet List"
                                        >
                                            <ListBulletIcon class="w-4 h-4 text-gray-700 dark:text-gray-300" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="formatText('ol')"
                                            class="px-2 py-1 hover:bg-gray-200 rounded dark:hover:bg-gray-600"
                                            title="Numbered List"
                                        >
                                            <!-- Custom SVG untuk Numbered List -->
                                            <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 6h13M3 12h13M3 18h13"/>
                                                <path d="M15 6h4M15 12h4M15 18h4"/>
                                                <text x="11" y="8" font-size="8" fill="currentColor">1.</text>
                                                <text x="11" y="14" font-size="8" fill="currentColor">2.</text>
                                                <text x="11" y="20" font-size="8" fill="currentColor">3.</text>
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            @click="insertImage"
                                            class="px-2 py-1 hover:bg-gray-200 rounded dark:hover:bg-gray-600"
                                            title="Insert Image"
                                        >
                                            <PhotoIcon class="w-4 h-4 text-gray-700 dark:text-gray-300" />
                                        </button>
                                    </div>
                                    
                                    <!-- Editor -->
                                    <textarea
                                        ref="editorRef"
                                        v-model="form.content"
                                        rows="12"
                                        class="w-full border-none focus:ring-0 px-4 py-3 resize-y dark:bg-gray-800 dark:text-white"
                                        placeholder="Tulis konten artikel di sini..."
                                        :class="{ 'border-red-500': form.errors.content }"
                                    ></textarea>
                                </div>
                                <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.content }}
                                </p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <Link href="/articles" class="btn-secondary">Batal</Link>
                                <button type="submit" class="btn-primary" :disabled="form.processing">
                                    <span v-if="form.processing">Menyimpan...</span>
                                    <span v-else>{{ actionButtonText }}</span>
                                </button>
                            </div>
                        </form>
                    </CardComponent>
                </div>

                <!-- Right Column - Sidebar (1/3) -->
                <div class="space-y-6">
                    <!-- Thumbnail Upload -->
                    <CardComponent title="Thumbnail Artikel">
                        <!-- Upload Thumbnail -->
                        <UploadInputComponent
                            v-model="form.thumbnail_file"
                            label="Upload Thumbnail"
                            hint="Seret dan lepas gambar di sini atau klik untuk memilih"
                            accept="image/*"
                            accept-hint="JPG, PNG, GIF, WebP maksimal 2MB"
                            :multiple="false"
                            :max-files="1"
                            @files-selected="handleThumbnailSelected"
                            @upload-progress="handleThumbnailProgress"
                            ref="thumbnailUpload"
                        />

                        <!-- Preview -->
                        <div v-if="thumbnailPreview" class="mt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">Preview:</h4>
                            <img 
                                :src="thumbnailPreview" 
                                alt="Thumbnail Preview" 
                                class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                            />
                            <button
                                type="button"
                                @click="removeThumbnail"
                                class="mt-2 text-red-600 hover:text-red-800 text-sm flex items-center gap-1 dark:text-red-400 dark:hover:text-red-300"
                            >
                                <TrashIcon class="w-4 h-4" />
                                Hapus Gambar
                            </button>
                        </div>

                        <div v-if="existingThumbnail && !thumbnailPreview" class="mt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">Thumbnail Saat Ini:</h4>
                            <img 
                                :src="existingThumbnail" 
                                alt="Current Thumbnail" 
                                class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                            />
                            <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                Upload gambar baru untuk mengganti
                            </p>
                        </div>
                    </CardComponent>

                    <!-- Status & Schedule -->
                    <CardComponent title="Status & Jadwal">
                        <div class="space-y-4">
                            <SelectComponent
                                v-model="form.status"
                                label="Status Artikel"
                                placeholder="Pilih status"
                                :options="statusOptions"
                                :required="true"
                                :error="form.errors.status"
                                :error-message="form.errors.status"
                            />

                            <!-- Published Date -->
                            <div v-if="form.status === 'published'">
                                <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                    Tanggal Publikasi
                                </label>
                                <input
                                    type="datetime-local"
                                    v-model="form.published_at"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                    Kosongkan untuk mempublikasi sekarang
                                </p>
                            </div>
                        </div>
                    </CardComponent>

                    <!-- Author Info -->
                    <CardComponent title="Penulis">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center dark:bg-gray-600">
                                <UserIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-200">{{ authorName }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Author ID: {{ form.author_id }}</p>
                            </div>
                        </div>
                    </CardComponent>

                    <!-- Save as Draft Button -->
                    <div class="text-center">
                        <button
                            type="button"
                            @click="saveAsDraft"
                            class="text-sm text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300"
                        >
                            Simpan sebagai Draft
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Insert Modal -->
        <ModalComponent 
            v-if="showImageModal"
            @close="showImageModal = false"
            @confirm="insertImageFromModal"
            title="Sisipkan Gambar"
            confirmText="Sisipkan"
            cancelText="Batal"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                        URL Gambar
                    </label>
                    <input
                        type="text"
                        v-model="imageUrl"
                        placeholder="https://example.com/image.jpg"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                        Deskripsi Alt Text
                    </label>
                    <input
                        type="text"
                        v-model="imageAlt"
                        placeholder="Deskripsi gambar"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>
            </div>
        </ModalComponent>
    </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'
import InputComponent from '@/Components/Admin/InputComponent.vue'
import SelectComponent from '@/Components/Admin/SelectComponent.vue'
import TextareaComponent from '@/Components/Admin/TextareaComponent.vue'
import UploadInputComponent from '@/Components/Admin/UploadInputComponent.vue'
import ModalComponent from '@/Components/Admin/ModalComponent.vue'
import NotificationContainer from '@/Components/Admin/NotificationContainer.vue'

// Hanya import icon yang tersedia di @heroicons/vue/24/outline
import {
    BoldIcon,
    ItalicIcon,
    ListBulletIcon,
    PhotoIcon,
    TrashIcon,
    UserIcon
} from '@heroicons/vue/24/outline'

const notification = ref()
const editorRef = ref()
const thumbnailUpload = ref()

const props = defineProps({
    mode: String,
    article: Object,
    errors: Object,
    authorName: String
})

// Fungsi slugify manual
const slugifyText = (text) => {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '')
}

// Initialize form
const form = useForm({
    title: props.article?.title || '',
    slug: props.article?.slug || '',
    excerpt: props.article?.excerpt || '',
    content: props.article?.content || '',
    thumbnail: props.article?.thumbnail || '',
    thumbnail_file: null,
    status: props.article?.status || 'draft',
    published_at: props.article?.published_at 
        ? new Date(props.article.published_at).toISOString().slice(0, 16)
        : '',
    author_id: props.article?.author_id || (typeof authUser !== 'undefined' ? authUser?.id : null),
})

const thumbnailPreview = ref(null)
const existingThumbnail = ref(props.article?.thumbnail ? `/storage/${props.article.thumbnail}` : '')
const showImageModal = ref(false)
const imageUrl = ref('')
const imageAlt = ref('')

// Computed properties
const isEditMode = computed(() => props.mode === 'edit')
const formTitle = computed(() => isEditMode.value ? 'Edit Artikel' : 'Buat Artikel Baru')
const actionButtonText = computed(() => isEditMode.value ? 'Update Artikel' : 'Simpan Artikel')

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Articles', href: '/articles' },
    { label: formTitle.value, active: true }
])

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'published', label: 'Published' },
    { value: 'archived', label: 'Archived' },
]

// Methods
const generateSlug = () => {
    if (!isEditMode.value || !form.slug) {
        form.slug = slugifyText(form.title)
    }
}

const formatText = (format) => {
    const textarea = editorRef.value
    if (!textarea) return
    
    const start = textarea.selectionStart
    const end = textarea.selectionEnd
    const selectedText = form.content.substring(start, end)
    
    let formattedText = ''
    
    switch(format) {
        case 'bold':
            formattedText = `**${selectedText}**`
            break
        case 'italic':
            formattedText = `*${selectedText}*`
            break
        case 'h1':
            formattedText = `# ${selectedText}`
            break
        case 'h2':
            formattedText = `## ${selectedText}`
            break
        case 'ul':
            formattedText = `\n* ${selectedText}\n`
            break
        case 'ol':
            formattedText = `\n1. ${selectedText}\n`
            break
    }
    
    form.content = form.content.substring(0, start) + 
                   formattedText + 
                   form.content.substring(end)
    
    nextTick(() => {
        textarea.focus()
        textarea.setSelectionRange(
            start + formattedText.length,
            start + formattedText.length
        )
    })
}

const insertImage = () => {
    showImageModal.value = true
}

const insertImageFromModal = () => {
    if (imageUrl.value) {
        const imgTag = `![${imageAlt.value || 'image'}](${imageUrl.value})`
        const textarea = editorRef.value
        const start = textarea.selectionStart
        
        form.content = form.content.substring(0, start) + 
                       imgTag + 
                       form.content.substring(start)
        
        showImageModal.value = false
        imageUrl.value = ''
        imageAlt.value = ''
        
        nextTick(() => {
            textarea.focus()
            textarea.setSelectionRange(start + imgTag.length, start + imgTag.length)
        })
    }
}

const handleThumbnailSelected = async (selectedFiles) => {
    if (selectedFiles.length > 0) {
        const file = selectedFiles[0]
        await createThumbnailPreview(file)
        form.thumbnail_file = file
    }
}

const handleThumbnailProgress = ({ fileName, progress }) => {
    console.log(`Upload progress for ${fileName}: ${progress}%`)
}

const createThumbnailPreview = (file) => {
    return new Promise((resolve) => {
        const reader = new FileReader()
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result
            existingThumbnail.value = '' // Clear existing thumbnail preview
            resolve()
        }
        reader.readAsDataURL(file)
    })
}

const removeThumbnail = () => {
    thumbnailPreview.value = null
    form.thumbnail_file = null
    form.thumbnail = '' // Clear thumbnail path
    
    if (thumbnailUpload.value) {
        thumbnailUpload.value.clearFiles()
    }
}

const saveAsDraft = () => {
    form.status = 'draft'
    submit()
}

const submit = async () => {
    // Generate slug if empty
    if (!form.slug.trim()) {
        form.slug = slugifyText(form.title)
    }
    
    // Set published_at if publishing without specific date
    if (form.status === 'published' && !form.published_at) {
        form.published_at = new Date().toISOString()
    }
    
    // Prepare FormData for file upload
    const formData = new FormData()
    
    // Add form data
    formData.append('title', form.title)
    formData.append('slug', form.slug)
    formData.append('excerpt', form.excerpt)
    formData.append('content', form.content)
    formData.append('status', form.status)
    formData.append('published_at', form.published_at || '')
    formData.append('author_id', form.author_id)
    
    // Add thumbnail file if exists
    if (form.thumbnail_file instanceof File) {
        formData.append('thumbnail_file', form.thumbnail_file)
    } else if (form.thumbnail) {
        // If editing and no new file, keep existing thumbnail
        formData.append('thumbnail', form.thumbnail)
    }
    
    if (isEditMode.value) {
        // For edit - use PUT method with FormData
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(`/articles/${props.article.id}`, {
            data: formData,
            preserveScroll: true,
            onSuccess: () => {
                notification.value?.success('Artikel berhasil diperbarui!', 'Success')
            },
            onError: (errors) => {
                handleFormErrors(errors)
            }
        })
    } else {
        // For create - use POST
        form.post('/articles', {
            data: formData,
            preserveScroll: true,
            onSuccess: () => {
                notification.value?.success('Artikel berhasil dibuat!', 'Success')
            },
            onError: (errors) => {
                handleFormErrors(errors)
            }
        })
    }
}

const handleFormErrors = (errors) => {
    if (errors.error) {
        notification.value?.error(errors.error, 'Error', {
            duration: 5000
        })
    } else if (errors.message) {
        notification.value?.error(errors.message, 'Error', {
            duration: 5000
        })
    } else {
        const firstError = Object.values(errors)[0]
        notification.value?.error(firstError || 'Terjadi kesalahan saat menyimpan data', 'Error', {
            duration: 5000
        })
    }
}

// Initialize
onMounted(() => {
    if (!isEditMode.value) {
        generateSlug()
    }
})
</script>

<style scoped>
.btn-primary {
    @apply px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed dark:bg-blue-500 dark:hover:bg-blue-600;
}

.btn-secondary {
    @apply px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700;
}
</style>