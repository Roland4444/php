import Vue from 'vue/dist/vue';
import BootstrapVue from 'bootstrap-vue';
import Notifications from 'vue-notification';
import VueEvents from 'vue-events';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import spareInventoryFormPanel from './spare-inventory-form-panel.vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-search-select/dist/VueSearchSelect.css';

Vue.use(BootstrapVue);
Vue.use(VueEvents);
Vue.use(VueMoment);
Vue.use(VueResource);
Vue.use(Notifications);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'spare-inventory-form-panel': spareInventoryFormPanel,
        },
    });
};
