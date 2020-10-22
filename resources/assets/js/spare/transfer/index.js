import Vue from 'vue/dist/vue';
import BootstrapVue from 'bootstrap-vue';
import VueEvents from 'vue-events';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-search-select/dist/VueSearchSelect.css';
import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';
import spareTransferFormPanel from './spare-transfer-form-panel.vue';

Vue.use(BootstrapVue);
Vue.use(VueEvents);
Vue.use(VueMoment);
Vue.use(VueResource);
Vue.use(VeeValidate, {
    fieldsBagName: 'formFields',
});
Validator.localize('ru', ru);

window.onload = () => {
    new Vue({ // eslint-disable-line no-new
        el: '#app',
        components: {
            'spare-transfer-form-panel': spareTransferFormPanel,
        },
    });
};
