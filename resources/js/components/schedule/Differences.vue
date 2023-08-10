<template>
<div class="g1">

    <div class="g1 g6">

        <div class="tbl_container">

            <table>

                <tbl-col-group :columns="Object.keys(columns)" :sort_col="sort_col"/>

                <tbl-head
                    @sorting="sorting"
                    v-bind="{ columns, sort_col, sort_dir }"
                />

                <tbody>
                    <tr
                        v-for="row, i in dataSorted"
                        :key="i"
                        :class="row.attr"
                    >
                        <td>{{ row.cislo }}</td>
                        <td>{{ row.agenda }}</td>
                        <td>{{ row.sudca }}</td>
                        <td v-if="sud === 'ks'">{{ row.sudca2 }}</td>
                        <td v-if="sud === 'ks'">{{ row.sudca3 }}</td>
                        <td>{{ row.asistent }}</td>
                        <td>{{ row.tajomnik }}</td>
                        <td>{{ row.vsu }}</td>
                        <td>{{ row.pomer }}</td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

    <tools
        :show="true"
        @cancel="$emit('differences', {})"
    />

</div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import useSortAttr from '@/js/composables/useSortAttr.js'
import TblHead from '@/js/components/TblHead.vue'
import TblColGroup from '@/js/components/TblColGroup.vue'
import Tools from '@/js/components/Tools.vue'

const {
    sort_col,
    sort_dir,
    sorting
} = useSortAttr({sort_col: 'cislo'});

const props = defineProps(['data']);
const emit = defineEmits(['differences']);

const columns = ref({
    cislo: {
        col: 'Číslo',
        class: 'col_small'
    },
    agenda: {
        col: 'Agenda',
        class: 'col_small'
    },
    sudca: {
        col: 'Sudca',
        class: 'col_big'
    },
    sudca2: {
        col: 'Sudca 2',
        class: 'col_big'
    },
    sudca3: {
        col: 'Sudca 3',
        class: 'col_big'
    },
    asistent: {
        col: 'Asistent',
        class: 'col_big'
    },
    tajomnik: {
        col: 'Tajomník',
        class: 'col_big'
    },
    vsu: {
        col: 'VSU',
        class: 'col_big'
    },
    pomer: {
        col: 'Pomer',
        class: 'col_small'
    }
});

const sud = ref();

watch(() => props.data, newVal => {

    let sudca2 = false;

    newVal.forEach(el => {
        if (el.hasOwnProperty('sudca2')) sudca2 = true;
    });

    if (sudca2) sud.value = 'ks';
    else {

        delete columns.value.sudca2;
        delete columns.value.sudca3;

        sud.value = 'os';

    }

}, {immediate:true});

const dataSorted = computed(() => props.data.sort((a,b) => {

    if (a[sort_col.value] === null) return 1;
    if (b[sort_col.value] === null) return -1;

    let modifier = 1;

    if (sort_dir.value === 'desc') modifier = -1;

    return (
        (a[sort_col.value].toString().localeCompare(b[sort_col.value].toString(), 'sk-SK', {numeric: true}) * modifier) ||
        a['cislo'].toString().localeCompare(b['cislo'].toString(), 'sk-SK', {numeric: true})
    );

}));
</script>

<style scoped>
.delete * {
    text-decoration: line-through;
}

.missing * {
    font-weight: bold;
}
</style>