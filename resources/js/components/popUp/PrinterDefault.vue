<template>
<div>
    <div
        v-for="printer in attr.printers"
        @click="select(printer)"
        class="component_tool"
    >{{ printer }}</div>
</div>
</template>

<script setup>
import { useData } from '@/js/store/useData.js'
import { useStore } from '@/js/store'
import useComposables from '@/js/composables'

const props = defineProps(['attr']);

const { axiosReq } = useComposables();

const { setNotify, setPopUp } = useStore();

const { setUserPrinter } = useData();

const select = name => {

    let req = () => axios.post('/tools/setDeafultPrinter', {
        UserSID: props.attr.UserSID,
        printer: name
    });

    let success = response => {

        setPopUp(null);

        setUserPrinter(props.attr.UserSID, response.data.printerID, response.data.printerName);

        setNotify('Tlačiareň predvolená', 'success', true);

    }

    axiosReq(req, success);

}
</script>

<style scoped>
.component_tool {
    text-align: center;
}
</style>