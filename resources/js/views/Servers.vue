<template>
<div>

    <div class="g1 g6">

        <search-btns
            :all="categories"
            :selected="categories_selected"
            @select="categSelect"
        />

        <input
            type="search"
            class="txt_input g2"
            autocomplete="off"
            ref="input"
            v-model="search"
        />

        <tbl-servers
            v-bind="{ search, categories_selected }"
            @modelSelect="modelSelect"
            @setCategories="setCategories"
            @setCategsSelected="setCategsSelected"
        />

    </div>

    <tools
        :model="model_selected"
        @rdp="rdp(model_selected.Name)"
        @folder="folder(model_selected.Name)"
        @refresh="refresh(model_selected.SID)"
        @regRestart="regRestart(model_selected.SID)"
    />

</div>
</template>

<script setup>
import useAD from '@/js/composables/useAD.js'
import TblServers from '@/js/components/servers/Table.vue'

const {
    categories,
    model_selected,
    categories_selected,
    input,
    search,
    SearchBtns,
    Tools,
    rdp,
    folder,
    refresh,
    regRestart,
    categSelect,
    modelSelect,
    setCategsSelected,
    setCategories
} = useAD({ categories_selected: ['Podateľne'] });
</script>

<style scoped>
:deep(.search_btn) {
    width: 7rem;
}
</style>