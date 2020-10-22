<script>
    import { ModelSelect } from 'vue-search-select';
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';

    export default {
        components: {
            'model-select': ModelSelect,
            'date-picker': DatePicker,
        },
        props: {
            planning: {
                type: Object,
                default() {
                    return {
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
                        employee: '',
                        number: 0,
                        status: [{
                            isComposite: false,
                            text: '',
                            units: '',
                            value: 0,
                        }],
                        vehicle: '',
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
                    return [{
                        isComposite: false,
                        text: '',
                        units: '',
                        value: 0,
                    }];
                },
            },
            addLinePosition: {
                type: Object,
                default() {
                    return {
                        countInPlanning: '',
                        id: '',
                    };
                },
            },
            vehicles: {
                type: Array,
                default() {
                    return [{
                        text: '',
                        value: '',
                    }];
                },
            },
            employees: {
                type: Array,
                default() {
                    return [{
                        text: '',
                        value: 0,
                    }];
                },
            },
            accesses: {
                type: Object,
                default() {
                    return {};
                },
            },
        },
        data() {
            return {
                comment: this.planning.comment,
                spareNew: '',
                countNew: '',
                dateForm: this.planning.date ? this.planning.date : Moment(Date.now()).format('YYYY-MM-DD'),
                employee: this.planning.employee ? this.planning.employee : '',
                vehicle: this.planning.vehicle ? this.planning.vehicle : '',
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
            <h4
                v-if="planning.number"
                class="col-lg-4 col-md-4 col-sm-4"
                style="text-align: left;"
            >
                Заявка № {{ planning.number }}
            </h4>

            <div class="col-lg-4 col-md-4 col-sm-4">
                <select
                    v-if="planning.number && accesses.status"
                    v-model="planning.status"
                    class="form-control"
                >
                    <option value="NEW">
                        Новая
                    </option>
                    <option value="IN_WORK">
                        В работе
                    </option>
                    <option value="ORDERED">
                        Заказано
                    </option>
                    <option value="CLOSED">
                        Закрыто
                    </option>
                </select>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Дата</label>
                    <date-picker
                        v-model="dateForm"
                        input-class="form-control"
                        lang="ru"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8">
                <div class="form-group">
                    <label for="comment">Комментарий</label>
                    <textarea
                        id="comment"
                        v-model="comment"
                        class="form-control"
                        rows="1"
                    />
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="employees">Сотрудник</label>
                    <div class="searchMenu">
                        <model-select
                            id="employees"
                            v-model="employee"
                            :options="employees"
                        />
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8">
                <div class="form-group">
                    <label for="vehicles">Техника</label>
                    <div class="searchMenu">
                        <model-select
                            id="vehicles"
                            v-model="vehicle"
                            :options="vehicles"
                        />
                    </div>
                </div>
            </div>
        </div>

        <p
            v-if="addMsg"
            style="color: red"
        >
            {{ addMsg }}
        </p>
        <hr>
        <div
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
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="count">Количество</label>
                    <input
                        id="count"
                        v-model="addLinePosition.count"
                        type="number"
                        class="form-control"
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <p style="margin-top: 25px;">
                    <span
                        class="btn btn-default"
                        @click="addPosition()"
                    >Добавить позицию</span>
                </p>
            </div>
        </div>
    </div>
</template>
