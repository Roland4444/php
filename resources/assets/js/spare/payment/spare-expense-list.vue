<script>
    import DatePicker from 'vue2-datepicker';
    import { ModelSelect } from 'vue-search-select';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    export default {
        components: {
            'confirmation-modal': ConfirmationModal,
            'date-picker': DatePicker,
            'model-select': ModelSelect,
        },
        props: {
            sellers: {
                type: Array,
                default() {
                    return [{
                        default: false,
                        text: '',
                        value: 0,
                    }];
                },
            },
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
            expensesList: {
                type: Array,
                default() {
                    return [{
                        comment: '',
                        date: '',
                        id: 0,
                        inn: '',
                        money: 0,
                        orderId: null,
                        seller: '',
                    }];
                },
            },
            orderUrl: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                search: this.searchData,
                sellerOptions: this.sellers ? Object.values(this.sellers) : null,
                saveBindingInfo: {
                    orderId: 0,
                    expenseId: 0,
                },
            };
        },
        methods: {
            getExpenses() {
                this.$emit('getExpenses', this.search);
            },
            startBinding(expense) {
                this.$emit('startBinding', expense);
            },
            saveBind() {
                this.$emit('saveBind', this.saveBindingInfo.orderId, this.saveBindingInfo.expenseId, true);
            },
            defineSaveBindingInfo(orderId, expenseId) {
                this.saveBindingInfo.orderId = orderId;
                this.saveBindingInfo.expenseId = expenseId;
            },
            toCurrency(value) {
                const formatter = new Intl.NumberFormat('ru', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 0,
                });
                return formatter.format(value);
            },
            getOrderUrl(orderId) {
                return `${this.orderUrl}/${orderId}`;
            },
            setColorLine(hasExpenseOrderId) {
                if (!hasExpenseOrderId) {
                    return {
                        background: '#ff57141f',
                    };
                }

                return false;
            },
        },
    };
</script>
<template>
    <div>
        <div>
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <div class="form-group">
                        <date-picker
                            v-model="search.dateFrom"
                            input-class="form-control"
                            lang="ru"
                            :first-day-of-week="1"
                        />
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <div class="form-group">
                        <date-picker
                            v-model="search.dateTo"
                            input-class="form-control"
                            lang="ru"
                            :first-day-of-week="1"
                        />
                    </div>
                </div>
                <div
                    v-if="sellers"
                    class="col-lg-3 col-md-3 col-sm-6"
                >
                    <div class="form-group">
                        <div class="searchMenu">
                            <model-select
                                id="seller"
                                v-model="search.seller"
                                :options="sellerOptions"
                                placeholder="Поставщик"
                            />
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <div class="form-group">
                        <div class="searchMenu">
                            <input
                                id="name"
                                v-model="search.name"
                                class="form-control"
                                type="text"
                                autocomplete="off"
                                placeholder="Назначение"
                            >
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12">
                    <div class="form-group">
                        <span
                            class="btn btn-default"
                            style="width: 100%;"
                            @click="getExpenses()"
                        >
                            Фильтр
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped table-bordered main-table">
            <thead>
                <tr>
                    <th>
                        Дата
                    </th>
                    <th
                        v-if="sellers"
                    >
                        Поставщик
                    </th>
                    <th>
                        Сумма
                    </th>
                    <th>
                        Назначение платежа
                    </th>
                    <th>
                        Номер заказа
                    </th>
                    <th>
                        Привязать к заказу
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="expense in expensesList"
                    :key="expense.id"
                    :style="setColorLine(expense.orderId)"
                >
                    <td>{{ expense.date }}</td>
                    <td>{{ expense.seller }}</td>
                    <td>{{ toCurrency(expense.money) }}</td>
                    <td>{{ expense.comment }}</td>
                    <td>{{ expense.orderId }}</td>
                    <td style="text-align: center;">
                        <span
                            v-if="! expense.orderId"
                            class="forClick glyphicon glyphicon-paperclip"
                            @click="startBinding(expense)"
                        />
                        <div v-else>
                            <a
                                class="forClick"
                                target="_blank"
                                :href="getOrderUrl(expense.orderId)"
                            >Заказ</a> |
                            <span
                                class="forClick"
                                data-toggle="modal"
                                data-target="#confirmationModal"
                                @click="defineSaveBindingInfo(expense.orderId, expense.id)"
                            >Отвязать</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <confirmation-modal
            @onConfirmation="saveBind"
        >
            <span>Вы подтверждаете действие?</span>
        </confirmation-modal>
    </div>
</template>
