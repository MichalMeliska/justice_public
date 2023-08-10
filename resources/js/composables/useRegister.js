import { onMounted } from 'vue'
import { useData } from '@/js/store/useData.js'
import { storeToRefs } from 'pinia'
import useComposables from '@/js/composables'

export default function useRegister() {

    const Store = useData();
    const { getRegisterVersionServer } = storeToRefs(Store);
    const { setRegisterVersionServer } = Store;

    const { axiosReq } = useComposables();

    const correctRegisterVersion = version => {

        if (version === null || getRegisterVersionServer.value === null) return null;
        else if (getRegisterVersionServer.value === version) return true;
        else return false;

    }

    const getCurrentVersion = () => {

        let req = () => axios.get('/api/getRegisterVersionServer');

        let success = response => setRegisterVersionServer(response.data);

        axiosReq(req, success);

    }

    onMounted(() => {
        if (getRegisterVersionServer.value === null) getCurrentVersion();
    });

    return { correctRegisterVersion }

}