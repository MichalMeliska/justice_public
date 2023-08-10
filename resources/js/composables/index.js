import { useStore } from '@/js/store'
import { useData } from '@/js/store/useData.js'

export default function useComposables() {

    const { setNotify, setLoading } = useStore();

    const { setUsers, setComputers, setServers } = useData();

    const axiosReq = async (req, success = () => {}, err = () => {}) => {

        setLoading(true);

        await req()
            .then(response => {

                if (response.data.hasOwnProperty('error')) {

                    setNotify(response.data.error);

                    err(response.data.error);

                } else success(response);

            })
            .catch(error => setNotify(error.response.data.message + ' in ' + error.response.data.file + ', line: ' + error.response.data.line))
            .finally(() => setLoading(false));

    }

    const getRefreshData = async () => {

        let req = () => axios.get('/api/getRefreshData');

        let success = response => {

            setUsers(response.data.users);
            setComputers(response.data.computers);
            setServers(response.data.servers);

        }

        await axiosReq(req, success);

    }

    return {
        axiosReq,
        getRefreshData
    }

}