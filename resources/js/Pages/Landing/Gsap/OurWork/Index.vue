<script setup>
import NavbarFloating from '../ComponentsV2/NavbarFloating.vue'
</script>

<template>
    <div class="bg-black max-h-screen overflow-hidden items-center justify-center flex flex-col gap-12">
        <div class="bg-[#1a1a1a] rounded-b-2xl p-6 ">
            <div class="flex items-center justify-between">
                <h1 class="font-bold text-white text-4xl uppercase">supplybox</h1>

                <NavbarFloating
                    static-mode
                    balance="$100"
                    avatar-url=""
                    @nav-click="(id) => console.log('navigated to', id)"
                    @cart-click="() => console.log('cart clicked')"
                    @cta-click="() => console.log('free sketch clicked')"
                />
            </div>
            <div class="grid grid-cols-3 gap-6 mt-12">
                <!-- image card preview and list image -->
                <div class="col-span-1 bg-white/90 rounded-lg p-6 shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Our Work</h2>
                    <p class="text-gray-700 mb-4">
                        At SupplyBox, we pride ourselves on delivering top-notch digital solutions that drive results. Our team of experts has successfully completed a wide range of projects, from e-commerce platforms to custom web applications. We work closely with our clients to understand their unique needs and goals, ensuring that every project we undertake is tailored to their specific requirements. Our portfolio showcases a diverse array of work, demonstrating our ability
                        to create innovative and effective solutions across various industries. Whether you're looking to build a new website, develop a mobile app, or enhance your existing digital presence, SupplyBox has the expertise and experience to help you achieve your goals.
                    </p>
                    <button class="bg-[#4dfa03] hover:bg-green-400 font-extrabold text-black px-4 py-2 rounded-full transition-all">
                        View Portfolio
                    </button>
                </div>
                <!-- description for project -->
                <div class="col-span-2 bg-white/90 rounded-lg p-6 shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Case Studies</h2>
                    <p class="text-gray-700 mb-4">
                        Explore our case studies to see how we've helped businesses like yours achieve success. Each case study provides an in-depth look at the challenges our clients faced, the solutions we implemented, and the results we delivered. From increasing online sales to improving user engagement, our case studies highlight the tangible benefits of working with SupplyBox. Discover how we've transformed businesses and learn how we can help you achieve similar results.
                    </p>
                    <button class="bg-[#4dfa03] hover:bg-green-400 font-extrabold text-black px-4 py-2 rounded-full transition-all">
                        Read Case Studies
                    </button>

                </div>
            </div>
        </div>
        <button class="">
            <span class="text-[200px] text-center text-white font-bold">Get in touch</span>
        </button>
    </div>
</template>
