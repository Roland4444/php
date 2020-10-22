<script>
    import { ModelSelect } from 'vue-search-select';
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    const date = new Date();

    export default {
        components: {
            DatePicker,
            ModelSelect,
            ConfirmationModal,
        },
        mixins: [httpRequestWrapperMixin],
        data() {
            return {
                items: [],
                sum: 0,
                filter: {
                    dateFrom: new Date(date.getFullYear(), date.getMonth(), 1),
                    dateTo: new Date(date.getFullYear(), date.getMonth() + 1, 0),
                    customer: 0,
                    account: 0,
                },
                customers: [],
                bankAccounts: [],
                momentPlugin: Moment,
                selectedItem: {},
            };
        },
        computed: {
            apiUrl() {
                return this.$store.state.apiUrl;
            },
            permissions() {
                return this.$store.state.permissions;
            },
        },
        created() {
            this.refreshList();
            this.initCustomersSelect();
            this.initBankAccountsSelect();
        },
        methods: {
            refreshList() {
                const dateFrom = this.momentPlugin(this.filter.dateFrom).format('YYYY-MM-DD');
                const dateTo = this.momentPlugin(this.filter.dateTo).format('YYYY-MM-DD');

                const url = `${this.apiUrl}/list?date_from=${dateFrom}&date_to=${dateTo}
                &customer=${this.filter.customer}&account=${this.filter.account}`;

                this.httpGetRequest(url).then((response) => {
                    this.items = response.body.data;
                    this.sum = response.body.sum;
                });
            },
            selectItem(item) {
                this.selectedItem = item;
            },
            deleteSelectedItem() {
                if (!this.selectedItem.id) {
                    return;
                }
                const formData = new FormData();
                formData.append('id', this.selectedItem.id);
                this.$http.post(`${this.apiUrl}/delete`, formData).then(() => {
                    this.$notify({
                        title: 'Выполнено',
                        text: 'Запись удалена',
                    });
                    this.refreshList();
                }, (response) => {
                    this.showError(response.body.error);
                });
            },
            showError(msg) {
                this.$notify({
                    title: 'Ошибка',
                    text: msg,
                    type: 'error',
                });
            },
            initCustomersSelect() {
                this.httpGetRequest(`${this.apiUrl}/customers`).then((response) => {
                    this.customers = response.body.data;
                    this.customers.unshift({
                        id: 0,
                        text: 'Выбрать поставшика',
                        value: 0,
                    });
                });
            },
            initBankAccountsSelect() {
                this.httpGetRequest(`${this.apiUrl}/bank-accounts`).then((response) => {
                    this.bankAccounts = response.body.data;
                    this.bankAccounts.unshift({
                        id: 0,
                        text: 'Выбрать счет',
                        value: 0,
                    });
                });
            },
        },
    };
</script>

<template>
    <div>
        <notifications />

        <confirmation-modal @onConfirmation="deleteSelectedItem">
            <span>Удалить запись?</span>
        </confirmation-modal>

        <div class="filter">
            <div
                id="filter"
                class="form-inline form-filter ng-pristine ng-valid"
            >
                <div style="display: inline-block;">
                    <date-picker
                        v-model="filter.dateFrom"
                        input-class="form-control"
                        format="YYYY-MM-DD"
                        lang="ru"
                    />
                </div>
                <div style="display: inline-block;">
                    <date-picker
                        v-model="filter.dateTo"
                        input-class="form-control"
                        format="YYYY-MM-DD"
                        lang="ru"
                    />
                </div>
                <div style="display: inline-block; width: 200px;">
                    <model-select
                        id="customersSelect"
                        v-model="filter.customer"
                        class="form-control"
                        :options="customers"
                    />
                </div>
                <div style="display: inline-block; width: 200px;">
                    <model-select
                        id="accountSelect"
                        v-model="filter.account"
                        class="form-control"
                        :options="bankAccounts"
                    />
                </div>
                <button
                    class="btn btn-default"
                    @click="refreshList()"
                >
                    Фильтр
                </button>
            </div>
        </div>

        <p
            v-if="permissions.edit"
            style="margin-bottom: 20px;"
        >
            <router-link
                class="btn btn-default"
                to="/add"
            >
                Добавить
            </router-link>
        </p>
        <table class="table table-striped table-bordered main-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Контрагент</th>
                    <th>Счет</th>
                    <th>Сумма</th>
                    <th v-if="permissions.edit" />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="item in items"
                    :key="item.id"
                >
                    <td>
                        {{ item.date }}
                    </td>
                    <td>
                        {{ item.customer.name }}
                    </td>
                    <td>
                        {{ item.bank.short }}
                    </td>
                    <td class="num">
                        {{ item.moneyFormat }}
                    </td>
                    <td v-if="permissions.edit">
                        <router-link :to="{ name: 'edit', params: { id: item.id } }">
                            <img
                                src="/images/edit.png"
                                alt="Редактировать"
                            >
                        </router-link>
                        <a
                            v-if="permissions.delete"
                            data-toggle="modal"
                            data-target="#confirmationModal"
                            href="#"
                            @click="selectItem(item)"
                        >
                            <img
                                src="/images/del.png"
                                alt="Удалить"
                            >
                        </a>
                    </td>
                </tr>
                <tr>
                    <td />
                    <td />
                    <td />
                    <td class="total num">
                        {{ sum }}
                    </td>
                    <td v-if="permissions.edit" />
                </tr>
            </tbody>
        </table>
    </div>
</template>
