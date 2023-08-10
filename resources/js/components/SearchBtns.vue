<template>
<div class="a1">
    <span class="search_btn"
        v-for="btn in all"
        :class="{ selected: selected.includes(btn) }"
        @click="$emit('select', btn)"
    >{{ btn }}</span>
</div>
</template>

<script setup>
defineProps(['all', 'selected'])
defineEmits(['select']);
</script>

<style scoped>
.a1 {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

span {
    border: 1px outset var(--nav_bg);
    color: var(--nav_bg);
    border-radius: 5px;
    width: 4rem;
    text-align: center;
    padding: 3px;
    cursor: pointer;
    display: inline-block;
}

span:hover {
    box-shadow: 0 0 3px 1px var(--theme);
}

.selected {
    color: var(--theme);
    background: var(--nav_bg);
    border: 1px solid var(--theme);
}
</style>