<template>
    <DashboardLayout>
        <NotificationContainer ref="notification" />
        <BreadcrumbsComponent :items="breadcrumbs" />

        <div class="max-w-3xl mx-auto">
            <CardComponent :title="mode === 'create' ? 'Tambah Inquiry' : 'Edit Inquiry'">
                <form @submit.prevent="submit" class="space-y-6" novalidate>

                    <!-- Nama + Perusahaan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <InputComponent
                            v-model="form.full_name"
                            label="Nama Klien"
                            placeholder="Masukkan nama klien"
                            :error="!!errors.full_name"
                            :error-message="errors.full_name?.[0]"
                        />
                        <InputComponent
                            v-model="form.company_name"
                            label="Nama Perusahaan"
                            placeholder="Opsional"
                            :error="!!errors.company_name"
                            :error-message="errors.company_name?.[0]"
                        />
                    </div>

                    <!-- Email + Budget -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <InputComponent
                            v-model="form.email"
                            label="Email"
                            type="email"
                            placeholder="email@contoh.com"
                            :error="!!errors.email"
                            :error-message="errors.email?.[0]"
                        />
                        <InputComponent
                            v-model="form.budget"
                            label="Budget"
                            placeholder="Contoh: $500 - $1000"
                            :error="!!errors.budget"
                            :error-message="errors.budget?.[0]"
                        />
                    </div>

                    <!-- Voucher -->
                    <SelectComponent
                        v-model="form.voucher_code"
                        label="Voucher (opsional)"
                        placeholder="Pilih voucher"
                        :options="voucherOptions"
                        :error="!!errors.voucher_code"
                        :error-message="errors.voucher_code?.[0]"
                    />

                    <!-- Project Scopes -->
                    <div>
                        <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Project Scopes</p>
                        <div class="grid grid-cols-2 gap-4">
                            <label
                                v-for="scope in scopeOptions"
                                :key="scope.value"
                                class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition"
                                :class="form.scopes.includes(scope.value)
                                    ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'"
                            >
                                <input
                                    type="checkbox"
                                    :value="scope.value"
                                    v-model="form.scopes"
                                    class="size-4 accent-black"
                                />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ scope.label }}</span>
                            </label>
                        </div>
                        <p v-if="errors.scopes" class="mt-1 text-xs text-red-500">{{ errors.scopes?.[0] }}</p>
                    </div>

                    <!-- Timeline -->
                    <div>
                        <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Timeline</p>
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                v-for="tl in timelineOptions"
                                :key="tl.value"
                                class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition"
                                :class="form.timelines.includes(tl.value)
                                    ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'"
                            >
                                <input
                                    type="checkbox"
                                    :value="tl.value"
                                    v-model="form.timelines"
                                    class="size-4 accent-black"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ tl.label }}</span>
                            </label>
                        </div>
                        <p v-if="errors.timelines" class="mt-1 text-xs text-red-500">{{ errors.timelines?.[0] }}</p>
                    </div>

                    <!-- Deskripsi -->
                    <TextareaComponent
                        v-model="form.project_description"
                        label="Deskripsi Project"
                        placeholder="Ceritakan kebutuhan project"
                        :rows="4"
                        :error="!!errors.project_description"
                        :error-message="errors.project_description?.[0]"
                    />

                    <!-- Status -->
                    <RadioGroupComponent
                        v-model="form.status"
                        label="Status"
                        :options="statusOptions"
                        :error="!!errors.status"
                        :error-message="errors.status?.[0]"
                    />

                    <!-- Actions -->
                    <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <Link href="/admin/inquiries" class="btn-secondary">Batal</Link>
                        <button type="submit" class="btn-primary" :disabled="processing">
                            <span v-if="processing">Menyimpan...</span>
                            <span v-else>Simpan</span>
                        </button>
                    </div>
                </form>
            </CardComponent>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import DashboardLayout       from '@/Layouts/DashboardLayout.vue'
