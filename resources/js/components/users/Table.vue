<template>
<div class="tbl_container stretch">

    <table class="full_width">

        <tbl-col-group :columns="Object.keys(columns)" :sort_col="sort_col"/>

        <tbl-head
            @sorting="sorting"
            v-bind="{ columns, sort_col, sort_dir }"
        />

        <tbody>

            <tr
                v-for="user, sid in dataSortedFiltered"
                :class="{
                    offline: !user.online,
                    tr_selected: sid === selected
                }"
                :key="sid"
                :ref="el => tbl_row[sid] = el"
                @click="selectRow(sid)"
            >

                <td>{{ user.kanc }}</td>
                <td
                    class="align_left"
                    :class="{
                        disabled: !user.Enabled,
                        locked: user.LockedOut
                    }"
                >{{ user.UserName }}</td>
                <td>{{ user.sud }}</td>
                <td>{{ user.ipPhone }}</td>
                <td>{{ user.Description }}</td>
                <td>{{ user.SamAccountName }}</td>
                <td
                    :class="{
                        anchor: user.ComputerName,
                        wrong_pc: user.wrong_pc
                    }"
                    @click.stop="redirectComputer(sid, user.ComputerSID)"
                >{{ user.ComputerName }}</td>
                <td
                    :class="{
                        anchor: user.PrinterName,
                        old_specs: user.old_specs && user.PrinterName && user.Enabled
                    }"
                    @click.stop="redirectPrinter(sid, user.PrinterID)"
                >{{ user.PrinterName }}</td>
                <td>{{ user.password_last_set_h }}</td>
                <td>{{ user.password_expires_h }}</td>
                <td>{{ user.created_h }}</td>
                <td>{{ user.ms_exch_when_mailbox_created_h }}</td>
                <td>
                    <plug-check v-if="user.online"/>
                    <plug-xmark v-else/>
                </td>

            </tr>

        </tbody>

    </table>

</div>
</template>

<script setup>
import { toRef } from 'vue'
import { useData } from '@/js/store/useData.js'
import { storeToRefs } from 'pinia'
import columns from '@/js/components/users/columns.js'
import useADTable from '@/js/composables/useADTable.js'

const props = defineProps(['search', 'categories_selected']);
const emits = defineEmits(['modelSelect', 'setCategories', 'setCategsSelected', 'assign']);

const { getUsers } = storeToRefs(useData());

const redirectComputer = (user_sid, computer_sid) => {

    if (user_sid !== selected.value) selectRow(user_sid, true);

    if (computer_sid) router.push({ name: 'Computers', params: { sid: computer_sid } });

}

const redirectPrinter = (user_sid, printer_id) => {

    if (user_sid !== selected.value) selectRow(user_sid, true);

    if (printer_id) router.push({ name: 'Printers', params: { id: printer_id } });

}

const {
    selected,
    tbl_row,
    sort_col,
    sort_dir,
    sorting,
    dataSortedFiltered,
    router,
    TblHead,
    TblColGroup,
    PlugCheck,
    PlugXmark,
    selectRow
} = useADTable({
    data: getUsers,
    sort_col: 'UserName',
    search_in: 'UserName',
    search: toRef(props, 'search'),
    categories_selected: toRef(props, 'categories_selected'),
    emits
});
</script>

<style scoped>
.locked {
    background-image: linear-gradient(45deg, transparent 96%, #de270b 0);
}

.wrong_pc {
    background-image: linear-gradient(45deg, transparent 96%, rgb(229, 0, 229) 0);
}

.disabled {
    background-image: linear-gradient(45deg, transparent 96%, yellow 0);
}
</style>