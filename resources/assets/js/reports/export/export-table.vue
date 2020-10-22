<script>
    import Moment from 'moment';
    import Modal from './modal.vue';
    import ConfirmationModal from '../../common/confirmation_modal.vue';
    import TableFilter from './table-filter.vue';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        components: {
            Modal, ConfirmationModal, TableFilter,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            accessDelete: {
                type: Boolean,
                required: false,
                default: false,
            },
            apiUrl: {
                type: String,
                default: '',
            },
            getNumbersUrl: {
                type: String,
                default: '',
            },
            departments: {
                type: Array,
                default() {
                    return [{
                        id: 0,
                        name: '',
                        alias: '',
                        type: '',
                    }];
                },
            },
        },
        data() {
            return {
                modalData: null,
                selectedForDeleteId: 0,
                fullDeleteUrl: null,
                filter: {
                    startDate: new Date(),
                    endDate: new Date(),
                    comment: '',
                    type: '',
                    department: 0,
                },
                data: [],
                totalSor: '',
                totalWeight: '',
            };
        },
        computed: {
            formattedStartDate: {
                set(value) {
                    this.filter.startDate = Moment(value).format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.startDate).format('YYYY-MM-DD');
                },
            },
            formattedEndDate: {
                set(value) {
                    this.filter.endDate = Moment(value).format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.endDate).format('YYYY-MM-DD');
                },
            },
        },
        mounted() {
            this.getExports({
                formattedStartDate: this.formattedStartDate,
                formattedEndDate: this.formattedEndDate,
                filter: {
                    comment: this.filter.comment,
                    type: this.filter.type,
                    department: this.filter.department,
                },
            });
        },
        methods: {
            showModal(data) {
                this.modalData = data;
            },
            closeModal() {
                this.modalData = null;
            },
            deleteSelectedItem() {
                this.httpGetRequest(`${this.apiUrl}/delete/${this.selectedForDeleteId}`).then(() => {
                    const index = this.data.findIndex((item) => item.id === this.selectedForDeleteId);
                    if (index !== -1) {
                        this.data.splice(index, 1);
                    }
                });
            },
            selectForDelete(id) {
                this.selectedForDeleteId = id;
            },
            getExports(filterParams) {
                this.httpGetRequest(`${this.apiUrl}/list`, {
                    params: {
                        startdate: filterParams.formattedStartDate,
                        enddate: filterParams.formattedEndDate,
                        comment: filterParams.filter.comment,
                        type: filterParams.filter.type,
                        department: filterParams.filter.department,
                    },
                }).then((response) => {
                    this.data = response.body.rows;
                    this.totalSor = response.body.totalSor;
                    this.totalWeight = response.body.totalWeight;
                });
            },
        },
    };
</script>
<template>
    <div>
        <notifications />

        <table-filter
            :departments="departments"
            :api-url="apiUrl"
            @onSearch="getExports"
        />

        <modal
            v-if="modalData"
            :data="modalData"
            @closeModal="closeModal"
        />
        <table class="table table-striped main-table">
            <thead>
                <tr>
                    <th>Фото</th>
                    <th>Дата</th>
                    <th>Номер</th>
                    <th class="num">
                        Брутто
                    </th>
                    <th class="num">
                        Тара
                    </th>
                    <th class="num">
                        Масса
                    </th>
                    <th class="num">
                        Засор
                    </th>
                    <th class="num">
                        Итог
                    </th>
                    <th class="num">
                        Примеси
                    </th>
                    <th>№</th>
                    <th>ПЗУ</th>
                    <th v-if="accessDelete" />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(row, index) in data"
                    :key="index"
                >
                    <td class="foto">
                        <div
                            class="photo-preview"
                            @click="showModal(row)"
                        >
                            <img
                                style="width: 33px;height: 25px;"
                                :src="row['path'] + '_1.jpg'"
                                alt=""
                            >
                            <img
                                style="width: 33px;height: 25px;"
                                :src="row['path'] + '_2.jpg'"
                                alt=""
                            >
                        </div>
                    </td>
                    <td class="center">
                        {{ row['date'] }} - {{ row['time'] }}
                    </td>
                    <td class="center">
                        {{ row['transnumb'] }}
                    </td>
                    <td class="num">
                        {{ row['brute'] }}&nbsp;кг.
                    </td>
                    <td class="num">
                        {{ row['tare'] }}&nbsp;кг.
                    </td>
                    <td class="num">
                        {{ row['netto'] }}&nbsp;кг.
                    </td>
                    <td class="num">
                        {{ row['sor'] }}%
                    </td>
                    <td class="num">
                        {{ row['massa'] }}&nbsp;кг.
                    </td>
                    <td class="num">
                        {{ row['trash'] }}&nbsp;кг.
                    </td>
                    <td>{{ row['waybill'] }}</td>
                    <td>{{ row['department'] }}</td>
                    <td
                        v-if="accessDelete"
                        class="action"
                    >
                        <a
                            data-toggle="modal"
                            data-target="#confirmationModal"
                            href="#"
                            @click="selectForDelete(row.id)"
                        >
                            <img
                                src="/images/del.png"
                                alt="Удалить"
                            >
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">
                        Итого:
                    </td>
                    <td />
                    <td />
                    <td />
                    <td />
                    <td />
                    <td
                        class="num"
                        style="font-weight:bold;"
                    >
                        {{ totalSor }}
                    </td>
                    <td
                        class="num"
                        style="font-weight:bold;"
                    >
                        {{ totalWeight }} кг.
                    </td>
                    <td />
                    <td />
                    <td />
                    <td v-if="accessDelete" />
                </tr>
            </tbody>
        </table>

        <confirmation-modal
            @onConfirmation="deleteSelectedItem"
        >
            <span>Удалить?</span>
        </confirmation-modal>
    </div>
</template>
