<template>
<div v-show="getLoading" id="api_loading"></div>
</template>

<script setup>
import { useStore } from '@/js/store'
import { storeToRefs } from 'pinia'

const { getLoading } = storeToRefs(useStore());
</script>

<style scoped>
#api_loading {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid #fff;
    border-color: var(--theme) transparent transparent;
    animation: api_loading .5s linear infinite;
}

@keyframes api_loading {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>