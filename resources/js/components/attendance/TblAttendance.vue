<template>
<div class="tbl_container">

    <table>

        <tbl-head
            :columns="columns"
            :sortable="false"
        />

        <tbody>
            <tr v-for="day in data" :key="day.date">
                <td :class="{ volno: day.volno }">{{ day.date }}</td>
                <td>{{ day.oddo }}</td>
                <td>{{ day.prerusenie }}</td>
                <td>{{ day.trvanie }}</td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td class="sum">{{ saldo }}</td>
            </tr>
        </tfoot>
        
    </table>

</div>
</template>

<script setup>
import TblHead from '@/js/components/TblHead.vue'

defineProps(['data', 'saldo']);

let columns = {
    date: {
        col: 'Deň',
        class: 'col_medium'
    },
    oddo: {
        col: 'Od - do',
        class: 'col_datetime'
    },
    prerusenie: {
        col: 'Prerušenie',
        class: 'col_medium'
    },
    trvanie: {
        col: 'Trvanie',
        class: 'col_medium'
    }
};
</script>

<style scoped>
.volno {
    color: #C80000;
}

td {
    height: 1.6rem;
}

tfoot td {
    border-right: none;
}
</style>