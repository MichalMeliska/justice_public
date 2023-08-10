<template>
<div class="g1">

    <div class="g1 g6">

        <div class="tbl_container">

            <table>

                <tbl-col-group :columns="Object.keys(columns)" sort_col="Name"/>

                <tbl-head
                    :columns="columns"
                    :colspan="{ kanc: 3 }"
                    :sortable="false"
                />

                <tbody>
                    <tr v-for="user in data" :key="user.SID">
                        <td class="align_left">{{ user.Name }}</td>
                        <td>
                            <span :class="{ hidden: !user.old}">{{ user.old || user.new }}</span>
                        </td>
                        <td><i class="fa-solid fa-arrow-right-long"></i></td>
                        <td>
                            <span :class="{ hidden: !user.new}">{{ user.new || user.old }}</span>
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

    <tools
        :show="true"
        @submit="clickSubmit"
        @cancel="$emit('changesUpdate', {})"
    />

</div>
</template>

<script setup>
import { useData } from '@/js/store/useData.js'
import usePhonebook from '@/js/composables/usePhonebook.js'
import TblHead from '@/js/components/TblHead.vue'
import TblColGroup from '@/js/components/TblColGroup.vue'
import Tools from '@/js/components/Tools.vue'

const props = defineProps(['data']);
const emit = defineEmits(['changesUpdate']);

const { setUsers } = useData();

const { axiosReq, router, setNotify } = usePhonebook();

let columns = {
    Name: {
        col: 'Meno',
        class: 'col_big'
    },
    kanc: {
        col: 'Kanc.',
        class: 'col_big'
    }
};

const clickSubmit = () => {

    let req = () => axios.post('/api/phonebookChanges', { data: props.data });

    let success = async (response) => {

        setUsers(response.data);

        await router.push({ name: 'Users' });

        emit('changesUpdate', {});

        setNotify('Údaje aktualizované', 'success', true);

    };

    axiosReq(req, success);

}
</script>

<style scoped>
td:not(:first-child) {
    border-right: none;
}
</style>