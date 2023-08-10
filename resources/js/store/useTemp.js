import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useTemp = defineStore('Temp', () => {

    const assign = ref({
        UserSID: null,
        ComputerSID: null,
        sud: null
    });

    const getAssign = computed(() => assign.value);

    const setAssignUser = UserSID => assign.value.UserSID = UserSID;
    const setAssignComputer = ComputerSID => assign.value.ComputerSID = ComputerSID;
    const setAssignSud = sud => assign.value.sud = sud;
    const setAssignNull = () => assign.value = {
        UserSID: null,
        ComputerSID: null,
        sud: null
    }

    return {
        getAssign,
        setAssignUser,
        setAssignComputer,
        setAssignSud,
        setAssignNull
    }

})