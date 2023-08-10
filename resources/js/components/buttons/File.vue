<template>
<div>

    <button title="Vybrať súbor" @click="click">
        <i class="fa-solid fa-file-import"></i>
    </button>

    <input
        type="file"
        class="no_disp"
        name="phonebook"
        accept=".xls, .xlsx, .xlsm"
        ref="input"
        @change="$emit('fileSelected', $event.target.files[0])"
    >

</div>
</template>

<script setup>
import { ref } from 'vue'

defineEmits(['fileSelected']);

const input = ref(null);

const click = () => input.value.click();
</script>

<style scoped>
i {
    color: blue;
}

button {
    border: 1px solid blue;
}

button:hover {
    background: #aac8ff;
}
</style>