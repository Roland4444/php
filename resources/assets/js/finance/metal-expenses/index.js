import Vue from 'vue/dist/vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-search-select/dist/VueSearchSelect.css';
import Notifications from 'vue-notification';
import VueResource from 'vue-resource';
import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';
import MetalExpenses from './MetalExpenses.vue';

import routes from './routes';

Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(Notifications);
Vue.use(VueResource);
Vue.use(VeeValidate, {
    fieldsBagName: 'formFields',
});
Validator.localize('ru', ru);

const router = new VueRouter({ routes });

const store = new Vuex.Store({
    state: {
        apiUrl: '',
        permissions: {},
    },
    mutations: {
        setApiUrl(state, url) {
            state.apiUrl = url;
        },
        setPermissions(state, permissions) {
            state.permissions = permissions;
        },
    },
});

window.onload = () => {
    new Vue({
        store,
        router,
        components: {
            'metal-expenses': MetalExpenses,
        },
    }).$mount('#app');
};
