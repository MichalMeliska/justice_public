import './bootstrap';

import { createApp } from 'vue/dist/vue.esm-bundler'
import { createPinia } from 'pinia'

import NavBar from '@/js/components/NavBar.vue'
import Notify from '@/js/components/Notify.vue'
import PopUp from '@/js/components/PopUp.vue'

import router from '@/js/router'

import '@/css/app.css'
import '@/css/global.css'
import '@/css/FontAwesome.css'

const app = createApp({
    components: { NavBar, Notify, PopUp }
})
    .use(router)
    .use(createPinia());

router.isReady().then(() => app.mount('#app'));