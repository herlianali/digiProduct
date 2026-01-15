<template>
    <DashboardLayout>
        <BreadcrumbsComponent :items="listBreadcrumbs"/>

        <CardComponent title="Management Produk">
        <TableComponent
            :columns="columns"
            :rows="rows"
            :per-page="5"
        />
        </CardComponent>

    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import CardComponent from '@/Components/Admin/CardComponent.vue';
    import BreadcrumbsComponent from '@/Components/Admin/BreadcrumbsComponent.vue';
    import TableComponent from '@/Components/Admin/TableComponent.vue';

    const listBreadcrumbs = [
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Produk', active: true },
    ];

    const columns = [
        { key: 'name', label: 'Name', sortable: true },
        { key: 'email', label: 'Email', sortable: true },
        { key: 'role', label: 'Role', sortable: false },
    ]

    const rows = [
        { name: 'Ali', email: 'ali@mail.com', role: 'Admin' },
        { name: 'Budi', email: 'budi@mail.com', role: 'User' },
    ]
</script>
