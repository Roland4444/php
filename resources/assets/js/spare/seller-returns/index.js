import Vue from 'vue/dist/vue';
import VueResource from 'vue-resource';
import VeeValidate, { Validator } from 'vee-validate';
import Notifications from 'vue-notification';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-search-select/dist/VueSearchSelect.css';
import ru from 'vee-validate/dist/locale/ru';
import sellerReturnsList from './seller-returns-list.vue';

Vue.use(VueResource);
Vue.use(Notifications);
Vue.use(VeeValidate, {
    fieldsBagName: 'formFields',
});
Validator.localize('ru', ru);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'seller-returns-list': sellerReturnsList,
        },
    });
};
