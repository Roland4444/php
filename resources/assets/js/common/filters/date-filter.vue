<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';

    export default {
        name: 'DateFilter',
        components: {
            'date-picker': DatePicker,
        },
        data() {
            return {
                filter: {
                    startDate: moment().startOf('month').format('YYYY-MM-DD'),
                    endDate: moment().endOf('month').format('YYYY-MM-DD'),
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
