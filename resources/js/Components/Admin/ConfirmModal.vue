<!-- Components/Admin/ConfirmModal.vue -->
<template>
    <Teleport to="body">
        <Transition name="modal">
            <div class="fixed inset-0 z-[2000] flex items-center justify-center px-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('cancel')" />

                <!-- Dialog -->
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
                    <!-- Icon -->
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
                    </div>

                    <h3 class="text-lg font-black text-gray-800 text-center mb-2">{{ title }}</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">{{ message }}</p>

                    <div class="flex gap-3">
                        <button
                            @click="$emit('cancel')"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            @click="$emit('confirm')"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors"
                            :class="confirmClass"
                        >
                            {{ confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

defineProps({
    title:        { type: String, default: 'Konfirmasi' },
    message:      { type: String, default: 'Apakah Anda yakin?' },
    confirmLabel: { type: String, default: 'Ya' },
    confirmClass: { type: String, default: 'bg-brand-600 hover:bg-brand-700 text-white' },
})
defineEmits(['confirm', 'cancel'])
</script>

<style scoped>
.modal-enter-active { transition: all 0.25s ease; }
.modal-leave-active { transition: all 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; transform: scale(0.95); }
</style>
