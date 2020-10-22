<script>
    import FilterForm from './filter_form.vue';
    import ReceiptsModal from './receipts_modal.vue';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    export default {
        name: 'FinanceTraderReceipts',
        components: {
            FilterForm,
            ReceiptsModal,
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
            traders: {
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
                    bank: {},
                    trader: {},
                },
            };
        },
        methods: {
            search(data) {
                this.$http.post(`${this.apiUrl}/json`, data).then((response) => {
                    this.data = response.body.rows;
                    this.moneySum = response.body.sum;
                }, (response) => {
                    this.$notify({
                        title: 'Ошибка',
                        text: response.body.error,
                        type: 'error',
                    });
                });
            },
            refresh() {
                this.$refs.filterForm.search();
            },
            selectItem(item) {
                Object.assign(this.selectedItem, item);
            },
            clearItem() {
                let defaultBank = this.banks.find((item) => item.def === true);
                if (!defaultBank) {
                    [defaultBank] = this.banks;
                }
                let defaultTrader = this.traders.find((item) => item.def === true);
                if (!defaultTrader) {
                    [defaultTrader] = this.traders;
                }
                this.selectedItem = {
                    date: new Date(),
                    trader: defaultTrader,
                    bank: defaultBank,
                    cost: '',
                    type: 'black',
                };
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
        },
    };
</script>

<template>
    <div>
        <notifications />

        <FilterForm
            ref="filterForm"
            :banks="banks"
            :traders="traders"
            style="text-align: center;"
            @onSearch="search"
        />

        <ReceiptsModal
            ref="formModal"
            :banks="banks"
            :traders="traders"
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
            data-toggle="modal"
            data-target="#customModal"
            @click="clearItem()"
        >
            Добавить
        </a>

        <table class="table table-striped table-bordered main-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Трейдер</th>
                    <th>Счет</th>
                    <th>Тип</th>
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
                    <td class="date">
                        {{ item.date }}
                    </td>
                    <td>
                        {{ item.trader.name }}
                    </td>
                    <td>
                        {{ item.bank.name }}
                    </td>
                    <td>
                        <span v-if="item.type=='black'">
                            Черный
                        </span>
                        <span v-else>
                            Цветной
                        </span>
                    </td>
                    <td class="num">
                        {{ item.moneyFormat }}
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
