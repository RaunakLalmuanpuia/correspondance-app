import './bootstrap';
import '../css/app.css';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

// Import icon libraries
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'

// Import Quasar css
import 'quasar/src/css/index.sass'


import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import {ZiggyVue} from "../../vendor/tightenco/ziggy/dist"
import { Dialog, Loading, Notify, Quasar } from "quasar";
import { QuillEditor } from '@vueup/vue-quill'

const appName = import.meta.env.VITE_APP_NAME || 'PHED';

createInertiaApp({
    title: (title) => `${appName}`,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Quasar,{
                plugins:{Notify,Dialog,Loading}
            })
            .component('QuillEditor',QuillEditor)
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
