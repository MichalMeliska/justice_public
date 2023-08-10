<template>
<div>

    <import v-if="!showForm" @differences="diffs"/>

    <differences v-else :data="diff" @differences="diffs"/>

</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Import from '@/js/components/schedule/Import.vue'
import Differences from '@/js/components/schedule/Differences.vue'

const diff = ref({});

const diffs = data => diff.value = data;

const showForm = computed(() => Object.keys(diff.value).length);
</script>