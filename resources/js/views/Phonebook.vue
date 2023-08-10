<template>
<div>

    <import v-if="!showForm" @changesUpdate="changesUpdate"/>

    <changes v-else :data="changes" @changesUpdate="changesUpdate"/>

</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Import from '@/js/components/phonebook/Import.vue'
import Changes from '@/js/components/phonebook/Changes.vue'

const changes = ref({});

const changesUpdate = data => changes.value = data;

const showForm = computed(() => Object.keys(changes.value).length);
</script>