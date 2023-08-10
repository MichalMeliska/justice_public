<template>
<div class="center">

    <fieldset class="center tbl_container">

        <legend>Rozvrh práce</legend>

        <div class="center g5">

            <div class="a1">

                <div class="center">Rozpis:</div>

                <btn-file @fileSelected="fileSelected($event, 'rozpis')"/>

                <div class="center">Export:</div>

                <btn-file @fileSelected="fileSelected($event, 'export')"/>

            </div>

            <btn-submit @click="clickSubmit"/>

        </div>

    </fieldset>

</div>
</template>

<script setup>
import { ref } from 'vue'
import { useStore } from '@/js/store'
import useComposables from '@/js/composables'
import BtnFile from '@/js/components/buttons/File.vue'
import BtnSubmit from '@/js/components/buttons/Submit.vue'

const emit = defineEmits(['differences']);

const { setNotify } = useStore();

const { axiosReq } = useComposables();

const files = ref({
    rozpis: null,
    export: null
});

const fileSelected = (f, typ) => files.value[typ] = f;

const clickSubmit = () => {

    if (!files.value.rozpis) setNotify('Nie je vybraný rozpis', 'error', true);
    else if (!files.value.export) setNotify('Nie je vybraný export', 'error', true);
    else {

        let req = () => axios.postForm('/api/scheduleImport', {
            rozpis: files.value.rozpis,
            export: files.value.export
        });

        let success = response => {

            if (!response.data.length) setNotify('Rozvrh je OK', 'success', true);
            else emit('differences', response.data);

        };

        axiosReq(req, success);

    }

}
</script>

<style scoped>
.a1 {
    display: grid;
    grid-template-columns: auto auto;
    grid-gap: 10px;
}
</style>