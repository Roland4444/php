import Vue from 'vue/dist/vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Notifications from 'vue-notification';
import VueResource from 'vue-resource';
import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';
import ReceiverForm from './receiver-form.vue';

Vue.use(Notifications);
Vue.use(VueResource);
Vue.use(VeeValidate, {
    fieldsBagName: 'formFields',
});
Validator.localize('ru', ru);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            'receiver-form': ReceiverForm,
        },
    });
};
