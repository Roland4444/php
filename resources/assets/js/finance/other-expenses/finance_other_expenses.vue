<script>
    import FilterForm from './filter_form.vue';
    import OtherExpensesModal from './edit_expense_modal.vue';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    export default {
        name: 'FinanceOtherExpenses',
        components: {
            FilterForm,
            OtherExpensesModal,
            ConfirmationModal,
        },
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
            importUrl: {
                type: String,
                required: true,
            },
            banks: {
                type: Array,
                required: true,
            },
            categories: {
                type: Array,
                required: true,
            },
            permissions: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
                data: [],
                moneySum: null,
                selectedItem: {
                    category: {},
                    bank: {},
                },
            };
        },
        methods: {
            search(data) {
                this.$http.post(`${this.apiUrl}/json`, data).then((response) => {
                    this.data = response.body.rows;
                    this.moneySum = response.body.sum;
                }, (response) => {
                    this.showError(response.body.error);
                });
            },
            refresh() {
                this.$refs.filterForm.search();
            },
            selectItem(item) {
                this.selectedItem = item;
            },
            clearItem() {
                const defaultCategory = this.categories.find((item) => item.def === true);
                const defaultBank = this.banks.find((item) => item.def === true);
                this.selectedItem = {
                    date: new Date(),
                    recipient: '',
                    inn: '',
                    category: {},
                    bank: {},
                    money: '',
                };
                Object.assign(this.selectedItem.category, defaultCategory);
                Object.assign(this.selectedItem.bank, defaultBank);
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
                    this.refresh();
                }, (response) => {
                    this.showError(response.body.error);
                });
            },
            verify(id) {
                const formData = new FormData();
                formData.append('id', id);
                this.$http.post(`${this.apiUrl}/verify`, formData).then(() => {
                    this.refresh();
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
        },
    };
</script>

<template>
    <div>
        <notifications />

        <FilterForm
            ref="filterForm"
            :banks="banks"
            :categories="categories"
            style="text-align: center;"
            @onSearch="search"
        />

        <OtherExpensesModal
            ref="formModal"
            :banks="banks"
            :categories="categories"
            :item="selectedItem"
            :api-url="apiUrl"
            @onEntitySaved="refresh"
        />

        <confirmation-modal @onConfirmation="deleteSelectedItem">
            <span>Удалить запись?</span>
        </confirmation-modal>

        <a
            v-if="permissions.add"
            href="#"
            class="btn btn-default"
            data-target="#customModal"
            data-toggle="modal"
            @click="clearItem()"
        >
            Добавить
        </a>

        <a
            v-if="permissions.import"
            :href="importUrl"
            class="btn btn-default"
        >
            Импорт из БК
        </a>

        <div
            v-for="day in data"
            :key="day.date"
        >
            <div
                class="total"
                style="margin-top: 20px;"
            >
                {{ day.date }}
            </div>
            <table class="table table-bordered table-striped table-condensed main-table">
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Сумма</th>
                        <th>Комментарий</th>
                        <th>Дата расхода</th>
                        <th>Счет</th>
                        <th v-if="permissions.edit || permissions.delete" />
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in day.expenses"
                        :key="item.id"
                        :class="{ warning : !item.realdate || item.realdate ==='0000-00-00' }"
                    >
                        <td class="category">
                            {{ item.category.name }}
                        </td>
                        <td class="num">
                            {{ item.moneyFormat }}
                        </td>
                        <td title="item.recepient">
                            {{ item.comment }}
                        </td>
                        <td class="date">
                            {{ item.realdate }}
                        </td>
                        <td class="short-bank">
                            {{ item.bank.short }}
                        </td>
                        <td
                            v-if="permissions.edit || permissions.delete"
                            class="action"
                        >
                            <a
                                v-if="permissions.edit"
                                data-toggle="modal"
                                data-target="#customModal"
                            >
                                <img
                                    style="cursor: pointer"
                                    src="/images/edit.png"
                                    alt="Редактировать"
                                    @click="selectItem(item)"
                                >
                            </a>
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
                        <td class="total num">
                            {{ day.sum }}
                        </td>
                        <td />
                        <td />
                        <td />
                        <td v-if="permissions.edit || permissions.delete" />
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="total">
            Общая сумма: {{ moneySum }}
        </p>
    </div>
</template>
