<script>
    import DateFilter from '../../common/filters/date-filter.vue';
    import TransportIncomeModal from './transport_income_modal.vue';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    export default {
        name: 'TransportIncomeList',
        components: {
            DateFilter,
            TransportIncomeModal,
            ConfirmationModal,
        },
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
            access: {
                type: Object,
                required: false,
                default() {
                    return {
                        add: false,
                        delete: false,
                    };
                },
            },
        },
        data() {
            return {
                data: [],
                moneySum: 0,
                selectedItemId: null,
                selectedItem: {
                    date: new Date(),
                    money: '',
                },
            };
        },
        methods: {
            search(data) {
                this.$http.post(`${this.apiUrl}/list`, data).then((response) => {
                    this.data = response.body.rows;
                    this.moneySum = response.body.moneySum;
                }, (response) => {
                    this.$notify({
                        title: 'Ошибка',
                        text: response.body.error,
                        type: 'error',
                    });
                });
            },
            refresh() {
                this.$refs.dateFilter.search();
            },
            selectItem(row) {
                this.selectedItem = row;
            },
            clearItem() {
                this.selectedItem = {
                    date: new Date(),
                    money: '',
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
                    this.$notify({
                        title: 'Ошибка',
                        text: response.body.error,
                        type: 'error',
                    });
                });
            },
        },
    };
</script>

<template>
    <div>
        <notifications />

        <transport-income-modal
            ref="formModal"
            :item="selectedItem"
            :api-url="apiUrl"
            @onEntitySaved="refresh"
        />

        <confirmation-modal @onConfirmation="deleteSelectedItem">
            <span>Удалить запись?</span>
        </confirmation-modal>

        <date-filter
            ref="dateFilter"
            @onSearch="search"
        />

        <a
            v-if="access.add"
            class="btn btn-default"
            style="margin-top: 20px;"
            data-toggle="modal"
            data-target="#customModal"
            @click="clearItem"
        >
            Добавить
        </a>

        <table
            class="table table-striped main-table"
            style="max-width: 500px;"
        >
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th v-if="access.delete" />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in data"
                    :key="row.id"
                >
                    <td class="date">
                        {{ row['date'] }}
                    </td>
                    <td class="num">
                        {{ row['moneyFormat'] }} р.
                    </td>
                    <td
                        v-if="access.delete"
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
                                @click="selectItem(row)"
                            >
                        </a>
                        <a
                            data-toggle="modal"
                            data-target="#confirmationModal"
                            href="#"
                            @click="selectItem(row)"
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
                    <td class="num total">
                        {{ moneySum }} р.
                    </td>
                    <td v-if="access.delete" />
                </tr>
            </tbody>
        </table>
    </div>
</template>
