import Vue from 'vue/dist/vue';
import Vuex from 'vuex';
import BootstrapVue from 'bootstrap-vue';
import Notifications from 'vue-notification';
import VueEvents from 'vue-events';
import VueMoment from 'vue-moment';
import VueResource from 'vue-resource';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';
import ReceiptFormPanel from './spare-receipt-form-panel.vue';
import 'vue-search-select/dist/VueSearchSelect.css';

Vue.use(BootstrapVue);
Vue.use(VueEvents);
Vue.use(VueMoment);
Vue.use(VueResource);
Vue.use(Vuex);
Vue.use(Notifications);
Vue.use(VeeValidate, {
    fieldsBagName: 'formFields',
});
Validator.localize('ru', ru);

const store = new Vuex.Store({
    state: {
        apiUrl: '',
        orders: [],
        sellers: [],
        receiptItems: [],
    },
    mutations: {
        setApiUrl(state, url) {
            state.apiUrl = url;
        },
        setOrders(state, orders) {
            // Не учитывать количество по текущему заказу
            state.orders = orders.map((order) => {
                for (const orderItem of order.items) {
                    for (const receiptItem of state.receiptItems) {
                        orderItem.countReceipted = orderItem.id === receiptItem.orderItemId
                            ? (orderItem.countReceipted - receiptItem.count) : orderItem.countReceipted;
                    }
                }

                return order;
            });
        },
        setSellers(state, sellers) {
            state.sellers = sellers;
        },
        setReceiptItems(state, receiptItem) {
            state.receiptItems = receiptItem.items ? receiptItem.items : [];
        },
        updateReceiptItems(state, receiptItem) {
            const index = state.receiptItems.map((x) => x.orderItemId).indexOf(receiptItem.orderItemId);
            state.receiptItems[index] = receiptItem;
        },
        addReceiptItem(state, addableReceiptItem) {
            state.receiptItems.push({
                count: addableReceiptItem.countRestForReceipt,
                countInOrder: addableReceiptItem.countInOrder,
                countRestForReceipt: addableReceiptItem.countInOrder - addableReceiptItem.countReceipted,
                edited: false,
                isComposite: addableReceiptItem.isComposite,
                itemPrice: addableReceiptItem.itemPrice,
                nameSpare: addableReceiptItem.nameSpare,
                orderItemId: addableReceiptItem.id,
                spareUnits: addableReceiptItem.spareUnits,
                subCount: addableReceiptItem.subCount,
            });
        },
        delReceiptItem(state, itemId) {
            const index = state.receiptItems.findIndex((item) => item.orderItemId === itemId);
            if (index !== -1) {
                state.receiptItems.splice(index, 1);
            }
        },
    },
});

window.onload = () => {
    new Vue({
        store,
        components: {
            'spare-receipt-form-panel': ReceiptFormPanel,
        },
    }).$mount('#app');
};
