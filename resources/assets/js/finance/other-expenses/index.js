import 'bootstrap/dist/css/bootstrap.css';
import Vue from 'vue/dist/vue';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import Notifications from 'vue-notification';
import FinanceOtherExpenses from './finance_other_expenses.vue';
import 'vue-search-select/dist/VueSearchSelect.css';

Vue.use(VueMoment);
Vue.use(VueResource);
Vue.use(Notifications);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'finance-other-expenses': FinanceOtherExpenses,
        },
    });
};
