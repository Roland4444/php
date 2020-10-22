import Vue from 'vue/dist/vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-search-select/dist/VueSearchSelect.css';
import VueGoodWizard from 'vue-good-wizard';
import Notifications from 'vue-notification';
import VueResource from 'vue-resource';
import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';
import Vuex from 'vuex';
import ShipmentDoc from './shipmentdoc-add.vue';

Vue.use(VueGoodWizard);
Vue.use(Notifications);
Vue.use(VueResource);
Vue.use(VeeValidate, {
    fieldsBagName: 'formFields',
});
Validator.localize('ru', ru);
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        apiUrl: '',
    },
    mutations: {
        setApiUrl(state, url) {
            state.apiUrl = url;
        },
    },
});

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'shipmentdoc-add': ShipmentDoc,
        },
        store,
    });
};
