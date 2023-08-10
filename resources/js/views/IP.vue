<template>
<div>

    <div class="g1 g6">

        <div class="tbl_container">

            <table>

                <tbl-col-group :columns="Object.keys(columns)" sort_col="attr"/>

                <tbl-head
                    :sortable="false"
                    :columns="columns"
                />

                <tbody>
                    <tr v-for="row in data" :key="row.attr">
                        <td class="align_left">{{ row.attr }}</td>
                        <td>{{ row.kstt }}</td>
                        <td>{{ row.ostt }}</td>
                        <td>{{ row.osds }}</td>
                        <td>{{ row.osga }}</td>
                        <td>{{ row.ospn }}</td>
                        <td>{{ row.osse }}</td>
                        <td>{{ row.ossi }}</td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

</div>
</template>

<script setup>
import { ref } from 'vue'
import useComposables from '@/js/composables'
import TblHead from '@/js/components/TblHead.vue'
import TblColGroup from '@/js/components/TblColGroup.vue'

const { axiosReq } = useComposables();

const data = ref([]);

let columns = {
    attr: {
        col: '',
        class: 'col_big'
    },
    kstt: {
        col: 'KSTT',
        class: 'col_datetime'
    },
    ostt: {
        col: 'OSTT',
        class: 'col_datetime'
    },
    osds: {
        col: 'OSDS',
        class: 'col_datetime'
    },
    osga: {
        col: 'OSGA',
        class: 'col_datetime'
    },
    ospn: {
        col: 'OSPN',
        class: 'col_datetime'
    },
    osse: {
        col: 'OSSE',
        class: 'col_datetime'
    },
    ossi: {
        col: 'OSSI',
        class: 'col_datetime'
    }
}

const getIP = () => {

    let req = () => axios.get('/api/getIP');

    let success = response => data.value = response.data;

    axiosReq(req, success);

}

getIP();
</script>

<style scoped>
td {
    height: 4rem;
}
</style>