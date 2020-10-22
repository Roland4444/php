import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import VueEvents from 'vue-events';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import Snotify from 'vue-snotify';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import ShipmentAddPanel from './shipment-add-panel.vue';

Vue.use(BootstrapVue);
Vue.use(VueEvents);
Vue.use(VueMoment);
Vue.use(VueResource);
Vue.use(Snotify);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'shipment-add-panel': ShipmentAddPanel,
        },
    });
};