import CardComponent         from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent  from '@/Components/Admin/BreadcrumbsComponent.vue'
import InputComponent        from '@/Components/Admin/InputComponent.vue'
import SelectComponent       from '@/Components/Admin/SelectComponent.vue'
import TextareaComponent     from '@/Components/Admin/TextareaComponent.vue'
import RadioGroupComponent   from '@/Components/Admin/RadioGroupComponent.vue'
import NotificationContainer from '@/Components/Admin/NotificationContainer.vue'

const notification = ref()
const processing   = ref(false)
const errors       = reactive({})

const props = defineProps({
    mode:     { type: String, default: 'create' },
    inquiry:  { type: Object, default: null },
    vouchers: { type: Array,  default: () => [] },
})

// ─── Form state ───────────────────────────────────────────────────
const form = reactive({
    full_name:           props.inquiry?.full_name           ?? '',
    company_name:        props.inquiry?.company_name        ?? '',
    email:               props.inquiry?.email               ?? '',
    budget:              props.inquiry?.budget              ?? '',
    voucher_code:        props.inquiry?.voucher_code        ?? null,
    project_description: props.inquiry?.project_description ?? '',
    status:              props.inquiry?.status              ?? 'new',
    scopes:              props.inquiry?.scopes              ?? [],
    timelines:           props.inquiry?.timelines           ?? [],
})

// ─── Options ─────────────────────────────────────────────────────
const scopeOptions = [
    { value: 'design_service',       label: 'Design Service' },
    { value: 'illustration_service', label: 'Illustration Service' },
]
const timelineOptions = [
    { value: 'asap',      label: 'As soon as possible' },
    { value: '1_2_weeks', label: '1 - 2 Weeks' },
    { value: 'long_term', label: 'Long Term' },
    { value: 'flexible',  label: 'Flexible' },
]
const statusOptions = [
    { value: 'new',       label: 'New' },
    { value: 'in_review', label: 'In Review' },
    { value: 'accepted',  label: 'Accepted' },
    { value: 'rejected',  label: 'Rejected' },
]
const voucherOptions = computed(() => [
    { value: null, label: 'Tidak menggunakan voucher' },
    ...props.vouchers.map(v => ({ value: v.code, label: `${v.code} (${v.discount_pct}%)` })),
])

const breadcrumbs = [
    { label: 'Dashboard',       href: '/admin/dashboard' },
    { label: 'Project Inquiry', href: '/admin/inquiries' },
    { label: props.mode === 'create' ? 'Tambah' : 'Edit', active: true },
]

// ─── Submit via axios + _method spoofing ──────────────────────────
const submit = async () => {
    // Reset errors
    Object.keys(errors).forEach(k => delete errors[k])
    processing.value = true

    const url = props.mode === 'create'
        ? '/admin/inquiries'
        : `/admin/inquiries/${props.inquiry.id}`

    const payload = {
        ...form,
        // Laravel method spoofing: kirim selalu POST, tapi Laravel baca sebagai PUT saat edit
        _method: props.mode === 'edit' ? 'PUT' : undefined,
    }

    try {
        await axios.post(url, payload, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept':       'application/json',
                'Content-Type': 'application/json',
            },
        })

        notification.value?.success(
            props.mode === 'create' ? 'Inquiry berhasil ditambahkan!' : 'Inquiry berhasil diperbarui!',
            'Sukses'
        )

        // Redirect ke index setelah sukses
        setTimeout(() => router.visit('/admin/inquiries'), 800)

    } catch (err) {
        if (err.response?.status === 422) {
            Object.assign(errors, err.response.data.errors ?? {})
            const firstMsg = Object.values(err.response.data.errors ?? {})[0]?.[0] ?? 'Periksa kembali isian form.'
            notification.value?.error(firstMsg, 'Validasi Gagal')

        } else if (err.response?.status === 403) {
            notification.value?.error('Anda tidak memiliki akses.', 'Akses Ditolak')

        } else {
            notification.value?.error(
                err.response?.data?.message ?? 'Terjadi kesalahan server.',
                'Error'
            )
            console.error('Submit error:', err)
        }
    } finally {
        processing.value = false
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
