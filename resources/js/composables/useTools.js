import { useData } from '@/js/store/useData.js'
import { useTemp } from '@/js/store/useTemp.js'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from '@/js/store'
import { storeToRefs } from 'pinia'
import useComposables from '@/js/composables'

export default function useTools() {

    const route = useRoute();
    const router = useRouter();

    const { setNotify, setPopUp } = useStore();

    const { axiosReq } = useComposables();

    const Data = useData();
    const {
        getUsers,
        getComputers,
        getServers
    } = storeToRefs(Data);
    const {
        setCompCurrentRegVersion,
        setServerOffline,
        setComputerOffline,
        setRefreshed,
        setUserAssign,
        setServerRegisterRestarted
    } = Data;

    const Temp = useTemp();
    const { getAssign } = storeToRefs(Temp);
    const {
        setAssignUser,
        setAssignComputer,
        setAssignSud
    } = Temp;

    const rdp = hostname => {

        let req = () => axios.get('/tools/rdp/' + hostname + '/' + route.name);

        axiosReq(req);

    }

    const dameware = hostname => {

        let req = () => axios.get('/tools/dameware/' + hostname);

        axiosReq(req);

    }

    const folder = hostname => {

        let req = () => axios.get('/tools/folder/' + hostname + '/' + route.name);

        axiosReq(req);

    }

    const email = address => {

        let req = () => axios.get('/tools/email/' + address);

        axiosReq(req);

    }

    const registerCopy = ComputerSID => {

        if (!confirm('Nakopírovať novú verziu?')) return;

        let req = () => axios.get('/tools/registerCopy/' + ComputerSID);

        let success = () => {

            setCompCurrentRegVersion(ComputerSID);

            setNotify('Nová verzia nakopírovaná', 'success', true);

        }

        axiosReq(req, success);

    }

    const refresh = SID => {

        let req = () => axios.get('/tools/refresh/' + route.name + '/' + SID);

        let success = response => setRefreshed(route.name, response.data);

        let err = error => {
            if (error.startsWith('[offline]')) {

                if (route.name === 'Servers') setServerOffline(SID);
                else if (route.name === 'Computers') setComputerOffline(SID);

            }
        }

        axiosReq(req, success, err);

    }

    const wol = mac => {

        let req = () => axios.get('/tools/wol/' + mac);

        axiosReq(req);

    }

    const defaultPrinter = model => {

        let req = () => axios.get('/tools/getInstalledPrinters/' + model.ComputerName);
    
        let success = response => setPopUp('PrinterDefault', {
            printers: response.data,
            UserSID: model.UserSID
        });
    
        axiosReq(req, success);
    
    }

    const assign = (SID = null) => {

        if (getAssign.value.UserSID && getAssign.value.ComputerSID) {

            let req = () => axios.post('/tools/assign', {
                userSID: getAssign.value.UserSID,
                computerSID: getAssign.value.ComputerSID
            });

            let success = response => {

                setUserAssign(response.data);

                if (route.name !== 'Users') router.push({ name: 'Users' });

                setNotify('Spárované', 'success', true);

            }

            axiosReq(req, success);

        } else {

            if (route.name === 'Users') {

                setAssignUser(SID);
                setAssignSud(getUsers.value[SID].sud);

                router.push({ name: 'Computers' });

            } else if (route.name === 'Computers') {

                setAssignComputer(SID);
                setAssignSud(getComputers.value[SID].sud);

                router.push({ name: 'Users' });

            }

        }

    }

    const regRestart = SID => {

        let req = () => axios.get('/tools/registerRestart/' + getServers.value[SID].Name);

        let success = () => {

            setServerRegisterRestarted(SID);

            setNotify('Register reštartnutý', 'success', true);

        }

        axiosReq(req, success);

    }

    return {
        rdp,
        dameware,
        folder,
        email,
        registerCopy,
        refresh,
        wol,
        defaultPrinter,
        assign,
        regRestart
    }

}