<template>
<thead>

    <tr>

        <th
            v-for="(th, key) in columns"
            @click="$emit('sorting', key)"
            :class="th.class"
            :key="key"
            :colspan="colspn(key)"
        >

            <span>{{ th.col }}</span>

            <template v-if="sortable && sort_col === key">
                <i v-show="sort_dir === 'asc'" class="fa-solid fa-arrow-down-short-wide"></i>
                <i v-show="sort_dir === 'desc'" class="fa-solid fa-arrow-down-wide-short"></i>
            </template>

        </th>

    </tr>

</thead>
</template>

<script setup>
const props = defineProps({
    columns: undefined,
    sort_col: undefined,
    sort_dir: undefined,
    colspan: undefined,
    sortable: {default: true}
});
defineEmits(['sorting']);

const colspn = key => {
    if (props.colspan && Object.keys(props.colspan).includes(key)) return props.colspan[key];
}
</script>

<style scoped>
tr {
    position: relative;
    z-index: 1;
}

th {
    border-bottom: 2px solid black;
    height: 3rem;
    padding: 0 10px;
    position: sticky;
    top: 0;
    background: #8ca0b4;
    cursor: v-bind(props.sortable ? 'pointer' : 'default');
}

i {
    margin-left: 5px;
}

.col_small {
    width: 30px;
}

.col_online {
    width: 45px;
}

.col_medium {
    width: 75px;
}

.col_datetime {
    width: 100px;
}

.col_big {
    width: 230px;
}
</style>