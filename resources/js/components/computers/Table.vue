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
                v-for="computer, sid in dataSortedFiltered"
                :class="{
                    offline: !computer.online,
                    tr_selected: sid === selected
                }"
                :key="sid"
                :ref="el => tbl_row[sid] = el"
                @click="selectRow(sid)"
            >

                <td class="align_left" :class="{ old_specs: computer.old_specs }">{{ computer.ComputerName }}</td>
                <td>{{ computer.OperatingSystem }}</td>
                <td>{{ computer.OSArchitecture }}</td>
                <td>{{ computer.TotalPhysicalMemory }}</td>
                <td>{{ computer.Model }}</td>
                <td>
                    <div class="g3 center">
                        <span>{{ computer.RegisterVersion }}</span>
                        <check v-if="correctRegisterVersion(computer.RegisterVersion)"/>
                        <xmark v-else-if="correctRegisterVersion(computer.RegisterVersion) === false"/>
                    </div>
                </td>
                <td :class="{ anchor: computer.UserName }" @click.stop="redirectUser(sid, computer.UserSID)">{{ computer.UserName }}</td>
                <td>{{ computer.IP }}</td>
                <td>{{ computer.SN }}</td>
                <td>{{ computer.last_boot_up_time_h }}</td>
                <td>{{ computer.install_date_h }}</td>
                <td>{{ computer.created_h }}</td>
                <td>{{ !computer.online ? computer.last_online : null }}</td>
                <td>
                    <plug-check v-if="computer.online"/>
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
import useRegister from '@/js/composables/useRegister.js'
import columns from '@/js/components/computers/columns.js'
import useADTable from '@/js/composables/useADTable.js'

const props = defineProps(['search', 'categories_selected']);
const emits = defineEmits(['modelSelect', 'setCategories', 'setCategsSelected', 'assign']);

const { getComputers } = storeToRefs(useData());

const { correctRegisterVersion } = useRegister();

const redirectUser = (computer_sid, user_sid) => {

    if (computer_sid !== selected.value) selectRow(computer_sid, true);

    if (user_sid) router.push({ name: 'Users', params: { sid: user_sid } });

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
    Check,
    Xmark,
    selectRow
} = useADTable({
    data: getComputers,
    sort_col: 'ComputerName',
    search_in: 'ComputerName',
    search: toRef(props, 'search'),
    categories_selected: toRef(props, 'categories_selected'),
    emits
});
</script>