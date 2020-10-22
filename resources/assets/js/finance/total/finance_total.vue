<script>
    import DateFilter from '../../common/filters/date-filter.vue';

    export default {
        name: 'FinanceTotal',
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
                data: {
                    accounts: [],
                    total: {},
                    factoring: {},
                    parents: [],
                },
                showHide: false,
            };
        },
        methods: {
            search(data) {
                this.$http.post(`${this.apiUrl}/json`, data).then((response) => {
                    this.data = response.body.rows;
                    this.moneySum = response.body.moneySum;
                }, (response) => {
                    this.$notify({
                        title: 'Ошибка',
                        text: response.body.error,
                        type: 'error',
                    });
                });
            },
            revertHide() {
                this.showHide = !this.showHide;
            },
        },
    };
</script>

<template>
    <div>
        <notifications />
        <date-filter
            ref="dateFilter"
            style="text-align: center;"
            @onSearch="search"
        />

        <div style="overflow: hidden;">
            <div style="float:left;width: 48%;margin: 5px;">
                <h3>Остатки на счетах</h3>
                <table class="table table-bordered table-striped main-table">
                    <thead>
                        <tr>
                            <th>Номер счета</th>
                            <th>Остаток</th>
                            <th>Овердрафт</th>
                            <th>Банк</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(acc, index) in data.accounts.items"
                            :key="index"
                        >
                            <td>
                                {{ acc.name }}
                            </td>
                            <td class="num">
                                {{ acc.total }}
                            </td>
                            <td class="num">
                                {{ acc.overdraft }}
                            </td>
                            <td class="num">
                                {{ acc.bank }}
                            </td>
                        </tr>
                        <tr>
                            <td />
                            <td class="total num">
                                {{ data.accounts.sum }}
                            </td>
                            <td />
                            <td />
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="float:right;width: 48%;margin: 5px;">
                <h3>Приход/Расход</h3>
                <table class="table table-bordered table-striped main-table">
                    <tbody>
                        <tr>
                            <td>Приход от трейдеров</td>
                            <td class="num">
                                {{ data.total.traderReceipts }}
                            </td>
                        </tr>
                        <tr>
                            <td>Управленческие расходы</td>
                            <td class="num">
                                {{ data.total.otherExpense }}
                            </td>
                        </tr>
                        <tr>
                            <td>Прочие приходы</td>
                            <td class="num">
                                {{ data.total.otherReceipts }}
                            </td>
                        </tr>
                        <tr>
                            <td>Расходы в кассу</td>
                            <td class="num">
                                {{ data.total.moneyDepartment }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3>Факторинг</h3>
                <table class="table table-bordered table-striped main-table">
                    <tbody>
                        <tr>
                            <td>
                                {{ data.factoring.title }}
                            </td>
                            <td class="num">
                                {{ data.factoring.total }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <h3>Остатки по контрагентам</h3>
        <p style="text-align: right;">
            <strong
                style="color: #229a13; cursor: pointer; "
                @click="revertHide()"
            >
                <span v-if="showHide">
                    Скрыть архивных контрагентов
                </span>
                <span v-if="! showHide">
                    Показать архивных контрагентов
                </span>
            </strong>
        </p>

        <div
            v-for="parent in data.parents.items"
            :key="parent.id"
        >
            <div v-if="parent.traders.length && (! parent.hide || showHide)">
                <p style="font-weight: bold">
                    {{ parent.name }}
                </p>
                <table class="table table-bordered table-striped main-table">
                    <thead>
                        <th>Контрагент</th>
                        <th>Отгружено на сумму</th>
                        <th>Оплачено</th>
                        <th>Контрагенты должны</th>
                    </thead>
                    <tbody>
                        <tr
                            v-for="trader in parent.traders"
                            :key="trader.id"
                        >
                            <td>
                                {{ trader.name }}
                            </td>
                            <td class="num">
                                {{ trader.send }}
                            </td>
                            <td class="num">
                                {{ trader.payment }}
                            </td>
                            <td class="num">
                                {{ trader.total }}
                            </td>
                        </tr>
                        <tr>
                            <td />
                            <td class="num" />
                            <td class="num" />
                            <td class="total num">
                                {{ parent.sum }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <table class="table table-bordered table-striped main-table">
            <tbody>
                <tr>
                    <td />
                    <td class="num" />
                    <td class="num" />
                    <td class="total num">
                        {{ data.parents.sum }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
