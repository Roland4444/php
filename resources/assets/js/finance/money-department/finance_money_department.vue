<script>
    import FilterForm from './filter_form.vue';
    import MoneyDepartmentModal from './modal.vue';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    export default {
        name: 'FinanceCashTransfer',
        components: {
            FilterForm,
            MoneyDepartmentModal,
            ConfirmationModal,
        },
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
            banks: {
                type: Array,
                required: true,
            },
            departments: {
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
                    department: {},
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
                Object.assign(this.selectedItem, item);
            },
            clearItem() {
                const defaultDepatment = this.departments[0];
                const defaultBank = this.banks.find((item) => item.def === true);
                this.selectedItem = {
                    date: new Date(),
                    department: {},
                    bank: {},
                    cost: '',
                };
                Object.assign(this.selectedItem.department, defaultDepatment);
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
            :departments="departments"
            style="text-align: center;"
            @onSearch="search"
        />

        <MoneyDepartmentModal
            ref="formModal"
            :banks="banks"
            :departments="departments"
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

        <table class="table table-striped table-bordered main-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Источник</th>
                    <th>Получатель</th>
                    <th>Сумма</th>
                    <th v-if="permissions.edit" />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="item in data"
                    :id="'tr' + item.id"
                    :key="item.id"
                >
                    <td
                        class="date"
                    >
                        {{ item.date }}
                    </td>
                    <td>{{ item.department.name }}</td>
                    <td>{{ item.bank.name }}</td>
                    <td
                        class="num"
                    >
                        <span v-if="item.verified">
                            {{ item.moneyFormat }}&nbsp;
                            <span
                                class="glyphicon glyphicon-ok text-success"
                                aria-hidden="true"
                            />
                        </span>
                        <span v-else>
                            <label>
                                {{ item.moneyFormat }}&nbsp;
                                <input
                                    ng-model="item.verified"
                                    type="checkbox"
                                    @change="verify(item.id)"
                                >
                            </label>
                        </span>
                    </td>
                    <td
                        v-if="permissions.edit"
                        class="action"
                    >
                        <a
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
                    <td />
                    <td />
                    <td class="total num">
                        {{ moneySum }}
                    </td>
                    <td v-if="permissions.edit" />
                </tr>
            </tbody>
        </table>
    </div>
</template>
