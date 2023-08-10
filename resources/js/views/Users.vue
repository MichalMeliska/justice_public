<template>
<div>

    <div class="g1 g6">

        <router-link :to="{ name: 'Phonebook' }" title="Telefónny zoznam" class="component_tool" v-if="user_type !== 'user'">
            <i class="fa-solid fa-phone"></i>
        </router-link>

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

        <tbl-users
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
        @email="email(model_selected.EmailAddress)"
        @refresh="refresh(model_selected.UserSID)"
        @printer="defaultPrinter(model_selected)"
        @assign="assign(model_selected.UserSID)"
    />

</div>
</template>

<script setup>
import useAD from '@/js/composables/useAD.js'
import TblUsers from '@/js/components/users/Table.vue'

let user_type = user;

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
    email,
    refresh,
    defaultPrinter,
    assign,
    categSelect,
    modelSelect,
    setCategsSelected,
    setCategories
} = useAD({ categories_selected: ['KSTT', 'OSTT'] });
</script>

<style scoped>
.component_tool {
    position: absolute;
    top: 10px;
    right: 10px;
}

.component_tool:hover * {
    transform: rotateY(180deg);
    color: var(--theme);
}

.component_tool * {
    font-size: 1.5rem;
    color: var(--nav_bg);
    transition: .5s;
}
</style>