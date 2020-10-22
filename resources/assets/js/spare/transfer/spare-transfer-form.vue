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
            warehouses: {
                type: Array,
                default() {
                    return [{
                        text: '',
                        value: 0,
                    }];
                },
            },
            transfers: {
                type: Object,
                default() {
                    return {
                        data: [{
                            count: 0,
                            spare: 0,
                        }],
                        date: '',
                        transferId: 0,
                        warehouse: 0,
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
                        text: '',
                        value: 0,
                    }];
                },
            },
            sparesObject: {
                type: Object,
                default() {
                    return {};
                },
            },
            addLinePosition: {
                type: Object,
                default() {
                    return {
                        count: '',
                        spare: 0,
                    };
                },
            },
        },
        data() {
            return {
                dateForm: this.transfers.date ? this.transfers.date : Moment(Date.now()).format('YYYY-MM-DD'),
                warehouse: this.transfers.warehouse ? this.transfers.warehouse : '',
            };
        },
        methods: {
            addPosition() {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.$emit('addPosition', this.addLinePosition);
                    }
                });
            },
            getMaxCount() {
                if (!this.addLinePosition.spare) {
                    return '';
                }
                return `Остаток: ${this.sparesObject[this.addLinePosition.spare].total}`;
            },
        },
    };
</script>
<template>
    <div class="">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="warehouses">Склад назначения</label>
                    <div class="searchMenu">
                        <model-select
                            id="warehouses"
                            v-model="warehouse"
                            :options="warehouses"
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
        </div>

        <div
            v-if="! transfers.transferId"
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
                    <label for="count">Количество</label>
                    <input
                        id="count"
                        v-model="addLinePosition.count"
                        v-validate="'min:1'"
                        type="number"
                        min="1"
                        class="form-control"
                        autocomplete="off"
                        :name="'кол-во'"
                        data-vv-validate-on="change|blur"
                    >
                    <span class="validation-error-message">{{ errors.first('кол-во') }}</span>
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
            <div class="col-lg-12 col-md-12 col-sm-12">
                <p>{{ getMaxCount() }}</p>
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
