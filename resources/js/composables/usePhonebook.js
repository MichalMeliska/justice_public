import useComposables from '@/js/composables'
import { useRouter } from 'vue-router'
import { useStore } from '@/js/store'

export default function usePhonebook() {

    const router = useRouter();

    const { setNotify } = useStore();

    const { axiosReq } = useComposables();

    return {
        axiosReq,
        router,
        setNotify
    }

}