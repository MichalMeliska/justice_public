import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useData = defineStore('Data', () => {

    const initial_data_received = ref(false);
    const users = ref({});
    const computers = ref({});
    const servers = ref({});
    const register_version_server = ref(null);

    const getInitialDataReceived = computed(() => initial_data_received.value);
    const getUsers = computed(() => users.value);
    const getComputers = computed(() => computers.value);
    const getServers = computed(() => servers.value);
    const getRegisterVersionServer = computed(() => register_version_server.value);

    const setInitialDataReceived = data => initial_data_received.value = data;
    const setUsers = data => users.value = data;
    const setComputers = data => computers.value = data;
    const setServers = data => servers.value = data;
    const setRegisterVersionServer = version => register_version_server.value = version;

    const setCompCurrentRegVersion = computerSID => computers.value[computerSID].RegisterVersion = getRegisterVersionServer.value;
    const setComputerOffline = SID => computers.value[SID].online = false;
    const setServerOffline = SID => servers.value[SID].online = false;
    const setUserPrinter = (userSID, printerID, printerName) => {

        users.value[userSID].PrinterID = printerID;
        users.value[userSID].PrinterName = printerName;

    }

    const setUserAssign = data => users.value[data.UserSID] = { ...users.value[data.UserSID], ...data };

    const setRefreshed = (route, data) => {

        if (route === 'Servers') servers.value[data.SID] = { ...servers.value[data.SID], ...data };
        else if (route === 'Users') users.value[data.UserSID] = { ...users.value[data.UserSID], ...data };
        else if (route === 'Computers') {

            computers.value[data.computer.SID] = { ...computers.value[data.computer.SID], ...data.computer };

            data.users.forEach(userSID => {

                users.value[userSID].ComputerName = data.computer.ComputerName;
                users.value[userSID].PrinterName = data.computer.PrinterName;
                users.value[userSID].PrinterID = data.computer.PrinterID;

            });

        }

    }

    const setServerRegisterRestarted = SID => {

        servers.value[SID].RegisterRunning = 1;
        servers.value[SID].TaskSchedulerRunning = 1;
        servers.value[SID].RegisterLog = 0;

    }

    return {
        getInitialDataReceived,
        getUsers,
        getComputers,
        getServers,
        getRegisterVersionServer,
        setInitialDataReceived,
        setUsers,
        setComputers,
        setServers,
        setRegisterVersionServer,
        setCompCurrentRegVersion,
        setComputerOffline,
        setServerOffline,
        setRefreshed,
        setUserPrinter,
        setUserAssign,
        setServerRegisterRestarted
    }

})