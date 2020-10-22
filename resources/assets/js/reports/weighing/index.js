import Vue from 'vue/dist/vue';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-search-select/dist/VueSearchSelect.css';
import Notifications from 'vue-notification';
import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';
import WeighingList from './weighing-list.vue';

Vue.use(VueMoment);
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
            'weighing-list': WeighingList,
        },
    });
};
