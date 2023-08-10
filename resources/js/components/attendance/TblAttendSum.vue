<template>
<div class="a3">

    <div class="a2">

        <i
            :class="{ hidden: !data.prev }"
            class="fa-solid fa-caret-left"
            @click="$emit('prevMonth')"
        ></i>
        <h1>{{ data.month_h }}</h1>
        <i
            :class="{ hidden: !data.next }"
            class="fa-solid fa-caret-right"
            @click="$emit('nextMonth')"
        ></i>

    </div>

    <div class="tbl_container">

        <table>

            <tbody>
                <tr v-for="val, key in dataSortedFiltered" :key="key">
                    <td class="align_left">{{ key }}</td>
                    <td :class="{ sum: key === 'Dovolenka - zostatok'}">{{ val }}</td>
                </tr>
            </tbody>

        </table>

    </div>

</div>
</template>

<script setup>
import { computed } from 'vue'
import TblHead from '@/js/components/TblHead.vue'

const props = defineProps(['data']);
defineEmits(['prevMonth', 'nextMonth']);

const dataSortedFiltered = computed(() => {

    let filtered = {};
    let dovo = {};
    let volno = {};

    for (let [key, val] of new Map(Object.entries(props.data))) {

        if (['Saldo', 'month_h', 'next', 'prev', 'month'].includes(key)) continue;

        if (key.startsWith('Dovolenka')) {

            dovo[key] = val;

            continue;

        }

        if (key.startsWith('Voľno')) {

            volno[key] = val;

            continue;

        }

        filtered[key] = val;

    }

    return {...filtered, ...volno, ...dovo};

});
</script>

<style scoped>
.a3 {
    display: flex;
    flex-direction: column;
}

.a2 {
    display: flex;
    align-items: center;
    margin: 30px 0;
}

.a2 * {
    text-align: center;
}

.a2 h1 {
    flex-grow: 2;
    font-weight: bold;
    font-size: 1.5rem;
}

.a2 i {
    flex-grow: 1;
    cursor: pointer;
    font-size: 2rem;
}

td:first-child {
    border-right: none;
    padding-right: 50px;
}

td:last-child {
    text-align: right;
}
</style>