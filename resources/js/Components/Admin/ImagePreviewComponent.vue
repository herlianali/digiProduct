<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
            {{ title }}
        </h3>
        
        <div v-if="images.length === 0" class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
            <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400">Belum ada gambar diunggah</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Upload gambar untuk melihat preview di sini</p>
        </div>
        
        <div v-else>
            <!-- Thumbnail Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="(image, index) in images" :key="image.id" 
                     class="relative group border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Image -->
                    <div class="aspect-square bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <img 
                            :src="getImageUrl(image)" 
                            :alt="image.name"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    
                    <!-- Overlay Actions -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity 
                                flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex space-x-2">
                            <button 
                                type="button"
                                @click="setAsPrimary(index)"
                                class="p-2 bg-white dark:bg-gray-800 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                :title="image.isPrimary ? 'Gambar Utama' : 'Jadikan Utama'"
                            >
                                <svg class="w-5 h-5" :class="image.isPrimary ? 'text-yellow-500' : 'text-gray-600 dark:text-gray-400'" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                            <button 
                                type="button"
                                @click="removeImage(index)"
                                class="p-2 bg-white dark:bg-gray-800 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                :title="'Hapus Gambar'"
                            >
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Primary Badge -->
                    <div v-if="image.isPrimary" 
                         class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                        Utama
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div v-if="image.progress < 100" 
                         class="absolute bottom-0 left-0 right-0 bg-gray-200 dark:bg-gray-700 h-1">
                        <div class="bg-blue-600 dark:bg-blue-500 h-1 transition-all duration-300" 
                             :style="{ width: image.progress + '%' }"></div>
                    </div>
                </div>
            </div>
            
            <!-- Image Info -->
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200">
                            {{ images.length }} gambar
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ totalImagesUploaded }} terunggah • {{ totalImagesPending }} dalam proses
                        </p>
                    </div>
                    
                    <button 
                        type="button"
                        @click="clearAll"
                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                        v-if="images.length > 0"
                    >
                        Hapus Semua
                    </button>
                </div>
                
                <!-- Image Details -->
                <div v-if="primaryImage" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mb-2">Gambar Utama:</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-16 h-16 rounded border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <img 
                                :src="getImageUrl(primaryImage)" 
                                :alt="primaryImage.name"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                                {{ primaryImage.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatFileSize(primaryImage.size) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    images: {
        type: Array,
        default: () => []
    },
    title: {
        type: String,
        default: 'Preview Gambar'
    }
})

const emit = defineEmits(['remove-image', 'set-primary', 'clear-all'])

const totalImagesUploaded = computed(() => {
    return props.images.filter(img => img.progress === 100).length
})

const totalImagesPending = computed(() => {
    return props.images.filter(img => img.progress < 100).length
})

const primaryImage = computed(() => {
    return props.images.find(img => img.isPrimary) || props.images[0]
})

const getImageUrl = (image) => {
    if (image.url) return image.url
    if (image.preview) return image.preview
    return '/images/placeholder.jpg'
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const removeImage = (index) => {
    emit('remove-image', index)
}

const setAsPrimary = (index) => {
    emit('set-primary', index)
}

const clearAll = () => {
    emit('clear-all')
}
</script>