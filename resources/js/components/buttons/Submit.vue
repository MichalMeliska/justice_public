<template>
<button type="button" title="Potvrdiť" @click="$emit('click')">
    <i class="fa-solid fa-circle-check"></i>
</button>
</template>

<script setup>
defineEmits(['click']);
</script>

<style scoped>
i {
    color: green;
}

button {
    border: 1px solid green;
}

button:hover {
    background: #c2edcc;
}
</style>