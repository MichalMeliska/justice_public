<template>
<colgroup>
    <col v-for="col in columns" :key="col" :class="{ sort: col === sort_col }"/>
</colgroup>
</template>

<script setup>
defineProps(['columns', 'sort_col']);
</script>

<style scoped>
.sort {
    background: #cfcfcf;
}
</style>