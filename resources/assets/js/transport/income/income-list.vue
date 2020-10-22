<script>
    import DateFilter from '../../common/filters/date-filter.vue';

    export default {
        name: 'IncomeList',
        components: {
            DateFilter,
        },
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                data: [],
                moneySum: 0,
                total: 0,
            };
        },
        methods: {
            search(data) {
                this.$http.post(`${this.apiUrl}/list`, data).then((response) => {
                    this.data = response.body.rows;
                    this.moneySum = response.body.moneySum;
                    this.total = response.body.total;
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

        <date-filter @onSearch="search" />

        <h2>Остаток: {{ total }}</h2>

        <table
            class="table table-striped main-table"
            style="max-width: 800px;"
        >
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in data"
                    :key="row.id"
                >
                    <td class="date">
                        {{ row['date'] }}
                    </td>
                    <td>
                        {{ row['customer'] }}
                    </td>
                    <td class="num">
                        {{ row['money'] }}
                    </td>
                </tr>
                <tr>
                    <td />
                    <td />
                    <td class="num">
                        {{ moneySum }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
