<template>
<transition>
    <div
        id="notify"
        v-show="show"
        :class="getNotify.type"
        class="center"
    >{{ getNotify.text }}</div>
</transition>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useStore } from '@/js/store'
import { storeToRefs } from 'pinia'

const { getNotify } = storeToRefs(useStore());

const show = ref(false);
let timeout;

watch(getNotify, newVal => {

    show.value = true;

    if (newVal.hide) {

        clearTimeout(timeout);
        timeout = setTimeout(() => show.value = false, 4000);

    }

})
</script>

<style scoped>
#notify {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50px;
    font-weight: bold;
    padding: 10px;
    z-index: 1;
}

.error {
    border-top: 2px solid #a3000f;
    color: #a3000f;
    background: #f8c4c8;
}

.success {
    border-top: 2px solid #155724;
    color: #155724;
    background: #c2edcc;
}

.v-enter-from, .v-leave-to {
    transform: translateY(72px);
}

.v-enter-active {
    transition: .5s;
}

.v-leave-active {
    transition: 1s;
}
</style>