<script>
    import Moment from 'moment';
    import TableFilter from './table-filter.vue';
    import SellerReturnForm from './seller-return-form.vue';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    export default {
        components: {
            TableFilter, ConfirmationModal, SellerReturnForm,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            sellers: {
                type: Array,
                default() {
                    return {
                        id: 0,
                        name: '',
                    };
                },
            },
            apiUrl: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                selectedForDeleteId: 0,
                filter: {
                    startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
                    endDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0),
                    seller: {},
                },
                items: [],
                sellerReturnItem: {
                    id: null,
                    money: null,
                    date: new Date(),
                    seller: null,
                },
            };
        },
        computed: {
            formattedStartDate: {
                set(value) {
                    this.filter.startDate = Moment(value)
                        .format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.startDate)
                        .format('YYYY-MM-DD');
                },
            },
            formattedEndDate: {
                set(value) {
                    this.filter.endDate = Moment(value)
                        .format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.endDate)
                        .format('YYYY-MM-DD');
                },
            },
        },
        mounted() {
            this.getSellerReturns({
                startDate: this.formattedStartDate,
                endDate: this.formattedEndDate,
                seller: this.filter.seller,
            });
        },
        // todo Тут нужно вызывать обновление таблицы методом getSellerReturns везде а не работать с массивом items
        methods: {
            getSellerReturns(filterParams) {
                this.httpGetRequest(`${this.apiUrl}/list`, {
                    params: {
                        startDate: filterParams.startDate,
                        endDate: filterParams.endDate,
                        seller: filterParams.seller.id,
                    },
                }).then((response) => {
                    this.items = response.body.data;
                });
            },
            selectForDelete(id) {
                this.selectedForDeleteId = id;
            },
            deleteSelectedItem() {
                this.httpGetRequest(`${this.apiUrl}/delete/${this.selectedForDeleteId}`).then(() => {
                    const index = this.items.findIndex((item) => item.id === this.selectedForDeleteId);
                    if (index !== -1) {
                        this.items.splice(index, 1);
                    }
                });
            },
            addSellerReturnToList(sellerReturn) {
                this.items.push(sellerReturn);
            },
            openEditModal(item) {
                this.sellerReturnItem = {
                    id: item.id,
                    date: item.date,
                    money: item.money,
                    seller: item.seller.id,
                };
            },
            openAddModal() {
                this.sellerReturnItem = {
                    id: null,
                    money: null,
                    date: new Date(),
                    seller: null,
                };
            },
        },
    };
</script>

<template>
    <div>
        <notifications />

        <table-filter
            :sellers="sellers"
            @onSearch="getSellerReturns"
        />

        <button
            class="btn btn-default"
            data-toggle="modal"
            data-target="#sellerReturnFormModal"
            @click="openAddModal"
        >
            Добавить
        </button>

        <table class="table table-striped main-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Продавец</th>
                    <th class="num">
                        Сумма
                    </th>
                    <th />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in items"
                    :key="index"
                >
                    <td class="center">
                        {{ item.date }}
                    </td>
                    <td class="center">
                        {{ item.seller.name }}
                    </td>
                    <td class="num">
                        {{ item.money }}
                    </td>
                    <td
                        class="action"
                    >
                        <a
                            data-toggle="modal"
                            data-target="#sellerReturnFormModal"
                            href="#"
                            @click="openEditModal(item)"
                        >
                            <img
                                src="/images/edit.png"
                                alt="Редактировать"
                            >
                        </a>
                        <a
                            data-toggle="modal"
                            data-target="#confirmationModal"
                            href="#"
                            @click="selectForDelete(item.id)"
                        >
                            <img
                                src="/images/del.png"
                                alt="Удалить"
                            >
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        <confirmation-modal
            @onConfirmation="deleteSelectedItem"
        >
            <span>Удалить?</span>
        </confirmation-modal>

        <seller-return-form
            :api-url="apiUrl"
            :sellers="sellers"
            :seller-return-item="sellerReturnItem"
            @onSellerReturnAdd="addSellerReturnToList"
        />
    </div>
</template>
