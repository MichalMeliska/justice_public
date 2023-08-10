<template>
<transition>

    <div v-show="Boolean(props.model) || show" id="tools">

        <img
            alt=""
            :class="{ disabled: !condition.pc }"
            :src="img_rdp"
            title="RDP"
            v-show="showTool('rdp')"
            @click="emitEvent($event.target.className, 'rdp')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.pc }" 
            :src="img_dameware"
            title="Dameware"
            v-show="showTool('dameware')"
            @click="emitEvent($event.target.className, 'dameware')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.pc }"
            :src="img_folder"
            title="C:\"
            v-show="showTool('folder')"
            @click="emitEvent($event.target.className, 'folder')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.mail }"
            :src="img_email"
            title="Email"
            v-show="showTool('email')"
            @click="emitEvent($event.target.className, 'email')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.register }"
            :src="img_register"
            title="Copy Register.exe"
            v-show="showTool('registerCopy')"
            @click="emitEvent($event.target.className, 'registerCopy')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.refresh }"
            :src="img_refresh"
            title="Refresh"
            v-show="showTool('refresh')"
            @click="emitEvent($event.target.className, 'refresh')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.wol }"
            :src="img_wol"
            title="Wake on LAN"
            v-show="showTool('wol')"
            @click="emitEvent($event.target.className, 'wol')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.pc }"
            :src="img_printer"
            title="Predvoliť tlačiareň"
            v-show="showTool('printer')"
            @click="emitEvent($event.target.className, 'printer')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.assign }"
            :src="img_assign"
            title="Spárovať"
            v-show="showTool('assign')"
            @click="emitEvent($event.target.className, 'assign')"
        />

        <img
            alt=""
            :class="{ disabled: !condition.regRestart }"
            :src="img_regRestart"
            title="Register restart"
            v-show="showTool('regRestart')"
            @click="emitEvent($event.target.className, 'regRestart')"
        />

        <btn-submit
            :class="{ disabled: !condition.submit }"
            @click="$emit('submit')"
            v-show="showTool('submit')"
        />

        <btn-cancel
            @click="$emit('cancel')"
            v-show="showTool('cancel')"
        />

        <btn-logout
            @click="$emit('logout')"
            v-show="showTool('logout')"
        />

    </div>

</transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useData } from '@/js/store/useData.js'
import { storeToRefs } from 'pinia'
import img_rdp from '@/assets/tools/rdp.png'
import img_dameware from '@/assets/tools/dameware.png'
import img_folder from '@/assets/tools/folder.png'
import img_email from '@/assets/tools/email.png'
import img_refresh from '@/assets/tools/refresh.png'
import img_printer from '@/assets/tools/printer.png'
import img_wol from '@/assets/tools/wol.png'
import img_register from '@/assets/tools/register.png'
import img_regRestart from '@/assets/tools/regRestart.png'
import img_assign from '@/assets/tools/assign.png'
import BtnCancel from '@/js/components/buttons/Cancel.vue'
import BtnSubmit from '@/js/components/buttons/Submit.vue'
import BtnLogout from '@/js/components/buttons/Logout.vue'

const props = defineProps({
    model: undefined,
    show: undefined,
    refresh: {default: true}
});
const emit = defineEmits([
    'rdp',
    'dameware',
    'folder',
    'email',
    'registerCopy',
    'cancel',
    'submit',
    'logout',
    'refresh',
    'wol',
    'printer',
    'assign',
    'regRestart'
]);

let user_type = user;

const route = useRoute();

const { getRegisterVersionServer } = storeToRefs(useData());

const model = ref();

const tools = {
    Users: ['rdp', 'dameware', 'folder', 'email', 'refresh', 'printer', 'assign'],
    Computers: ['rdp', 'dameware', 'folder', 'refresh', 'wol', 'registerCopy', 'assign'],
    Servers: ['rdp', 'folder', 'refresh', 'regRestart'],
    Phonebook: ['submit', 'cancel'],
    Attendance: ['logout', 'refresh'],
    Schedule: ['cancel']
}

const condition = computed(() => {
    return {
        pc: localhost && model.value?.ComputerName && model.value?.online && !model.value?.IT,
        mail: localhost && model.value?.Enabled,
        register: user_type !== 'user' && model.value?.online && getRegisterVersionServer.value !== model.value?.RegisterVersion && !model.value?.IT,
        refresh: user_type !== 'user' && props.refresh,
        wol: user_type !== 'user' && !model.value?.online,
        submit: user_type === 'owner',
        printer: user_type !== 'user' && model.value?.ComputerName && model.value?.online && !model.value?.IT,
        assign: user_type === 'owner',
        regRestart: user_type !== 'user' && model.value?.RegisterLog !== null
    }
});

watch(() => props.model, newVal => {
    if (newVal) model.value = newVal;
});

const showTool = tool => tools[route.name]?.includes(tool);

const emitEvent = (cl, ev) => {
    if (cl !== 'disabled') emit(ev);
}
</script>

<style scoped>
#tools {
    background: var(--nav_bg);
    display: flex;
    justify-content: center;
    gap: 30px;
}

.v-enter-from, .v-leave-to {
    height: 0px;
}

.v-enter-to, .v-leave-from {
    height: 70px;
}

.v-enter-active, .v-leave-active {
    transition: .4s;
}

img {
    height: 30px;
    padding: 5px 60px;
    display: block;
}

img:not(.disabled):active, button:active {
    border-style: inset;
}

img, button {
    margin: 15px 0;
    border: 1px outset transparent;
}

img:not(.disabled), button {
    border-color: #5c646c;
    cursor: pointer;
}

button {
    height: 42px;
    background: transparent;
    border-radius: 0;
}

button:hover {
    background: transparent;
}

.disabled {
    opacity: .2;
}
</style>