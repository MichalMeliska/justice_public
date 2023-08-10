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

        <tbl-computers
            v-bind="{ search, categories_selected }"
            @modelSelect="modelSelect"
            @setCategories="setCategories"
            @setCategsSelected="setCategsSelected"
            @assign="assign"
        />

    </div>

    <tools
        :model="model_selected"
        @rdp="rdp(model_selected.ComputerName)"
        @dameware="dameware(model_selected.ComputerName)"
        @folder="folder(model_selected.ComputerName)"
        @registerCopy="registerCopy(model_selected.ComputerSID)"
        @refresh="refresh(model_selected.ComputerSID)"
        @wol="wol(model_selected.Mac)"
        @assign="assign(model_selected.ComputerSID)"
    />

</div>
</template>

<script setup>
import useAD from '@/js/composables/useAD.js'
import TblComputers from '@/js/components/computers/Table.vue'

const {
    categories,
    model_selected,
    categories_selected,
    input,
    search,
    SearchBtns,
    Tools,
    rdp,
    dameware,
    folder,
    registerCopy,
    refresh,
    wol,
    assign,
    categSelect,
    modelSelect,
    setCategsSelected,
    setCategories
} = useAD({ categories_selected: ['KSTT', 'OSTT'] });
</script>