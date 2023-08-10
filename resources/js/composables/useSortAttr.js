import { ref, nextTick } from 'vue'

export default function useSortAttr(param) {

    const sort_col = ref(param.sort_col);
    const sort_dir = ref('asc');

    const sorting = async (col) => {

        if (col === sort_col.value) sort_dir.value = sort_dir.value === 'asc' ? 'desc' : 'asc';
        else sort_dir.value = col === 'online' ? 'desc' : 'asc';

        sort_col.value = col;

        await nextTick();

        if (param.selected?.value) param.tbl_row.value[param.selected.value].scrollIntoView({block: 'center'});
        else document.querySelector('.tbl_container').scrollTop = 0;

    }

    return {
        sort_col,
        sort_dir,
        sorting
    }
}