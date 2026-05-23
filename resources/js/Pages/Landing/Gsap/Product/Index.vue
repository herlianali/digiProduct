<template>
    <LoadingScreen />
    <div class="p-4 bg-[#e6e6e6] min-h-screen font-sans">
        <div class="flex justify-between items-start mb-4">
            <h1 class="font-extrabold text-4xl tracking-widest uppercase w-5">Official Supplaybox Shop</h1>
            <NavbarFloating
                static-mode
                balance="$100"
                avatar-url=""
                @nav-click="(id) => console.log('navigated to', id)"
                @cart-click="() => console.log('cart clicked')"
                @cta-click="() => console.log('free sketch clicked')"
            />
        </div>
        <DropDownCategory />


    </div>
</template>

<script setup>
import LoadingScreen from '../ComponentsV2/LoadingScreen.vue';
import NavbarFloating from '../ComponentsV2/NavbarFloating.vue';
import DropDownCategory from '../Components/DropDownCategory.vue';

</script>
