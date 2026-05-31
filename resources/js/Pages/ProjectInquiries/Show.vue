<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs" />

        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Info Klien -->
            <CardComponent title="Informasi Klien">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Nama</dt>
                        <dd class="text-gray-800 dark:text-gray-100 font-medium">{{ inquiry.full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Perusahaan</dt>
                        <dd class="text-gray-800 dark:text-gray-100">{{ inquiry.company_name || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Email</dt>
                        <dd>
                            <a :href="`mailto:${inquiry.email}`" class="text-blue-600 hover:underline">{{ inquiry.email }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Budget</dt>
                        <dd class="text-gray-800 dark:text-gray-100">{{ inquiry.budget || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Voucher</dt>
                        <dd>
                            <span v-if="inquiry.voucher_code" class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full font-bold">
                                {{ inquiry.voucher_code }}
                                <span v-if="inquiry.voucher"> ({{ inquiry.voucher.discount_pct }}%)</span>
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Status</dt>
                        <dd>
                            <span :class="statusBadgeClass(inquiry.status)" class="px-2.5 py-1 text-xs font-semibold rounded-full inline-flex items-center">
                                {{ statusLabel(inquiry.status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </CardComponent>

            <!-- Scope & Timeline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <CardComponent title="Project Scopes">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="scope in (inquiry.scopes ?? [])"
                            :key="scope"
                            class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full font-medium"
                        >
                            {{ scopeLabel(scope) }}
                        </span>
                        <span v-if="!inquiry.scopes?.length" class="text-gray-400 text-sm">-</span>
                    </div>
                </CardComponent>

                <CardComponent title="Timeline">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="tl in (inquiry.timelines ?? [])"
                            :key="tl"
                            class="px-3 py-1 bg-purple-100 text-purple-700 text-sm rounded-full font-medium"
                        >
                            {{ timelineLabel(tl) }}
                        </span>
                        <span v-if="!inquiry.timelines?.length" class="text-gray-400 text-sm">-</span>
                    </div>
                </CardComponent>
            </div>

            <!-- Deskripsi -->
            <CardComponent title="Deskripsi Project">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                    {{ inquiry.project_description || 'Tidak ada deskripsi.' }}
                </p>
            </CardComponent>

            <!-- Meta -->
            <CardComponent title="Informasi Sistem">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Dibuat</dt>
                        <dd class="text-gray-700 dark:text-gray-300">{{ formatDate(inquiry.created_at) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Diperbarui</dt>
                        <dd class="text-gray-700 dark:text-gray-300">{{ inquiry.updated_at ? formatDate(inquiry.updated_at) : '-' }}</dd>
                    </div>
                </dl>
            </CardComponent>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <Link href="/admin/inquiries" class="btn-secondary">Kembali</Link>
                <Link :href="`/admin/inquiries/${inquiry.id}/edit`" class="btn-primary">Edit Inquiry</Link>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import DashboardLayout      from '@/Layouts/DashboardLayout.vue'
import CardComponent        from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'

const props = defineProps({
    inquiry: { type: Object, required: true },
})

const breadcrumbs = [
    { label: 'Dashboard',       href: '/admin/dashboard' },
    { label: 'Project Inquiry', href: '/admin/inquiries' },
    { label: props.inquiry.full_name, active: true },
]

const statusLabel   = (s) => ({ new: 'New', in_review: 'In Review', accepted: 'Accepted', rejected: 'Rejected' }[s] ?? s)
const scopeLabel    = (s) => ({ design_service: 'Design Service', illustration_service: 'Illustration Service' }[s] ?? s)
const timelineLabel = (t) => ({ asap: 'As Soon As Possible', '1_2_weeks': '1 - 2 Weeks', long_term: 'Long Term', flexible: 'Flexible' }[t] ?? t)
const formatDate    = (d) => new Date(d).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })

const statusBadgeClass = (s) => ({
    new:       'bg-blue-100 text-blue-800 border border-blue-200',
    in_review: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    accepted:  'bg-green-100 text-green-800 border border-green-200',
    rejected:  'bg-red-100 text-red-800 border border-red-200',
}[s] ?? 'bg-gray-100 text-gray-600')
</script>

<style scoped>
.btn-primary {
    @apply px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700
           transition-colors dark:bg-blue-500 dark:hover:bg-blue-600;
}
.btn-secondary {
    @apply px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium
           hover:bg-gray-50 transition-colors
           dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700;
}
</style>
