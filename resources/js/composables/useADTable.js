import { ref, computed, watch, onActivated, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useData } from '@/js/store/useData.js'
import { useTemp } from '@/js/store/useTemp.js'
import { storeToRefs } from 'pinia'
import useComposables from '@/js/composables'
import useSortAttr from '@/js/composables/useSortAttr.js'
import TblHead from '@/js/components/TblHead.vue'
import TblColGroup from '@/js/components/TblColGroup.vue'
import PlugCheck from '@/js/components/fontAwesome/PlugCheck.vue'
import PlugXmark from '@/js/components/fontAwesome/PlugXmark.vue'
import Check from '@/js/components/fontAwesome/Check.vue'
import Xmark from '@/js/components/fontAwesome/Xmark.vue'

export default function useADTable(param) {

    const route = useRoute();
    const router = useRouter();

    const { getRefreshData } = useComposables();

    const Temp = useTemp();
    const { getAssign } = storeToRefs(Temp);
    const {
        setAssignUser,
        setAssignComputer,
        setAssignSud,
        setAssignNull
    } = Temp;

    const Data = useData();
    const {
        getInitialDataReceived,
        getUsers,
        getComputers
    } = storeToRefs(Data);
    const { setInitialDataReceived } = Data;

    const selected = ref();
    const tbl_row = ref([]);
    const categories = ref([]);

    const {
        sort_col,
        sort_dir,
        sorting
    } = useSortAttr({
        sort_col: param.sort_col,
        selected,
        tbl_row
    });

    const dataSortedFiltered = computed(() => {

        let search = param.search.value.toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');

        let filtered = Object.keys(param.data.value).reduce((f, key) => {

            if (
                (
                    (route.name !== 'Servers' && param.categories_selected.value.includes(param.data.value[key].sud)) ||
                    (route.name === 'Servers' && param.categories_selected.value.includes(param.data.value[key].categ))
                ) &&
                param.data.value[key][param.search_in].toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '').indexOf(search) > -1
            ) f[key] = param.data.value[key];

            return f;

        }, {});

        return Object.fromEntries(Object.entries(filtered).sort(([,a],[,b]) => {

            if (a.hasOwnProperty('Enabled') && a['Enabled'] !== 1) return 1;
            if (b.hasOwnProperty('Enabled') && b['Enabled'] !== 1) return -1;

            if (a[sort_col.value] === null) return 1;
            if (b[sort_col.value] === null) return -1;

            let modifier = 1;

            if (sort_dir.value === 'desc') modifier = -1;

            return (
                    (a[sort_col.value].toString().localeCompare(b[sort_col.value].toString(), 'sk-SK', {numeric: true}) * modifier) ||
                    a[param.search_in].toString().localeCompare(b[param.search_in].toString(), 'sk-SK', {numeric: true})
            );

        }));

    });

    watch(param.search, newVal => {
        if (newVal === '') selected.value = null;
    });

    watch(selected, newVal => {
        param.emits('modelSelect', param.data.value[newVal]);
    });

    watch(dataSortedFiltered, newVal => {

        if (Object.keys(newVal).length === 1) selected.value = Object.keys(newVal)[0];
        else if (!newVal.hasOwnProperty(selected.value)) selected.value = null;

        param.emits('modelSelect', param.data.value[selected.value]);

    }, {deep:true});

    onActivated(async () => {

        if (!getInitialDataReceived.value) {

            await getRefreshData();

            setInitialDataReceived(true);

        }

        if (!categories.value.length) {

            let column;

            column = route.name === 'Servers' ? 'categ' : 'sud';

            categories.value = objUniqueColumnValues(param.data.value, column);

            param.emits('setCategories', categories.value);

        }

        if (getAssign.value.sud) {

            param.emits('setCategsSelected', [getAssign.value.sud]);

            setAssignSud(null);

        }

        selectAndScroll();

    });

    const objUniqueColumnValues = (obj, column) => {

        let col = [];

        Object.keys(obj).forEach(key => {
            if (!col.includes(obj[key][column])) col.push(obj[key][column]);
        });

        return col.sort();

    }

    const selectAndScroll = async () => {

        if (route.params.sid) {

            if (Object.keys(param.data.value).length) {

                if (!param.categories_selected.value.includes(param.data.value[route.params.sid].sud)) {

                    let categories_selected = param.categories_selected.value.concat([param.data.value[route.params.sid].sud]);

                    param.emits('setCategsSelected', categories_selected);

                }

                await nextTick();

            }

            selected.value = route.params.sid;

            router.push({ name: route.name });

        }

        tbl_row.value[selected.value]?.scrollIntoView({block: 'center'});

    }

    const selectRow = (sid, redirect = false) => {

        if ((getAssign.value.UserSID || getAssign.value.ComputerSID)) {

            let str = setAssign(sid);

            if (str !== false && confirm('Spárovať: ' + str + ' ?')) param.emits('assign');
            else setAssignNull();

        }

        selected.value = (sid === selected.value) ? null : sid;

        if (
            ! redirect &&
            Object.keys(dataSortedFiltered.value).length > 2 &&
            (
                selected.value === Object.keys(dataSortedFiltered.value)[Object.keys(dataSortedFiltered.value).length - 1] ||
                selected.value === Object.keys(dataSortedFiltered.value)[Object.keys(dataSortedFiltered.value).length - 2]
            )
        ) setTimeout(() => {

            let div = document.querySelector('.tbl_container');
            div.scrollTo({top: div.scrollHeight, behavior: 'smooth'});

        }, 300);

    }

    const setAssign = sid => {

        let name1;
        let name2;

        if (getAssign.value.UserSID) {

            if (route.name === 'Computers') {
                
                setAssignComputer(sid);

                name1 = getUsers.value[getAssign.value.UserSID].UserName;
                name2 = getComputers.value[getAssign.value.ComputerSID].ComputerName;

            } else return false;

        } else {

            if (route.name === 'Users') {
                
                setAssignUser(sid);

                name1 = getComputers.value[getAssign.value.ComputerSID].ComputerName;
                name2 = getUsers.value[getAssign.value.UserSID].UserName;

            } else return false;

        }

        return name1 + ' -> ' + name2;

    }

    return {
        selected,
        sort_col,
        sort_dir,
        sorting,
        dataSortedFiltered,
        tbl_row,
        router,
        TblHead,
        TblColGroup,
        PlugCheck,
        PlugXmark,
        Check,
        Xmark,
        selectRow
    }

}