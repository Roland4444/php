import Vue from 'vue/dist/vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Notifications from 'vue-notification';
import VueResource from 'vue-resource';
import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';
import Owners from './Owners.vue';

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
        items: [],
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
        store,
        router,
        components: {
            owners: Owners,
        },
    }).$mount('#app');
};
