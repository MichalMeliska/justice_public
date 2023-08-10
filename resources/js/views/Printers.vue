<template>
<div>

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
                        v-for="printer in dataSorted"
                        :key="printer.ID"
                        :class="{ tr_selected: printer.ID == selected }"
                        :ref="el => { tbl_row[printer.ID] = el }"
                        @click="selectRow(printer.ID)"
                    >
                        <td class="align_left">{{ printer.name }}</td>
                        <td>{{ printer.cartridge }}</td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

</div>
</template>

<script setup>
import { ref, computed, onActivated } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useComposables from '@/js/composables'
import useSortAttr from '@/js/composables/useSortAttr.js'
import TblHead from '@/js/components/TblHead.vue'
import TblColGroup from '@/js/components/TblColGroup.vue'

const { axiosReq } = useComposables();

const route = useRoute();
const router = useRouter();

let columns = {
    name: {
        col: 'Názov',
        class: 'col_big'
    },
    cartridge: {
        col: 'Toner',
        class: 'col_medium'
    }
};

const data = ref([]);
const selected = ref();
const tbl_row = ref([]);

const {
    sort_col,
    sort_dir,
    sorting
} = useSortAttr({
    sort_col: 'name',
    selected,
    tbl_row
});

const selectRow = id => selected.value = (id == selected.value) ? null : id;

const dataSorted = computed(() => {

    return data.value.sort((a,b) => {

        if (a[sort_col.value] === null) return 1;
        if (b[sort_col.value] === null) return -1;

        let modifier = 1;

        if (sort_dir.value === 'desc') modifier = -1;

        return a[sort_col.value].toString().localeCompare(b[sort_col.value].toString(), 'sk-SK', {numeric: true}) * modifier;

    });

});

const getPrinters = () => {

    let req = () => axios.get('/api/getPrinters');

    let success = response => data.value = response.data;

    axiosReq(req, success);

}

onActivated(() => {

    if (route.params.id) {

        selected.value = route.params.id;

        router.push({ name: route.name });

    }

    if (tbl_row.value[selected.value]) tbl_row.value[selected.value].scrollIntoView({block: 'center'});

});

getPrinters();
</script>