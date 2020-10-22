import 'bootstrap/dist/css/bootstrap.css';
import Vue from 'vue/dist/vue';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import Notifications from 'vue-notification';
import StoragePurchaseAdd from './StoragePurchaseAdd.vue';
import 'vue-search-select/dist/VueSearchSelect.css';

Vue.use(VueMoment);
Vue.use(VueResource);
Vue.use(Notifications);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'storage-purchase-add': StoragePurchaseAdd,
        },
    });
};
