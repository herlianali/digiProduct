<template>
    <DashboardLayout>
        <NotificationContainer ref="notification" />
        <BreadcrumbsComponent :items="breadcrumbs" />

        <div class="max-w-2xl mx-auto">
            <CardComponent :title="mode === 'create' ? 'Tambah Voucher' : 'Edit Voucher'">
                <form @submit.prevent="submit" class="space-y-6" novalidate>

                    <!-- Kode Voucher -->
                    <InputComponent
                        v-model="form.code"
                        label="Kode Voucher"
                        placeholder="Contoh: LAUNCH25"
                        :error="form.errors.code"
                        :error-message="form.errors.code"
                    />

                    <!-- Diskon -->
                    <InputComponent
                        v-model="form.discount_pct"
                        label="Diskon (%)"
                        type="number"
                        placeholder="0"
                        suffix="%"
                        :min="0"
                        :max="100"
                        :error="form.errors.discount_pct"
                        :error-message="form.errors.discount_pct"
                    />

                    <!-- Tanggal Kadaluarsa -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Kadaluarsa
                        </label>
                        <input
                            v-model="form.expires_at"
                            type="datetime-local"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                                   focus:ring focus:ring-brand-200 focus:border-brand-400
                                   dark:bg-gray-800 dark:text-gray-100"
                        />
                        <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ada batas kadaluarsa</p>
                        <p v-if="form.errors.expires_at" class="mt-1 text-xs text-red-500">
                            {{ form.errors.expires_at }}
                        </p>
                    </div>

                    <!-- Status Aktif -->
                    <ToggleSwitchComponent
                        v-model="form.is_active"
                        label="Status Voucher"
                        active-label="Aktif"
                        inactive-label="Tidak Aktif"
                    />

                    <!-- Actions -->
                    <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <Link href="/admin/vouchers" class="btn-secondary">Batal</Link>
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan</span>
                        </button>
                    </div>
                </form>
            </CardComponent>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import DashboardLayout       from '@/Layouts/DashboardLayout.vue'
import CardComponent         from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent  from '@/Components/Admin/BreadcrumbsComponent.vue'
import InputComponent        from '@/Components/Admin/InputComponent.vue'
import ToggleSwitchComponent from '@/Components/Admin/ToggleSwitchComponent.vue'
import NotificationContainer from '@/Components/Admin/NotificationContainer.vue'

const notification = ref()

const props = defineProps({
    mode:    { type: String, default: 'create' },
    voucher: { type: Object, default: null },
})

const form = useForm({
    code:         props.voucher?.code         ?? '',
    discount_pct: props.voucher?.discount_pct ?? '',
    expires_at:   props.voucher?.expires_at   ? props.voucher.expires_at.slice(0, 16) : '',
    is_active:    props.voucher?.is_active    ?? true,
})

const breadcrumbs = [
    { label: 'Dashboard', href: '/admin/dashboard' },
    { label: 'Vouchers',  href: '/admin/vouchers' },
    { label: props.mode === 'create' ? 'Tambah' : 'Edit', active: true },
]

const submit = () => {
    const onSuccess = () => notification.value?.success(
        props.mode === 'create' ? 'Voucher berhasil ditambahkan!' : 'Voucher berhasil diperbarui!',
        'Sukses'
    )
    const onError = (errors) => {
        const msg = errors.error ?? Object.values(errors)[0] ?? 'Terjadi kesalahan.'
        notification.value?.error(msg, 'Error')
    }

    if (props.mode === 'create') {
        form.post('/admin/vouchers', { preserveScroll: true, onSuccess, onError })
    } else {
        form.put(`/admin/vouchers/${props.voucher.id}`, { preserveScroll: true, onSuccess, onError })
    }
}
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
