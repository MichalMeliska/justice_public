<template>
<div>

    <div class="g1" v-if="view">

        <div class="g1">

            <div class="a1">

                <tbl-attendance :data="data.mesiac" :saldo="data.sumar.Saldo"/>

                <tbl-attend-sum
                    :data="data.sumar"
                    @prevMonth="getAttendance(false, data.sumar.month, -1)"
                    @nextMonth="getAttendance(false, data.sumar.month, 1)"
                />

            </div>

        </div>

        <tools
            :show="view"
            :refresh="current_month"
            @logout="logoutAtt"
            @refresh="getAttendance(true)"
        />

    </div>

    <login
        v-else-if="view === false"
        :email="email"
        @login="loginAtt"
    />

</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import useComposables from '@/js/composables'
import Login from '@/js/components/attendance/Login.vue'
import TblAttendance from '@/js/components/attendance/TblAttendance.vue'
import TblAttendSum from '@/js/components/attendance/TblAttendSum.vue'
import Tools from '@/js/components/Tools.vue'

const email = ref();
const data = ref({});
const view = ref(null);

const { axiosReq } = useComposables();

const current_month = computed(() => {

    const d = new Date();

    return data.value.sumar?.month === d.getFullYear() + '-' + (d.getMonth() + 1).toString().padStart(2, '0');

});

const loginAtt = obj => getAttendance(false, null, null, obj.email, obj.password, obj.remember);

const logoutAtt = () => {

    let req = () => axios.get('/attendance/logout');

    let success = () => {
        
        view.value = false;

        email.value = null;

    }

    axiosReq(req, success);

}

const getAttendance = async (force = false, cur_month = null, change_month = null, mail = null, password = null, remember = false) => {

    let req = () => axios.post('/attendance', {
        mail,
        password,
        remember,
        force,
        cur_month,
        change_month
    });

    let success = response => {

        view.value = false;

        if (response.data.hasOwnProperty('email')) email.value = response.data.email;
        else if (response.data.hasOwnProperty('attendance')) {

            data.value = response.data.attendance;

            view.value = true;

        }

    }

    await axiosReq(req, success);

}

onMounted(async () => await getAttendance());
</script>

<style scoped>
.a1 {
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    overflow: hidden;
    padding: 10px;
}

:deep(.sum) {
    font-size: 1.4rem;
    font-weight: bold;
}
</style>