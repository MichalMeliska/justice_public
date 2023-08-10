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
                v-for="server, sid in dataSortedFiltered"
                :class="{
                    offline: !server.online,
                    tr_selected: sid === selected
                }"
                :key="sid"
                :ref="el => tbl_row[sid] = el"
                @click="selectRow(sid)"
            >

                <td class="align_left" :class="{ old_specs: server.old_specs }">{{ server.Name }}</td>
                <td>{{ server.OperatingSystem }}</td>
                <td>{{ server.OSArchitecture }}</td>
                <td>{{ server.TotalPhysicalMemory }}</td>
                <td>{{ server.Model }}</td>
                <td>
                    <div class="g3 center">
                        <span>{{ server.RegisterVersion }}</span>
                        <check v-if="correctRegisterVersion(server.RegisterVersion)"/>
                        <xmark v-else-if="correctRegisterVersion(server.RegisterVersion) === false"/>
                    </div>
                </td>
                <td>
                    <check v-if="server.RegisterRunning === 1"/>
                    <xmark v-else-if="server.RegisterRunning === 0"/>
                </td>
                <td>
                    <check v-if="server.TaskSchedulerRunning === 1"/>
                    <xmark v-else-if="server.TaskSchedulerRunning === 0"/>
                </td>
                <td>
                    <check v-if="server.RegisterLog !== null && server.RegisterLog < 15"/>
                    <div class="g3 center" v-else-if="server.RegisterLog !== null">
                        <span>{{ server.RegisterLog }}min</span>
                        <xmark/>
                    </div>
                </td>
                <td>{{ server.SN }}</td>
                <td>{{ server.last_boot_up_time_h }}</td>
                <td>{{ server.install_date_h }}</td>
                <td>{{ server.created_h }}</td>
                <td>{{ !server.online ? server.last_online : null }}</td>
                <td>
                    <plug-check v-if="server.online"/>
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
import columns from '@/js/components/servers/columns.js'
import useADTable from '@/js/composables/useADTable.js'

const props = defineProps(['search', 'categories_selected']);
const emits = defineEmits(['modelSelect', 'setCategories', 'setCategsSelected']);

const { getServers } = storeToRefs(useData());

const { correctRegisterVersion } = useRegister();

const {
    selected,
    tbl_row,
    sort_col,
    sort_dir,
    sorting,
    dataSortedFiltered,
    TblHead,
    TblColGroup,
    PlugCheck,
    PlugXmark,
    Check,
    Xmark,
    selectRow
} = useADTable({
    data: getServers,
    sort_col: 'Name',
    search_in: 'Name',
    search: toRef(props, 'search'),
    categories_selected: toRef(props, 'categories_selected'),
    emits
});
</script>