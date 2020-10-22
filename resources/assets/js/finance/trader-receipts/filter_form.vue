<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';

    export default {
        name: 'FilterForm',
        components: {
            'date-picker': DatePicker,
        },
        props: {
            banks: {
                type: Array,
                required: true,
            },
            traders: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                filter: {
                    startDate: moment().startOf('month').format('YYYY-MM-DD'),
                    endDate: moment().endOf('month').format('YYYY-MM-DD'),
                    bank: 0,
                    trader: 0,
                    type: '0',
                },
            };
        },
        created() {
            this.search();
        },
        methods: {
            search() {
                const formData = new FormData();
                formData.append('startdate', moment(this.filter.startDate).format('YYYY-MM-DD'));
                formData.append('enddate', moment(this.filter.endDate).format('YYYY-MM-DD'));
                formData.append('bank', this.filter.bank);
                formData.append('trader', this.filter.trader);
                formData.append('type', this.filter.type);
                this.$emit('onSearch', formData);
            },
        },
    };
</script>

<template>
    <div class="form-inline">
        <div class="form-group">
            <date-picker
                v-model="filter.startDate"
                input-class="form-control"
                format="YYYY-MM-DD"
                lang="ru"
            />
        </div>
        <div class="form-group">
            <date-picker
                v-model="filter.endDate"
                input-class="form-control"
                format="YYYY-MM-DD"
                lang="ru"
            />
        </div>
        <select
            v-model="filter.bank"
            class="form-control"
        >
            <option
                selected="selected"
                :value="0"
            >
                Выбрать счет
            </option>
            <option
                v-for="bank in banks"
                :key="bank.id"
                :value="bank.id"
            >
                {{ bank.name }}
            </option>
        </select>
        <select
            v-model="filter.trader"
            class="form-control"
        >
            <option
                selected="selected"
                :value="0"
            >
                Выбрать трейдера
            </option>
            <option
                v-for="trader in traders"
                :key="trader.id"
                :value="trader.id"
            >
                {{ trader.name }}
            </option>
        </select>
        <select
            v-model="filter.type"
            class="form-control"
        >
            <option
                selected="selected"
                value="0"
            >
                Тип
            </option>
            <option value="black">
                Черный
            </option>
            <option value="color">
                Цветной
            </option>
        </select>
        <div class="form-group">
            <input
                id="submitbutton"
                type="submit"
                name="submit"
                class="btn btn-default"
                value="Фильтр"
                @click="search"
            >
        </div>
    </div>
</template>
