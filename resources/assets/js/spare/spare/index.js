import Vue from 'vue/dist/vue';
import VueResource from 'vue-resource';
import SpareForm from './spare-form.vue';

Vue.use(VueResource);

window.onload = () => {
    new Vue({
        el: '#app',
        components: {
            SpareForm,
        },
    });
};
