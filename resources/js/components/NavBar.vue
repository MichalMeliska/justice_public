<template>
<nav>

    <router-link :to="{ name: 'Users' }" title="Užívatelia" class="center">
        <i class="fa-solid fa-user" :class="{ selected: $route.name === 'Users' }"></i>
    </router-link>

    <router-link :to="{ name: 'Computers' }" title="Počítače" class="center">
        <i class="fa-solid fa-computer" :class="{ selected: $route.name === 'Computers' }"></i>
    </router-link>

    <router-link :to="{ name: 'Printers' }" title="Tlačiarne" class="center">
        <i class="fa-solid fa-print" :class="{ selected: $route.name === 'Printers' }"></i>
    </router-link>

    <router-link :to="{ name: 'Servers' }" title="Servery" class="center" v-if="user_type !== 'user'">
        <i class="fa-solid fa-server" :class="{ selected: $route.name === 'Servers' }"></i>
    </router-link>

    <router-link :to="{ name: 'Attendance' }" title="Dochádzka" class="center">
        <i class="fa-solid fa-calendar-days" :class="{ selected: $route.name === 'Attendance' }"></i>
    </router-link>

    <router-link :to="{ name: 'Schedule' }" title="Rozvrh práce" class="center" v-if="user_type !== 'user'">
        <i class="fa-solid fa-code-compare" :class="{ selected: $route.name === 'Schedule' }"></i>
    </router-link>

    <router-link :to="{ name: 'IP' }" title="IP adresy" class="center" v-if="user_type !== 'user'">
        <i class="fa-solid fa-link" :class="{ selected: $route.name === 'IP' }"></i>
    </router-link>

    <app-loading class="app_loading"/>

</nav>
</template>

<script setup>
import { onMounted } from 'vue'
import useComposables from '@/js/composables'
import AppLoading from '@/js/components/AppLoading.vue'

let data_loaded = Date.now();

let user_type = user;

const { getRefreshData } = useComposables();

onMounted(() => {
    window.addEventListener('focus', async () => {

        const load_d = new Date(data_loaded);
        const cur_d = new Date();

        if (
            (Date.now() - data_loaded) / 1000 >= 600 ||       // 10 minutes
            (
                (cur_d.getMinutes() % 10) >= 1 &&
                (cur_d.getMinutes() % 10) < (load_d.getMinutes() % 10)
            )
        ) {

            await getRefreshData();

            data_loaded = Date.now();

        }

    });
});
</script>

<style scoped>
nav {
    display: flex;
    background: var(--nav_bg);
    padding: 15px;
    gap: 20px;
    position: relative;
    border-bottom: 2px solid var(--theme);
}

a {
    width: 90px;
    height: 45px;
    border-radius: 3px;
    transition: 0.3s;
}

a:hover {
	box-shadow: 0 0 4px 2px var(--theme);
}

i {
    color: white;
    font-size: 2rem;
}

.selected {
    color: var(--theme);
}

.app_loading {
    position: absolute;
    top: 13px;
    right: 13px;
}
</style>