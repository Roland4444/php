<script>
    import DatePicker from 'vue2-datepicker';
    import moment from 'moment';
    import { ModelSelect } from 'vue-search-select';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        components: {
            'date-picker': DatePicker,
            'model-select': ModelSelect,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            id: {
                type: Number,
                required: false,
                default: null,
            },
        },
        data() {
            return {
                waitWhileSaving: false,
                expense: {
                    date: moment().format('YYYY-MM-DD'),
                },
                customers: [],
                bankAccounts: [],
            };
        },
        computed: {
            apiUrl() {
                return this.$store.state.apiUrl;
            },

        },
        mounted() {
            if (this.id) {
                this.httpGetRequest(`${this.apiUrl}/get/${this.id}`).then((response) => {
                    const expense = response.body.data;
                    this.expense.id = expense.id;
                    this.expense.date = expense.date;
                    this.expense.customer = expense.customer.id;
                    this.expense.bank = expense.bank.id;
                    this.expense.money = expense.money;
                });
            }

            this.httpGetRequest(`${this.apiUrl}/customers`).then((response) => {
                this.customers = response.body.data;
            });

            this.httpGetRequest(`${this.apiUrl}/bank-accounts`).then((response) => {
                this.bankAccounts = response.body.data;
            });
        },
        methods: {
            save() {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.expense.date = moment(this.expense.date).format('YYYY-MM-DD');

                        this.httpPostRequest(`${this.apiUrl}/save`, this.expense).then(() => {
                            this.$router.push('/');
                        });
                    }
                });
            },
        },
    };
</script>

<template>
    <div>
        <notifications />
        <form @submit.prevent="save">
            <div class="row expense-inputs">
                <div class="col-md-2 mb-2">
                    <label for="expenseDate">
                        Дата:
                    </label>
                </div>
                <div class="col-md-4 mb-4">
                    <date-picker
                        id="expenseDate"
                        v-model="expense.date"
                        v-validate="'required'"
                        name="expenseDate"
                        input-class="form-control"
                        lang="ru"
                        type="date"
                        :first-day-of-week="1"
                        data-vv-as="дата"
                        data-vv-validate-on="change|blur"
                    />

                    <span class="validation-error-message">
                        {{ errors.first("expenseDate") }}
                    </span>
                </div>
            </div>
            <div class="row expense-inputs">
                <div class="col-md-2 mb-2">
                    <label for="customer">
                        Контрагент:
                    </label>
                </div>
                <div class="col-md-4 mb-4">
                    <model-select
                        id="customer"
                        v-model="expense.customer"
                        v-validate="'required'"
                        name="customer"
                        :options="customers"
                        data-vv-as="контрагент"
                        data-vv-validate-on="change|blur"
                    />
                    <span class="validation-error-message">
                        {{ errors.first("customer") }}
                    </span>
                </div>
            </div>
            <div class="row expense-inputs">
                <div class="col-md-2 mb-2">
                    <label for="bank">
                        Счет:
                    </label>
                </div>
                <div class="col-md-4 mb-4">
                    <model-select
                        id="bank"
                        v-model="expense.bank"
                        v-validate="'required'"
                        name="bank"
                        :options="bankAccounts"
                        data-vv-as="контрагент"
                        data-vv-validate-on="change|blur"
                    />
                    <span class="validation-error-message">
                        {{ errors.first("bank") }}
                    </span>
                </div>
            </div>
            <div class="row expense-inputs">
                <div class="col-md-2 mb-2">
                    <label for="money">
                        Сумма:
                    </label>
                </div>
                <div class="col-md-4 mb-4">
                    <input
                        id="money"
                        v-model="expense.money"
                        v-validate="'required'"
                        name="money"
                        type="number"
                        step="0.01"
                        min="0"
                        data-vv-as="сумма"
                        data-vv-validate-on="change|blur"
                        class="form-control"
                    >
                    <span class="validation-error-message">
                        {{ errors.first("money") }}
                    </span>
                </div>
            </div>
            <hr>
            <div>
                <input
                    type="submit"
                    class="btn btn-primary"
                    :disabled="waitWhileSaving"
                    value="Сохранить"
                >
                <router-link
                    class="btn btn-default"
                    to="/"
                >
                    Отмена
                </router-link>
            </div>
        </form>
    </div>
</template>
<style>
    .expense-inputs {
        margin-bottom: 20px;
    }
    .validation-error-message {
        color: #ff0000;
        font-size: 10pt
    }
</style>
