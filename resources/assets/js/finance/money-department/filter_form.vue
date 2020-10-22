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
            departments: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                filter: {
                    startDate: moment().startOf('month').format('YYYY-MM-DD'),
                    endDate: moment().endOf('month').format('YYYY-MM-DD'),
                    department: 0,
                    bank: 0,
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
                formData.append('department', this.filter.department);
                formData.append('bank', this.filter.bank);
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
            v-model="filter.department"
            class="form-control"
        >
            <option
                selected="selected"
                :value="0"
            >
                Подразделение
            </option>
            <option
                v-for="department in departments"
                :key="department.id"
                :value="department.id"
            >
                {{ department.name }}
            </option>
        </select>
        <select
            v-model="filter.bank"
            class="form-control"
        >
            <option :value="0">
                Счет
            </option>
            <option
                v-for="bank in banks"
                :key="bank.id"
                :value="bank.id"
            >
                {{ bank.name }}
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
