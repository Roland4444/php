import Vue from 'vue/dist/vue';
import Notifications from 'vue-notification';
import VueResource from 'vue-resource';
import ExpenseLimits from './expense-limits.vue';

import 'bootstrap/dist/css/bootstrap.css';

Vue.use(Notifications);
Vue.use(VueResource);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'expense-limits': ExpenseLimits,
        },
    });
};
