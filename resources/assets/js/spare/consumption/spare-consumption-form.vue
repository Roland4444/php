<script>
    import { ModelSelect } from 'vue-search-select';
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';

    export default {
        components: {
            'date-picker': DatePicker,
            'model-select': ModelSelect,
        },
        props: {
            roles: {
                type: Array,
                default() {
                    return [];
                },
            },
            vehicles: {
                type: Array,
                default() {
                    return {
                        text: '',
                        value: '',
                    };
                },
            },
            employees: {
                type: Array,
                default() {
                    return {
                        text: '',
                        value: 0,
                    };
                },
            },
            consumptions: {
                type: Object,
                default() {
                    return {
                        data: [{
                            comment: '',
                            editableSpare: {
                                isComposite: 0,
                                spareUnits: '',
                                text: '',
                                value: 0,
                            },
                            id: 0,
                            newQuantity: '',
                            quantity: '',
                            spare: {
                                isComposite: 0,
                                spareUnits: '',
                                text: '',
                                value: 0,
                            },
                            vehicle: {
                                text: '',
                                value: null,
                            },
                        }],
                        date: '',
                        employee: {
                            id: 0,
                            license: '',
                            name: '',
                        },
                        id: 0,
                    };
                },
            },
            addMsg: {
                type: String,
                default: '',
            },
            spares: {
                type: Array,
                default() {
                    return {
                        spareUnits: '',
                        text: '',
                        total: 0,
                        value: 0,
                    };
                },
            },
            sparesObject: {
                type: Object,
                default() {
                    return {
                        0: {
                            spareUnits: '',
                            spare_id: 0,
                            text: '',
                            total: 0,
                        },
                    };
                },
            },
            addLinePosition: {
                type: Object,
                default() {
                    return {
                        vehicle: {
                            value: null,
                            text: '',
                            total: 0,
                        },
                        comment: '',
                        spare: {
                            value: 0,
                            text: '',
                            spareUnits: '',
                        },
                        editableSpare: {
                            value: 0,
                            text: '',
                            spareUnits: '',
                        },
                        quantity: 0,
                    };
                },
            },

        },
        data() {
            return {
                dateForm: this.consumptions.date ? this.consumptions.date : Moment(Date.now()).format('YYYY-MM-DD'),
                employee: this.consumptions.employee ? this.consumptions.employee.id : '',
                addedPosition: {
                    vehiclesToSelect: {},
                },
                addLinePositionData: {
                    vehicles: {
                        value: '',
                        text: '',
                    },
                },
            };
        },
        methods: {
            addPosition() {
                this.$emit('addPosition', this.addLinePosition);
            },
        },
    };
</script>
<template>
    <div class="">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="employees">Сотрудник</label>
                    <div class="searchMenu">
                        <model-select
                            id="employees"
                            v-model="employee"
                            :option-value="'id'"
                            :options="employees"
                        />
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                <div class="form-group">
                    <label>Дата</label>
                    <date-picker
                        v-model="dateForm"
                        input-class="form-control"
                        lang="ru"
                        :disabled="!roles.includes('admin') && !roles.includes('officecash')"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="vehicles">Техника</label>
                    <div class="searchMenu">
                        <model-select
                            id="vehicles"
                            v-model="addLinePosition.vehicle"
                            :options="vehicles"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="! consumptions.consumptionId"
            class="row"
            style="margin-top: 5px; "
        >
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="position">Позиция</label>
                    <div class="searchMenu">
                        <model-select
                            id="position"
                            v-model="addLinePosition.spare"
                            :options="spares"
                        />
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                <div class="form-group">
                    <label for="quantity">Количество</label>
                    <input
                        id="quantity"
                        v-model="addLinePosition.quantity"
                        type="number"
                        min="0.01"
                        :max="addLinePosition.spare.total"
                        class="form-control"
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="supplierMsg">Комментарий</label>
                    <textarea
                        id="supplierMsg"
                        v-model="addLinePosition.comment"
                        class="form-control"
                        rows="1"
                    />
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <p v-if="addLinePosition.spare">
                    <b>Остаток: </b> {{ addLinePosition.spare.total }}
                </p>
                <p v-else />
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <p style="text-align: right;">
                    <span
                        class="btn btn-default"
                        @click="addPosition()"
                    >Добавить позицию</span>
                </p>
            </div>
        </div>

        <p
            v-if="addMsg"
            style="color: red"
        >
            {{ addMsg }}
        </p>
    </div>
</template>
