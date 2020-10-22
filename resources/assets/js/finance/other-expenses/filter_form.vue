<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';
    import { ModelSelect } from 'vue-search-select';

    export default {
        name: 'FilterForm',
        components: {
            'date-picker': DatePicker,
            'model-select': ModelSelect,
        },
        props: {
            banks: {
                type: Array,
                required: true,
            },
            categories: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                filter: {
                    startDate: moment().startOf('month').format('YYYY-MM-DD'),
                    endDate: moment().endOf('month').format('YYYY-MM-DD'),
                    category: 0,
                    bank: 0,
                    comment: '',
                },
            };
        },
        created() {
            this.categories.unshift({
                text: 'Категория',
                value: 0,
            });
            this.search();
        },
        methods: {
            search() {
                const formData = new FormData();
                formData.append('startdate', moment(this.filter.startDate).format('YYYY-MM-DD'));
                formData.append('enddate', moment(this.filter.endDate).format('YYYY-MM-DD'));
                formData.append('category', this.filter.category);
                formData.append('bank', this.filter.bank);
                formData.append('comment', this.filter.comment);
                this.$emit('onSearch', formData);
            },
        },
    };
</script>

<template>
    <div
        class="form-inline"
        style="margin-bottom: 20px;"
        @keyup.enter="search"
    >
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
        <div
            class="form-group"
            style="width: 300px;"
        >
            <model-select
                v-model="filter.category"
                class="form-control"
                :options="categories"
            />
        </div>
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
        <input
            v-model="filter.comment"
            required
            type="text"
            class="form-control"
        >
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
