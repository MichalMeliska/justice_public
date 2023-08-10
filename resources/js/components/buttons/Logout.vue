<template>
<button type="button" title="Odhlásiť sa" @click="$emit('click')">
    <i class="fa-solid fa-right-from-bracket"></i>
</button>
</template>

<script setup>
defineEmits(['click']);
</script>

<style scoped>
i {
    color: yellow;
}

button {
    border: 1px solid yellow;
}

button:hover {
    background: #ffffa9;
}
</style>