<template>
<div class="center">

    <fieldset class="center tbl_container">

        <legend>Telefónny zoznam</legend>

        <div class="center g5">

            <btn-file @fileSelected="fileSelected"/>

            <div class="g7">

                <div class="g8">
                    <input type="radio" name="sud" value="KSTT" id="a2" v-model="sud">
                    <label for="a2">KSTT</label>
                </div>

                <div class="g8">
                    <input type="radio" name="sud" value="OSTT" id="a3" v-model="sud">
                    <label for="a3">OSTT</label>
                </div>

            </div>

            <div class="a4">
                <btn-submit @click="clickSubmit"/>
                <btn-cancel @click="clickCancel"/>
            </div>

        </div>

    </fieldset>

</div>
</template>

<script setup>
import { ref } from 'vue'
import usePhonebook from '@/js/composables/usePhonebook.js'
import BtnFile from '@/js/components/buttons/File.vue'
import BtnCancel from '@/js/components/buttons/Cancel.vue'
import BtnSubmit from '@/js/components/buttons/Submit.vue'

const emit = defineEmits(['changesUpdate']);

const file = ref(false);
const sud = ref(false);

const { axiosReq, router, setNotify } = usePhonebook();

const fileSelected = f => file.value = f;

const clickCancel = () => router.push({ name: 'Users' });

const clickSubmit = () => {

    if (!file.value) setNotify('Nie je vybraný žiadny súbor', 'error', true);
    else if (!sud.value) setNotify('Nie je vybraný súd', 'error', true);
    else {

        let req = () => axios.postForm('/api/phonebookImport', {
            file: file.value,
            sud: sud.value
        });

        let success = response => {

            if (!response.data.length) setNotify('Údaje sú aktuálne', 'success', true);
            else emit('changesUpdate', response.data);

        };

        axiosReq(req, success);

    }

}
</script>

<style scoped>
.a4 {
    display: flex;
    gap: 10px;
}
</style>