import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue, route } from '../../vendor/tightenco/ziggy';

// Expoe route() globalmente para uso em <script setup> sem necessidade de import por componente
window.route = route;

createInertiaApp({
    title: (title) => title ? `${title} | Base SaaS` : 'Base SaaS',
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Sincroniza config do Ziggy para que window.route() funcione em script setup
        window.Ziggy = props.initialPage.props.ziggy;
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, props.initialPage.props.ziggy)
            .mount(el);
    },
    progress: {
        color: '#4f46e5',
    },
});
