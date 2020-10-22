import 'bootstrap/dist/css/bootstrap.css';
import Vue from 'vue/dist/vue';
import VueEvents from 'vue-events';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import Notifications from 'vue-notification';
import ExportTable from './export-table.vue';

Vue.use(VueEvents);
Vue.use(VueMoment);
Vue.use(VueResource);
Vue.use(Notifications);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'export-table': ExportTable,
        },
    });
};
