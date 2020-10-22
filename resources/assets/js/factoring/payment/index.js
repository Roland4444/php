import Vue from 'vue/dist/vue';
import Notifications from 'vue-notification';
import VueResource from 'vue-resource';
import PaymentList from './payment-list.vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

Vue.use(VueResource);
Vue.use(Notifications);

window.onload = () => {
    new Vue({
        components: {
            'payment-list': PaymentList,
        },
    }).$mount('#app');
};
