<script>
    import Datepicker from 'vuejs-datepicker';
    import { ru } from 'vuejs-datepicker/dist/locale';
    import moment from 'moment';

    export default {
        components: {
            'date-picker': Datepicker,
        },
        props: {
            fullAccess: {
                type: Number,
                required: false,
                default: 0,
            },
            traders: {
                type: Array,
                required: true,
            },
            tariffs: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                rus: ru,
                shipment: {
                    date: Date.now(),
                    trader: null,
                    tariff: null,
                    rate: 0,
                },
            };
        },
        mounted() {
            this.$nextTick(() => {
                this.shipment.trader = this.getDefaultItem(this.traders);
                this.shipment.tariff = this.getDefaultItem(this.tariffs);
            });
        },
        methods: {
            getDefaultItem(data) {
                let result = null;
                data.forEach((item) => {
                    if (item.default) {
                        result = item.value;
                    }
                });
                return result;
            },
            save() {
                this.shipment.date = moment(this.shipment.date).format('YYYY-MM-DD');
                this.$events.fire('save-shipment', this.shipment);
            },
        },
    };
</script>
<template>
    <div class="col-md-4">
        <b-form>
            <b-form-group
                label="Дата:"
                label-for="exampleInput1"
            >
                <date-picker
                    id="date"
                    v-model="shipment.date"
                    :language="rus"
                    input-class="form-control"
                    monday-first
                    format="yyyy-MM-dd"
                    typeable
                    :disabled="!fullAccess"
                    required
                />
            </b-form-group>
            <b-form-group
                label="Трейдер:"
                label-for="trader"
            >
                <b-form-select
                    id="trader"
                    v-model="shipment.trader"
                    :options="traders"
                />
            </b-form-group>
            <b-form-group
                label="Тариф:"
                label-for="tariff"
            >
                <b-form-select
                    id="tariff"
                    v-model="shipment.tariff"
                    :options="tariffs"
                />
            </b-form-group>
            <b-form-group
                v-if="fullAccess"
                label="Курс доллара:"
                label-for="rate"
            >
                <b-form-input
                    id="rate"
                    v-model="shipment.rate"
                    type="number"
                    required
                    placeholder="Введите курс"
                />
            </b-form-group>
            <b-button
                variant="primary"
                @click="save"
            >
                Далее
            </b-button>
        </b-form>
    </div>
</template>
