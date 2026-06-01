import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import ProgressBar from './Components/ProgressBar.vue'
import { provideCart } from './Composables/useCart'

createInertiaApp({
    resolve: name =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({
            setup() {
                provideCart()
            },
            render: () => h(App, props)
        })

        vueApp.use(plugin)
        vueApp.component('ProgressBar', ProgressBar)
        vueApp.mount(el)
    },
})
