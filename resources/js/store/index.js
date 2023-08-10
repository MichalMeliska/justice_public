import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useStore = defineStore('Store', () => {

    const notify = ref({
        text: null,
        type: null,
        hide: null
    });

    const loading = ref(false);
    let loading_count = 0;

    const pop_up = ref({
        component: null,
        attr: null
    });

    const getNotify = computed(() => notify.value);
    const getLoading = computed(() => loading.value);
    const getPopUp = computed(() => pop_up.value);

    const setNotify = (text, type = 'error', hide = false) => {

        if (
            text.startsWith('[offline]') ||
            text.startsWith('[winRM]') ||
            text.startsWith('[not found]') ||
            text.startsWith('[admin]') ||
            text.startsWith('[register]')
        ) {

            let txt;

            if (text.startsWith('[offline]')) txt = 'Offline';
            else if (text.startsWith('[winRM]')) txt = 'WinRM service je nedostupný';
            else if (text.startsWith('[not found]')) txt = 'SID nenájdené';
            else if (text.startsWith('[admin]')) txt = 'Počítač je v skupine administrators';
            else if (text.startsWith('[register]')) txt = 'Register nebol reštartnutý';

            text = text.replace(/^\[.*?\]/, txt);

            hide = true;

        }

        notify.value = { text, type, hide };

    }

    const setLoading = show => {

        if (show) {
            
            loading.value = true;

            loading_count++;

        } else {

            loading_count--;

            if (loading_count === 0) loading.value = false;

        }

    }

    const setPopUp = (component, attr = {}) => {

        pop_up.value.component = component;

        pop_up.value.attr = attr;

    }

    return {
        getNotify,
        getLoading,
        getPopUp,
        setNotify,
        setLoading,
        setPopUp
    }

})