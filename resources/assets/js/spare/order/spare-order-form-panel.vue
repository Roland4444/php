<script>
    import Moment from 'moment';
    import DatePicker from 'vue2-datepicker';
    import { ModelSelect } from 'vue-search-select';
    import OrderList from './spare-order-list.vue';
    import OrderModal from './spare-order-modal.vue';
    import PlanningList from './spare-planning-list.vue';
    import PlanningsRequest from './spare-planning-request.vue';

    export default {
        components: {
            'order-modal': OrderModal,
            'order-list': OrderList,
            'planning-list': PlanningList,
            'planning-request': PlanningsRequest,
            'date-picker': DatePicker,
            'model-select': ModelSelect,
        },
        props: {
            sparesObject: {
                type: Object,
                default() {
                    return {
                        isComposite: false,
                        text: '',
                        units: '',
                        value: 0,
                    };
                },
            },
            planningsData: {
                type: Array,
                default() {
                    return [{
                        comment: '',
                        data: [{
                            countInPlanning: '',
                            countOrdered: 0,
                            id: 0,
                            isComposite: 0,
                            nameSpare: '',
                            planningItemId: 0,
                            spareId: 0,
                            spareUnits: '',
                        }],
                        date: '',
                        employee: 0,
                        number: 0,
                        status: {},
                        vehicle: '',
                    }];
                },
            },
            roles: {
                type: Array,
                default() {
                    return [];
                },
            },
            sellerList: {
                type: Array,
                default() {
                    return {
                        default: false,
                        text: '',
                        value: 0,
                    };
                },
            },
            order: {
                type: Object,
                default() {
                    return {
                        data: [{
                            count: '',
                            countInPlanning: '',
                            countRestForOrder: 0,
                            date: '',
                            isComposite: 0,
                            itemId: 0,
                            nameSpare: '',
                            number: 0,
                            planningItemId: 0,
                            price: '',
                            spareId: 0,
                            spareUnits: '',
                            subCount: null,
                        }],
                        date: '',
                        documentName: '',
                        expectedDate: '',
                        id: 0,
                        provider: 0,
                    };
                },
            },
            cancelUri: {
                type: String,
                default: '',
            },
            saveUri: {
                type: String,
                default: '',
            },
            getPlanningUri: {
                type: String,
                default: '',
            },
            startDateFrom: {
                type: String,
                default: '',
            },
            startDateTo: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                sellers: Object.values(this.sellerList),
                plannings: this.planningsData,
                positions: this.order.data ? this.order.data : [],
                editablePosition: {},
                editMsg: '',
                msgResponse: '',
                documentName: this.order.documentName ? this.order.documentName : '',
                provider: this.order.provider ? this.order.provider : '',
                savingEnable: true,
                countPosition: this.positions ? Object.keys(this.positions).length : 0,
                orderId: this.order.id ? this.order.id : '',
                date: this.order.date ? this.order.date : Moment().format('YYYY-MM-DD'),
                orderData: this.getOrder(),
                spares: this.sparesObject ? Object.values(this.sparesObject) : [],
                expectedDate: this.order.expectedDate ? this.order.expectedDate : Moment().format('YYYY-MM-DD'),
            };
        },
        mounted() {
            this.countPosition = this.positions ? Object.keys(this.positions).length : 0;
            this.updateLists(null);
        },
        methods: {
            getOrder() {
                if (this.orderData) {
                    return this.orderData;
                }
                const order = [];

                if (this.order.data) {
                    Object.keys(this.order.data).forEach((keyItem) => {
                        order[keyItem] = [];
                        order[keyItem].count = this.order.data[keyItem].count;
                        order[keyItem].isComposite = this.order.data[keyItem].isComposite;
                        order[keyItem].subCount = this.order.data[keyItem].subCount;
                        order[keyItem].countRestForOrder = this.order.data[keyItem].countRestForOrder;
                    });
                }

                return order;
            },
            getPlannings(dateFrom, dateTo) {
                const formData = new FormData();
                formData.append('dateFrom', dateFrom
                    ? Moment(dateFrom).format('YYYY-MM-DD')
                    : '');
                formData.append('dataTo', dateTo
                    ? Moment(dateTo).format('YYYY-MM-DD')
                    : '');
                this.$http.post(this.getPlanningUri, formData).then((response) => {
                    if (response.body.status === 'ok') {
                        this.plannings = response.body.result;
                    }
                });
            },
            getClonePosition(key) {
                return {
                    itemId: this.positions[key].itemId,
                    planningItemId: this.positions[key].planningItemId,
                    nameSpare: this.positions[key].nameSpare,
                    spareId: this.positions[key].spareId,
                    spareUnits: this.positions[key].spareUnits,
                    count: this.positions[key].count,
                    countInOrder: this.positions[key].countInOrder,
                    date: this.positions[key].date,
                    number: this.positions[key].number,
                    isComposite: this.positions[key].isComposite,
                    subCount: this.positions[key].subCount,
                    price: this.positions[key].price,
                    countRestForOrder: this.positions[key].countRestForOrder,
                    key,
                };
            },
            save() {
                this.msgResponse = '';
                if (this.countPosition < 1) {
                    this.msgResponse = 'Для сохранения добавьте хотя бы одну позицию';
                    return;
                }

                if (!this.documentName || !this.provider) {
                    this.msgResponse = 'Поля Идентификатор документа, Поставщик не должны быть пустыми';
                    return;
                }

                this.savingEnable = false;

                const formData = new FormData();

                formData.append('date', this.date
                    ? Moment(this.date).format('YYYY-MM-DD')
                    : '');

                formData.append('expectedDate', this.expectedDate
                    ? Moment(this.expectedDate).format('YYYY-MM-DD')
                    : '');

                formData.append('documentName', this.documentName);
                formData.append('provider', this.provider);
                formData.append('positions', JSON.stringify(this.positions));
                formData.append('orderId', this.orderId);

                this.$http.post(this.saveUri, formData).then((response) => {
                    if (response.body.status === 'ok') {
                        this.msgResponse = 'Сохранено успешно';
                        window.location.href = this.cancelUri;
                    }
                }, (response) => {
                    this.savingEnable = true;
                    this.msgResponse = response.body.error;
                });
            },
            openModalForAdd(positionAdd) {
                const positionToAdd = positionAdd;
                positionToAdd.key = this.positions.length;
                this.editablePosition = positionToAdd;
            },
            openModalForEdit(key) {
                this.editablePosition = this.getClonePosition(key);
            },
            delPosition(key) {
                this.positions.splice(key, 1);
                this.countPosition = this.positions ? Object.keys(this.positions).length : 0;
                this.updateLists();
            },
            editPosition(editablePosition) {
                this.positions[editablePosition.key] = editablePosition;
                this.closeEditForm();
                this.countPosition = this.positions ? Object.keys(this.positions).length : 0;
                this.updateLists(editablePosition);
            },
            closeEditForm() {
                this.editablePosition = {};
            },
            updateLists(editablePosition) {
                // this.$refs.planningList.$forceUpdate();
                this.$refs.planningList.update(editablePosition);
                this.$refs.orderList.$forceUpdate();
            },
            isEnableSave() {
                if (!this.roles.includes('admin') && Moment().diff(this.date, 'days') > 14) {
                    return false;
                }
                return this.savingEnable;
            },
        },
    };
