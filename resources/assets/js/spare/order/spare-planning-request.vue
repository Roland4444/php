<script>
    import DatePicker from 'vue2-datepicker';

    export default {
        components: {
            'date-picker': DatePicker,
        },
        props: {
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
                dateFrom: this.startDateFrom,
                dateTo: this.startDateTo,
            };
        },
        methods: {
            getPlannings() {
                this.$emit('getPlannings', this.dateFrom, this.dateTo);
            },
        },
    };
</script>
<template>
    <div>
        <h4 style="text-align: left;">
            Заявки
        </h4>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <date-picker
                        v-model="dateFrom"
                        input-class="form-control"
                        lang="ru"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <date-picker
                        v-model="dateTo"
                        input-class="form-control"
                        lang="ru"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    <span
                        v-if="dateFrom && dateTo"
                        class="btn btn-default"
                        @click="getPlannings()"
                    >
                        Фильр
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
