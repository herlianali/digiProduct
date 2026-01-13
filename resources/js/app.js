import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import ProgressBar from './Components/ProgressBar.vue'

createInertiaApp({
  resolve: name =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })
    
    vueApp.use(plugin)
    
    // Register ProgressBar globally
    vueApp.component('ProgressBar', ProgressBar)
    
    vueApp.mount(el)
  },
})