<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';

    export default {
        name: 'StorageCashTotalLegal',
        components: {
            'date-picker': DatePicker,
        },
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                filter: {
                    date: moment().endOf('month').format('YYYY-MM-DD'),
                },
                rows: [],
            };
        },
        created() {
            this.search();
        },
        methods: {
            search() {
                const formData = new FormData();
                formData.append('date', moment(this.filter.date).format('YYYY-MM-DD'));
                this.$http.post(`${this.apiUrl}/legal-data`, formData).then((response) => {
                    this.rows = response.body.rows;
                }, (response) => {
                    this.$notify({
                        title: 'Ошибка',
                        text: response.body.error,
                        type: 'error',
                    });
                });
            },
        },
    };
</script>
<template>
    <div>
        <notifications />
        <div class="form-inline">
            <div class="form-group">
                <date-picker
                    v-model="filter.date"
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

        <table
            class="table table-striped table-bordered main-table"
            style="max-width: 800px;"
        >
            <thead>
                <tr>
                    <th>
                        Компания
                    </th>
                    <th>
                        Сальдо
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in rows"
                    :key="row.customer_id"
                >
                    <td>
                        {{ row.customer }}
                    </td>
                    <td class="num">
                        {{ row.fact_total }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
