import Vue from 'vue/dist/vue';
import BootstrapVue from 'bootstrap-vue';
import VueEvents from 'vue-events';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import OrderFormPanel from './spare-order-form-panel.vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-search-select/dist/VueSearchSelect.css';

Vue.use(BootstrapVue);
Vue.use(VueEvents);
Vue.use(VueMoment);
Vue.use(VueResource);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'spare-order-form-panel': OrderFormPanel,
        },
    });
};