</script>
<template>
    <div style="margin-left: 20px">
        <order-modal
            :edit-msg="editMsg"
            :editable-position="editablePosition"
            @closeEditForm="closeEditForm"
            @editPosition="editPosition"
        />

        <h4 style="text-align: left;">
            Информация о заказе
        </h4>
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Дата заказа</label>
                    <date-picker
                        v-model="date"
                        input-class="form-control"
                        lang="ru"
                        :disabled="!roles.includes('admin') && !roles.includes('officecash')"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Дата поступления</label>
                    <date-picker
                        v-model="expectedDate"
                        input-class="form-control"
                        lang="ru"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="documentName">Документ</label>
                    <textarea
                        id="documentName"
                        v-model="documentName"
                        required
                        class="form-control"
                        rows="1"
                    />
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="provider">Поставщик</label>
                    <div class="searchMenu">
                        <model-select
                            id="provider"
                            v-model="provider"
                            :options="sellers"
                        />
                    </div>
                </div>
            </div>
        </div>

        <order-list
            ref="orderList"
            :positions="positions"
            :order-date="date"
            :count-position="countPosition"
            @delPosition="delPosition"
            @openModalForEdit="openModalForEdit"
        />

        <p
            v-if="msgResponse"
            style="color: red"
        >
            {{ msgResponse }}
        </p>
        <p style="text-align: right; margin-top: 10px;">
            <span
                v-if="countPosition > 0 && isEnableSave()"
                class="btn btn-default"
                @click="save()"
            >
                Сохранить
            </span>
            <a
                :href="cancelUri"
                class="btn btn-default"
            >Отмена</a>
        </p>

        <planning-request
            :start-date-from="startDateFrom"
            :start-date-to="startDateTo"
            @getPlannings="getPlannings"
        />

        <planning-list
            ref="planningList"
            :order="orderData"
            :positions="positions"
            :plannings="plannings"
            @openModalForAdd="openModalForAdd"
        />
    </div>
</template>
