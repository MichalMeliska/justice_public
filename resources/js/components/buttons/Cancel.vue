<template>
<button type="button" title="Zrušiť" @click="$emit('click')">
    <i class="fa-solid fa-circle-xmark"></i>
</button>
</template>

<script setup>
defineEmits(['click']);
</script>

<style scoped>
i {
    color: darkred;
}

button {
    border: 1px solid darkred;
}

button:hover {
    background: #f8c4c8;
}
</style>