<template>
    <DashboardLayout>
        <NotificationContainer ref="notification" />

        <BreadcrumbsComponent :items="breadcrumbs"/>

        <div class="max-w-7xl mx-auto">
            <!-- Grid Container -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column - Form Input -->
                <CardComponent :title="mode === 'create' ? 'Tambah Produk' : 'Edit Produk'">
                    <form @submit.prevent="submit" class="space-y-8" novalidate>
                        <!-- Upload Image Section -->
                        <div>
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
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <InputComponent
                                v-model="form.title"
                                label="Nama Produk"
                                placeholder="Masukkan nama produk"
                                :error="form.errors.title"
                                :error-message="form.errors.title"
                            />
                            
                            <!-- Hanya Category saja (sebelumnya Type sudah dihapus) -->
                            <SelectComponent
                                v-model="form.category_id"
                                label="Kategori"
                                placeholder="Pilih kategori produk"
                                :options="categoryOptions"
                                :required="true"
                                :error="form.errors.category_id"
                                :error-message="form.errors.category_id"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <InputComponent
                                v-model="form.price"
                                label="Harga"
                                type="number"
                                placeholder="0"
                                prefix="Rp"
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

                        <TextareaComponent
                            v-model="form.description"
                            label="Deskripsi"
                            placeholder="Masukkan deskripsi produk"
                            :rows="4"
                            hint="Deskripsi akan ditampilkan pada halaman produk"
                            :error="form.errors.description"
                            :error-message="form.errors.description"
                        />

                        <RadioGroupComponent
                            v-model="form.status"
                            label="Status"
                            :options="statusOptions"
                            :error="form.errors.status"
                            :error-message="form.errors.status"
                        />

                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <Link href="/products" class="btn-secondary">Batal</Link>
                            <button type="submit" class="btn-primary" :disabled="form.processing || uploadInProgress">
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else-if="uploadInProgress">Menunggu upload...</span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </form>
                </CardComponent>

                <!-- Right Column - Image Preview -->
                <CardComponent title="Preview Gambar">
                    <ImagePreviewComponent
                        :images="imagePreviews"
                        title="Gambar Produk"
                        @remove-image="removeImage"
                        @set-primary="setPrimaryImage"
                        @clear-all="clearAllImages"
                    />

                    <!-- Upload Progress Summary -->
                    <div v-if="uploadInProgress" class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-200">
                                Mengunggah gambar...
                            </span>
                            <span class="text-sm text-blue-700 dark:text-blue-300">
                                {{ uploadProgress }}%
                            </span>
                        </div>
                        
                        <div class="w-full bg-blue-200 dark:bg-blue-700 rounded-full h-2">
                            <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full transition-all duration-300" 
                                 :style="{ width: uploadProgress + '%' }"></div>
                        </div>
                        
                        <div class="mt-2 flex justify-between text-xs text-blue-700 dark:text-blue-300">
                            <span>{{ imagesUploaded }} terunggah</span>
                            <span>{{ imagesPending }} tersisa</span>
                        </div>
                    </div>

                    <!-- Tips Section -->
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-200 mb-2">Tips:</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Gambar pertama akan menjadi gambar utama produk</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Format gambar yang disarankan: JPG atau PNG</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Rasio aspek optimal: 1:1 (persegi)</span>
                            </li>
                        </ul>
                    </div>
                </CardComponent>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { useForm, Link } from '@inertiajs/vue3'
    import DashboardLayout from '@/Layouts/DashboardLayout.vue'
    import CardComponent from '@/Components/Admin/CardComponent.vue'
    import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'

    import InputComponent from '@/Components/Admin/InputComponent.vue'
    import SelectComponent from '@/Components/Admin/SelectComponent.vue'
    import TextareaComponent from '@/Components/Admin/TextareaComponent.vue'
    import ToggleSwitchComponent from '@/Components/Admin/ToggleSwitchComponent.vue'
    import RadioGroupComponent from '@/Components/Admin/RadioGroupComponent.vue'
    import UploadInputComponent from '@/Components/Admin/UploadInputComponent.vue'
    import ImagePreviewComponent from '@/Components/Admin/ImagePreviewComponent.vue'

    import NotificationContainer from '@/Components/Admin/NotificationContainer.vue'

    const notification = ref()
    const uploadInput = ref()

    const props = defineProps({
        mode: String,
        product: Object,
        existingImages: {
            type: Array,
            default: () => []
        },
        categories: {
            type: Array,
            default: () => []
        }
    })

    // Initialize form with default values
    // HAPUS: form.type karena kolom type sudah dihapus dari database
    const form = useForm({
        title: props.product?.title ?? '',
        category_id: props.product?.category_id ?? (props.categories.length > 0 ? props.categories[0].id : null),
        price: props.product?.price ?? 0,
        is_free: props.product?.is_free ?? false,
        description: props.product?.description ?? '',
        status: props.product?.status ?? 'draft',
        images: [],
        delete_images: []
    })

    const imagePreviews = ref([])
    const uploadActive = ref(false)

    // HAPUS: typeOptions karena sudah tidak digunakan
    const statusOptions = [
        { value: 'draft', label: 'Draft' },
        { value: 'published', label: 'Published' },
        { value: 'archived', label: 'Archived' },
    ]

    const categoryOptions = computed(() => {
        return props.categories.map(cat => ({
            value: cat.id,
            label: cat.name
        }))
    })

    // Computed properties for upload progress
    const uploadInProgress = computed(() => {
        return uploadActive.value || imagePreviews.value.some(img => img.progress < 100 && img.progress > 0)
    })

    const uploadProgress = computed(() => {
        const uploadingImages = imagePreviews.value.filter(img => img.file && img.progress < 100)
        if (uploadingImages.length === 0) return 0
        
        const total = uploadingImages.reduce((sum, img) => sum + img.progress, 0)
        return Math.round(total / uploadingImages.length)
    })

    const imagesUploaded = computed(() => {
        return imagePreviews.value.filter(img => img.progress === 100).length
    })

    const imagesPending = computed(() => {
        return imagePreviews.value.filter(img => img.progress < 100 && img.file).length
    })

    // Handle file selection
    const handleFilesSelected = async (selectedFiles) => {
        console.log('Files selected from UploadInput:', selectedFiles)
        
        uploadActive.value = true
        
        // Reset jika perlu
        if (imagePreviews.value.length === 0) {
            form.delete_images = []
        }
        
        // Tambahkan file ke preview
        for (const file of selectedFiles) {
            await addFileToPreview(file)
        }
        
        // Update form.images dengan file asli
        updateFormImages()
        
        // Start upload simulation
        startUploadSimulation()
    }


    // Add file to preview with FileReader
    const addFileToPreview = (file) => {
        return new Promise((resolve) => {
            const reader = new FileReader()
            reader.onload = (e) => {
                const preview = {
                    id: Date.now() + Math.random(),
                    file: file, // Simpan File object asli
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    preview: e.target.result,
                    isPrimary: imagePreviews.value.length === 0 && !props.existingImages?.length,
                    progress: 0
                }
                imagePreviews.value.push(preview)
                console.log('Added file to preview:', file.name)
                resolve()
            }
            reader.readAsDataURL(file)
        })
    }

    // Simulate upload progress
    const startUploadSimulation = () => {
        const uploadingImages = imagePreviews.value.filter(img => img.file && img.progress < 100)
        
        if (uploadingImages.length === 0) {
            uploadActive.value = false
            return
        }

        // Simulate upload for each image
        uploadingImages.forEach(image => {
            simulateImageUpload(image)
        })
    }

    // Simulate upload for a single image
    const simulateImageUpload = (image) => {
        let progress = 0
        const interval = setInterval(() => {
            progress += 20 // Increase progress faster
            
            if (progress >= 100) {
                progress = 100
                clearInterval(interval)
                
                // Check if all uploads are complete
                setTimeout(() => {
                    const allComplete = imagePreviews.value
                        .filter(img => img.file)
                        .every(img => img.progress === 100)
                    
                    if (allComplete) {
                        uploadActive.value = false
                    }
                }, 500)
            }
            
            // Update progress
            image.progress = progress
            
            // Emit progress update
            if (uploadInput.value) {
                uploadInput.value.updateProgress?.(image.name, progress)
            }
        }, 200) // Update every 200ms
    }

    // Handle upload progress from UploadInputComponent
    const handleUploadProgress = ({ fileName, progress }) => {
        const image = imagePreviews.value.find(img => img.name === fileName)
        if (image) {
            image.progress = progress
            updateFormImages()
        }
    }

    // Update form.images with uploaded files
    const updateFormImages = () => {
        console.log('Updating form images...')
        
        // Ambil file dari UploadInputComponent jika ada
        const uploadInputFiles = uploadInput.value?.getAllFiles?.() || []
        
        // Ambil juga file dari imagePreviews (jika ada file langsung)
        const previewFiles = imagePreviews.value
            .filter(img => img.file && img.file instanceof File && img.progress === 100)
            .map(img => img.file)
        
        // Gabungkan semua file
        const allFiles = [...uploadInputFiles, ...previewFiles]
        
        // Hapus duplikat berdasarkan nama dan size
        const uniqueFiles = allFiles.filter((file, index, self) => 
            index === self.findIndex(f => 
                f.name === file.name && 
                f.size === file.size && 
                f.lastModified === file.lastModified
            )
        )
        
        console.log('Unique files to upload:', uniqueFiles.map(f => f.name))
        form.images = uniqueFiles
    }

    // Remove image
    const removeImage = (index) => {
        const image = imagePreviews.value[index]
        
        // Jika image adalah existing image dari server (ada ID), tambahkan ke delete_images
        if (image.id && !image.file) { // image.file tidak ada berarti dari server
            // Pastikan ID tidak duplikat
            if (!form.delete_images.includes(image.id)) {
                form.delete_images.push(image.id)
            }
        }
        
        imagePreviews.value.splice(index, 1)
        
        // Jika kita menghapus gambar utama dan ada gambar lain, buat yang pertama jadi utama
        if (image.isPrimary && imagePreviews.value.length > 0) {
            imagePreviews.value[0].isPrimary = true
        }
        
        updateFormImages()
    }

    // Set primary image
    const setPrimaryImage = (index) => {
        // Remove primary from all images
        imagePreviews.value.forEach(img => {
            img.isPrimary = false
        })  
        
        // Set new primary
        imagePreviews.value[index].isPrimary = true
        
        // Move primary image to first position
        const [primaryImage] = imagePreviews.value.splice(index, 1)
        imagePreviews.value.unshift(primaryImage)
    }

    // Clear all images
    const clearAllImages = () => {
        // Add all server images to delete list
        imagePreviews.value.forEach(image => {
            if (image.id && !image.file) {
                form.delete_images.push(image.id)
            }
        })
        
        imagePreviews.value = []
        form.images = []
        uploadActive.value = false
        
        if (uploadInput.value) {
            uploadInput.value.clearFiles()
        }
    }

    // Initialize with existing images
    onMounted(() => {
        if (props.existingImages && props.existingImages.length > 0) {
            // Existing images dari server (sudah ada di product_previews)
            imagePreviews.value = props.existingImages.map((img, index) => ({
                id: img.id, // ID dari product_previews table
                name: img.filename || 'image',
                size: img.size || 0,
                type: 'image/' + (img.extension || 'jpg'),
                url: img.url || '/storage/' + img.path, // Path dari database
                isPrimary: index === 0,
                progress: 100 // Already uploaded
            }))
            
            // Set existing images untuk thumbnail
            if (props.existingImages.length > 0 && props.product?.thumbnail) {
                const thumbnailImage = props.existingImages.find(img => 
                    img.path === props.product.thumbnail || 
                    '/storage/' + img.path === props.product.thumbnail
                )
                if (thumbnailImage) {
                    const index = props.existingImages.findIndex(img => img.id === thumbnailImage.id)
                    if (index !== -1 && imagePreviews.value[index]) {
                        imagePreviews.value[index].isPrimary = true
                    }
                }
            }
        }
        
        // Set default category_id jika tidak ada
        if (!form.category_id && props.categories.length > 0) {
            form.category_id = props.categories[0].id
        }
    })

    const submit = async () => {
        console.log('=== SUBMIT STARTED ===')
        console.log('Upload in progress:', uploadInProgress.value)
        console.log('Image previews count:', imagePreviews.value.length)
        
        // Pastikan semua upload selesai
        if (uploadInProgress.value) {
            notification.value?.warning('Harap tunggu sampai semua gambar selesai diunggah', 'Upload Sedang Berjalan')
            return
        }
        
        // Update form.images terakhir kali sebelum validasi
        await updateFormImages()
        
        console.log('Form images after update:', form.images.length)
        console.log('Form images detail:', form.images)
        
        // Validasi untuk create mode
        if (props.mode === 'create') {
            const hasImages = form.images.length > 0 || 
                            (props.existingImages && props.existingImages.length > 0)
            
            if (!hasImages) {
                notification.value?.error('Minimal satu gambar produk wajib diupload', 'Validation Error')
                
                // Debug: Tampilkan semua file yang ada
                const uploadInputFiles = uploadInput.value?.getAllFiles?.() || []
                console.log('UploadInput files:', uploadInputFiles.length)
                console.log('imagePreviews with file:', imagePreviews.value.filter(img => img.file).length)
                
                return
            }
        }
        
        // Pastikan form.images adalah array File objects
        if (form.images.length > 0 && !(form.images[0] instanceof File)) {
            console.error('form.images is not File objects:', form.images)
            notification.value?.error('Format file tidak valid. Silakan upload ulang gambar.', 'Error')
            return
        }
        
        // Prepare FormData untuk upload file
        const formData = new FormData()
        
        // Tambahkan data form biasa
        formData.append('title', form.title)
        formData.append('category_id', form.category_id)
        formData.append('price', form.price)
        formData.append('is_free', form.is_free ? '1' : '0')
        formData.append('description', form.description)
        formData.append('status', form.status)
        
        // Tambahkan images sebagai multiple files
        form.images.forEach((file, index) => {
            if (file instanceof File) {
                formData.append(`images[${index}]`, file)
            }
        })
        
        // Tambahkan delete_images jika ada
        if (form.delete_images.length > 0) {
            formData.append('delete_images', JSON.stringify(form.delete_images))
        }
        
        console.log('FormData entries:')
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ', pair[1])
        }
        
        // Gunakan FormData untuk submit
        if (props.mode === 'create') {
            form.post('/products', {
                data: formData, // Gunakan FormData
                preserveScroll: true,
                onSuccess: () => {
                    notification.value?.success('Produk berhasil ditambahkan!', 'Success')
                },
                onError: (errors) => {
                    console.log('Errors:', errors)
                    handleFormErrors(errors)
                }
            })
        } else {
            // Untuk update, gunakan method PUT dengan FormData
            const url = `/products/${props.product.id}`
            
            // Inertia tidak mendukung PUT dengan FormData secara langsung,
            // jadi kita gunakan approach berbeda
            form.transform((data) => ({
                ...data,
                _method: 'PUT' // Untuk spoofing PUT dengan FormData
            })).post(url, {
                data: formData,
                preserveScroll: true,
                onSuccess: () => {
                    notification.value?.success('Produk berhasil diperbarui!', 'Success')
                },
                onError: (errors) => {
                    handleFormErrors(errors)
                }
            })
        }
    }

    const handleFormErrors = (errors) => {
        // Show errors from controller
        if (errors.error) {
            notification.value?.error(errors.error, 'Error', {
                duration: 5000
            })
        } else if (errors.message) {
            notification.value?.error(errors.message, 'Error', {
                duration: 5000
            })
        } else {
            // Show first validation error
            const firstError = Object.values(errors)[0]
            notification.value?.error(firstError || 'Terjadi kesalahan saat menyimpan data', 'Error', {
                duration: 5000
            })
        }
    }

    const breadcrumbs = [
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Produk', href: '/products' },
        { label: props.mode === 'create' ? 'Tambah' : 'Edit', active: true },
    ]
</script>

<style scoped>
    .btn-primary {
        @apply px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed dark:bg-blue-500 dark:hover:bg-blue-600;
    }

    .btn-secondary {
        @apply px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700;
    }
</style>