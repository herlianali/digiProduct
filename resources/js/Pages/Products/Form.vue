<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="breadcrumbs"/>

        <CardComponent :title="mode === 'create' ? 'Tambah Produk' : 'Edit Produk'">
            <form @submit.prevent="submit" class="space-y-4">

                <div>
                    <label class="block font-medium">Nama Produk</label>
                    <input v-model="form.title" class="input" />
                </div>

                <div>
                    <label class="block font-medium">Tipe Produk</label>
                    <select v-model="form.type" class="input">
                        <option value="font">Font</option>
                        <option value="template">Template</option>
                        <option value="illustration">Illustration</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block font-medium">Harga</label>
                        <input
                            v-model="form.price"
                            type="number"
                            class="input"
                            :disabled="form.is_free"
                        />
                    </div>

                    <div class="flex items-center mt-6">
                        <input type="checkbox" v-model="form.is_free" />
                        <span class="ml-2">Gratis</span>
                    </div>
                </div>

                <div>
                    <label class="block font-medium">Deskripsi</label>
                    <textarea v-model="form.description" class="input"></textarea>
                </div>

                <div>
                    <label class="block font-medium">Status</label>
                    <select v-model="form.status" class="input">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <Link href="/products" class="btn-secondary">Batal</Link>
                    <button class="btn-primary">Simpan</button>
                </div>

            </form>
        </CardComponent>
    </DashboardLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CardComponent from '@/Components/Admin/CardComponent.vue'
import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue'

const props = defineProps({
    mode: String,
    product: Object
})

const form = useForm({
    title: props.product?.title ?? '',
    type: props.product?.type ?? 'font',
    price: props.product?.price ?? 0,
    is_free: props.product?.is_free ?? false,
    description: props.product?.description ?? '',
    status: props.product?.status ?? 'draft',
})

const submit = () => {
    props.mode === 'create'
        ? form.post('/products')
        : form.put(`/products/${props.product.id}`)
}

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Produk', href: '/products' },
    { label: props.mode === 'create' ? 'Tambah' : 'Edit', active: true },
]
</script>
