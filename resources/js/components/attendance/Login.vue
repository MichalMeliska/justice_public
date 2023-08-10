<template>
<div class="center">

    <fieldset class="center tbl_container">

        <legend>Login</legend>

        <div class="center g5">

            <div class="a1">
            
                <input
                    type="email"
                    class="txt_input"
                    autocomplete="off"
                    placeholder="E-mail"
                    ref="inp_email"
                    v-model="email"
                    @keyup.enter="clickSubmit"
                />

                <input
                    type="password"
                    class="txt_input"
                    autocomplete="off"
                    placeholder="Heslo do Humanetu"
                    ref="inp_pass"
                    v-model="password"
                    @keyup.enter="clickSubmit"
                />

                <div class="center">

                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        v-model="remember"
                    />

                    <label for="remember">Zapamätať</label>

                </div>

            </div>

            <btn-submit @click="clickSubmit"/>

        </div>

    </fieldset>

</div>
</template>

<script setup>
import { ref, onActivated, onMounted } from 'vue'
import { useStore } from '@/js/store'
import BtnSubmit from '@/js/components/buttons/Submit.vue'

const props = defineProps(['email']);
const emit = defineEmits(['login']);

const { setNotify } = useStore();

const inp_email = ref();
const inp_pass = ref();
const email = ref(props.email);
const password = ref();
const remember = ref(true);

const clickSubmit = () => {

    if (!email.value) {

        setNotify('Zadaj email.', 'error', true);

        inp_email.value.focus();

    } else if (!password.value) {

        setNotify('Zadaj heslo.', 'error', true);

        inp_pass.value.focus();

    } else emit('login', {
        email: email.value,
        password: password.value,
        remember: remember.value
    });

}

const inputFocus = () => {

    if (email.value) inp_pass.value.focus();
    else inp_email.value.focus();

}

onActivated(() => inputFocus());

onMounted(() => {

    inputFocus();

    window.addEventListener('focus', () => inputFocus());

});
</script>

<style scoped>
.a1 {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
</style>