import Vue from 'vue/dist/vue';
import BootstrapVue from 'bootstrap-vue';
import VueEvents from 'vue-events';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import sparePlanningFormPanel from './spare-planning-form-panel.vue';
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
            'spare-planning-form-panel': sparePlanningFormPanel,
        },
    });
};
