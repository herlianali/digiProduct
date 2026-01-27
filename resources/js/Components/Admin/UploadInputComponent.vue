<template>
    <div class="space-y-4">
        <div 
            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer"
            @dragenter.prevent="onDragEnter"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
            @click="openFilePicker"
            :class="{
                'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20': isDragging,
                'border-red-500 dark:border-red-400 bg-red-50 dark:bg-red-900/20': hasError
            }"
        >
            <div class="max-w-xs mx-auto">
                <div class="flex justify-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ label }}
                </h3>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ hint }}
                </p>
                
                <button 
                    type="button"
                    class="btn-primary inline-flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Pilih File
                </button>
                
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
                    {{ acceptHint }}
                </p>
            </div>
            
            <!-- REMOVED :required attribute -->
            <input 
                type="file"
                ref="fileInput"
                :multiple="multiple"
                :accept="accept"
                class="hidden"
                @change="onFileSelected"
            />
        </div>
        
        <!-- Error Message -->
        <div v-if="hasError" class="text-sm text-red-600 dark:text-red-400">
            {{ errorMessage }}
        </div>
        
        <!-- File List -->
        <div v-if="files.length > 0" class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ files.length }} file{{ files.length > 1 ? 's' : '' }} dipilih
                </span>
                <button 
                    type="button"
                    @click="clearFiles"
                    class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                >
                    Hapus Semua
                </button>
            </div>
            
            <div v-for="(file, index) in files" :key="file.id" 
                 class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex items-center space-x-3">
                    <!-- File Icon -->
                    <div class="w-10 h-10 flex items-center justify-center rounded bg-gray-200 dark:bg-gray-700">
                        <template v-if="file.type.startsWith('image/')">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </template>
                        <template v-else>
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </template>
                    </div>
                    
                    <!-- File Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                            {{ file.name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatFileSize(file.size) }}
                            <span v-if="file.progress < 100">
                                • {{ file.progress }}%
                            </span>
                        </p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <!-- Progress Bar -->
                    <div v-if="file.progress < 100" class="w-24">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-blue-600 dark:bg-blue-500 h-1.5 rounded-full transition-all duration-300" 
                                 :style="{ width: file.progress + '%' }"></div>
                        </div>
                    </div>
                    
                    <!-- Remove Button -->
                    <button 
                        type="button"
                        @click="removeFile(index)"
                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-1"
                        :disabled="file.progress < 100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    label: {
        type: String,
        default: 'Upload File'
    },
    hint: {
        type: String,
        default: 'Seret dan lepas file di sini atau klik untuk memilih file'
    },
    accept: {
        type: String,
        default: 'image/*'
    },
    acceptHint: {
        type: String,
        default: 'PNG, JPG, GIF maksimal 10MB'
    },
    multiple: {
        type: Boolean,
        default: true
    },
    maxFiles: {
        type: Number,
        default: 5
    },
    maxSize: {
        type: Number,
        default: 10 * 1024 * 1024 // 10MB
    }
})

const emit = defineEmits(['update:modelValue', 'upload-progress', 'files-selected'])

const fileInput = ref(null)
const isDragging = ref(false)
const files = ref([])
const errorMessage = ref('')

const hasError = computed(() => errorMessage.value.length > 0)

const openFilePicker = () => {
    fileInput.value.click()
}

const onFileSelected = (event) => {
    const selectedFiles = Array.from(event.target.files)
    validateAndAddFiles(selectedFiles)
    // Clear input value to allow selecting same file again
    fileInput.value.value = ''
}

const onDragEnter = () => {
    isDragging.value = true
}

const onDragOver = () => {
    isDragging.value = true
}

const onDragLeave = () => {
    isDragging.value = false
}

const onDrop = (event) => {
    isDragging.value = false
    const droppedFiles = Array.from(event.dataTransfer.files)
    validateAndAddFiles(droppedFiles)
}

const validateAndAddFiles = (newFiles) => {
    errorMessage.value = ''
    
    // Check max files
    if (files.value.length + newFiles.length > props.maxFiles) {
        errorMessage.value = `Maksimal ${props.maxFiles} file yang dapat diupload`
        return
    }
    
    const validFiles = []
    
    for (const file of newFiles) {
        // Check file size
        if (file.size > props.maxSize) {
            errorMessage.value = `File ${file.name} terlalu besar. Maksimal ${formatFileSize(props.maxSize)}`
            continue
        }
        
        // Check file type for images
        if (props.accept.includes('image') && !file.type.startsWith('image/')) {
            errorMessage.value = `File ${file.name} bukan gambar. Hanya file gambar yang diterima`
            continue
        }
        
        validFiles.push({
            id: Date.now() + Math.random(),
            file: file,
            name: file.name,
            size: file.size,
            type: file.type,
            preview: null,
            progress: 0
        })
    }
    
    if (validFiles.length > 0) {
        // Generate previews for images
        const previewPromises = validFiles.map(fileObj => {
            return new Promise((resolve) => {
                if (fileObj.type.startsWith('image/')) {
                    const reader = new FileReader()
                    reader.onload = (e) => {
                        fileObj.preview = e.target.result
                        resolve()
                    }
                    reader.readAsDataURL(fileObj.file)
                } else {
                    resolve()
                }
            })
        })
        
        // Wait for all previews to be generated
        Promise.all(previewPromises).then(() => {
            files.value = [...files.value, ...validFiles]
            
            // Emit events
            emit('files-selected', validFiles.map(f => f.file))
            updateModelValue()
        })
    }
}

const removeFile = (index) => {
    files.value.splice(index, 1)
    updateModelValue()
}

const clearFiles = () => {
    files.value = []
    updateModelValue()
    emit('upload-progress', { fileName: 'all', progress: 0 })
}

const updateModelValue = () => {
    emit('update:modelValue', files.value.map(f => f.file))
}

const updateProgress = (fileName, progress) => {
    const file = files.value.find(f => f.name === fileName)
    if (file) {
        file.progress = progress
        emit('upload-progress', { fileName, progress })
    }
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Watch for modelValue changes
watch(() => props.modelValue, (newValue) => {
    if (Array.isArray(newValue) && newValue.length === 0) {
        files.value = []
    }
}, { deep: true })

onMounted(() => {
    // Initialize with existing files from modelValue
    if (props.modelValue && props.modelValue.length > 0) {
        files.value = props.modelValue.map(file => ({
            id: Date.now() + Math.random(),
            file: file,
            name: file.name || 'File',
            size: file.size || 0,
            type: file.type || 'application/octet-stream',
            preview: null,
            progress: 100 // Already uploaded
        }))
    }
})

const getAllFiles = () => {
    return files.value.map(f => f.file).filter(file => file instanceof File)
}

defineExpose({
    clearFiles,
    updateProgress,
    getAllFiles
})
</script>

<style scoped>
.btn-primary {
    @apply px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors dark:bg-blue-500 dark:hover:bg-blue-600;
}
</style>