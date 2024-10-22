import './bootstrap';
import '../css/app.css';
import '../css/vuetify.css';

// Import Notyf CSS and JS
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Vuetify imports
import { createVuetify } from 'vuetify';
import 'vuetify/styles'; // Vuetify styles
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { mdi } from 'vuetify/iconsets/mdi';

// Setup Vuetify instance
const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
    },
    theme: {
        defaultTheme: 'customTheme',
        themes: {
            customTheme: {
                colors: {
                    background: '#f0f0f0', // Your desired background color
                },
            },
        },
    }
});

// Create Notyf instance and make it available globally
const notyf = new Notyf({
    duration: 3000, // Duration for the notification
    ripple: true,   // Ripple effect on notifications
    position: { x: 'right', y: 'top' }, // Position of notifications
});

const appName = 'Syriatel Portal';

createInertiaApp({
    title: (title) => `${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .provide('notyf', notyf) // Make Notyf instance globally accessible
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
