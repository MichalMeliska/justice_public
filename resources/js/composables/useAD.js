import { ref, onActivated, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import useTools from '@/js/composables/useTools.js'
import SearchBtns from '@/js/components/SearchBtns.vue'
import Tools from '@/js/components/Tools.vue'

export default function useAD(param) {

    const route = useRoute();

    const {
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
    } = useTools();

    const input = ref();
    const search = ref('');
    const model_selected = ref(null);
    const categories = ref([]);
    const categories_selected = ref(param.categories_selected);

    const modelSelect = model => model_selected.value = model;

    const categSelect = categ => {

        input.value.focus();

        let index = categories_selected.value.indexOf(categ);

        if (index !== -1) categories_selected.value.splice(index, 1);
        else categories_selected.value.push(categ);

    }

    const setCategsSelected = categs => categories_selected.value = categs;

    const setCategories = categs => categories.value = categs;

    onActivated(() => {

        input.value.focus();

        if (route.params.sid) search.value = '';

    });

    onMounted(() => window.addEventListener('focus', () => input.value.focus()));

    return {
        categories,
        model_selected,
        categories_selected,
        input,
        search,
        SearchBtns,
        Tools,
        rdp,
        dameware,
        folder,
        email,
        registerCopy,
        refresh,
        wol,
        defaultPrinter,
        assign,
        regRestart,
        categSelect,
        modelSelect,
        setCategsSelected,
        setCategories
    }

}