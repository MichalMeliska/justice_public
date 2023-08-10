<template>
<div
    id="black_screen"
    v-show="getPopUp.component"
    @click.stop="blackScreenClick($event.target.id)"
    class="center"
>

    <transition>

        <div id="pop_up" v-show="getPopUp.component">

            <div>

                <component :is="component[getPopUp.component]" :attr="getPopUp.attr"/>

            </div>

        </div>

    </transition>

</div>
</template>

<script setup>
import { defineAsyncComponent, onMounted } from 'vue'
import { useStore } from '@/js/store'
import { storeToRefs } from 'pinia'

const Store = useStore();
const { getPopUp } = storeToRefs(Store);
const { setPopUp } = Store;

const component = {
    PrinterDefault: defineAsyncComponent(() => import('@/js/components/popUp/PrinterDefault.vue'))
}

const blackScreenClick = id => {
    if (id === 'black_screen') setPopUp(null);
}

onMounted(() => {

    window.addEventListener('keyup', e => {

        if (e.key === 'Escape' && getPopUp.value.component) {

            e.stopImmediatePropagation();

            setPopUp(null);

        }

    });

});
</script>

<style scoped>
#black_screen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #000000d1;
    z-index: 100;
}

#pop_up {
    display: flex;
    max-width: 85%;
    max-height: 85%;
    background: var(--body_bg);
    padding: 10px;
    box-shadow: 0 0 10px 3px var(--theme);
}

.v-enter-active {
    animation: zoom .3s linear;
}

@keyframes zoom {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
}

#pop_up > div {
    overflow: auto;
}
</style>