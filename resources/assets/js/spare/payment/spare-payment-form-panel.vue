<script>
    import Moment from 'moment';
    import OrderListModal from './spare-order-list-modal.vue';
    import ExpenseList from './spare-expense-list.vue';

    export default {
        components: {
            'expense-list': ExpenseList,
            'order-list-modal': OrderListModal,
        },
        props: {
            // Данные для поиска платежей
            searchData: {
                type: Object,
                default() {
                    return {
                        dateFrom: '',
                        dateTo: '',
                        seller: '',
                    };
                },
            },
            // Поставщики
            sellers: {
                type: Array,
                default() {
                    return {
                        default: false,
                        text: '',
                        value: 0,
                    };
                },
            },
            // Заказы
            ordersList: {
                type: Object,
                default() {
                    return {
                        0: {
                            0: {
                                date: '',
                                document: '',
                                id: 0,
                                items: [{
                                    price: '',
                                    quantity: '',
                                    spare: '',
                                }],
                                price: 0,
                            },
                        },
                    };
                },
            },
            // Список платежей
            expensesList: {
                type: Array,
                default() {
                    return [{
                        comment: '',
                        date: '',
                        id: 0,
                        inn: '',
                        money: 0,
                        orderId: 0,
                        seller: '',
                    }];
                },
            },
            // Путь для получения спска платежей
            getExpensesUri: {
                type: String,
                default: '',
            },
            // Путь для сохранения связи
            saveBindUrl: {
                type: String,
                default: '',
            },
            // Путь для удаления связи
            removeBindUrl: {
                type: String,
                default: '',
            },
            // Путь перехода на платеж
            orderUrl: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                search: this.searchData,
                expenses: this.expensesList,
                orders: this.ordersList,
                expenseForBinding: null,
                msg: null,
            };
        },
        methods: {
            getExpenses(search) {
                this.msg = null;

                const formData = new FormData();

                formData.append('dateFrom', search.dateFrom
                    ? Moment(search.dateFrom).format('YYYY-MM-DD')
                    : '');
                formData.append('dataTo', search.dateTo
                    ? Moment(search.dateTo).format('YYYY-MM-DD')
                    : '');
                formData.append('seller', search.seller ? search.seller : '');
                formData.append('name', search.name ? search.name : '');

                this.$http.post(this.getExpensesUri, formData).then((response) => {
                    if (response.body.status === 'ok') {
                        this.expenses = response.body.result;
                    }
                }, (response) => {
                    this.msg = response.body.error;
                });
            },
            startBinding(expense) {
                this.expenseForBinding = expense;
            },
            closeForm() {
                this.expenseForBinding = null;
            },
            saveBind(orderId, expenseid, deleteBind = false) {
                this.closeForm();
                this.msg = null;
                const formData = new FormData();

                formData.append('orderId', orderId);
                formData.append('expenseId', expenseid);

                let url = this.saveBindUrl;
                if (deleteBind) {
                    url = this.removeBindUrl;
                }

                this.$http.post(url, formData).then((response) => {
                    if (response.body.status === 'ok') {
                        this.msg = 'Изменения внесены';
                        this.orders = response.body.data.orders;

                        this.getExpenses(this.$refs.expenseList.search);
                    }
                }, (response) => {
                    this.msg = response.body.error;
                });
            },
        },
    };
</script>
<template>
    <div style="margin-left: 20px">
        <order-list-modal
            v-if="expenseForBinding"
            :expense-for-binding="expenseForBinding"
            :orders="orders"
            @closeForm="closeForm"
            @saveBind="saveBind"
        />

        <p
            v-if="msg"
            style="color: red"
        >
            {{ msg }}
        </p>

        <expense-list
            ref="expenseList"
            :sellers="sellers"
            :search-data="search"
            :expenses-list="expenses"
            :order-url="orderUrl"
            @getExpenses="getExpenses"
            @startBinding="startBinding"
            @saveBind="saveBind"
        />
    </div>
</template>
