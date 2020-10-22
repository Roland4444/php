<script>
    import { ModelSelect } from 'vue-search-select';
    import DatePicker from 'vue2-datepicker';
    import moment from 'moment';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        components: {
            'date-picker': DatePicker,
            'model-select': ModelSelect,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            action: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                dateFrom: moment().startOf('month').format('YYYY-MM-DD'),
                dateTo: moment().endOf('month').format('YYYY-MM-DD'),
                sellerId: '',
            };
        },
        computed: {
            apiUrl() {
                return this.$store.state.apiUrl;
            },
            sellers() {
                return this.$store.state.sellers;
            },
            receiptItems() {
                return this.$store.state.receiptItems;
            },
            getStartDate() {
                if (this.action === 'update') {
                    return moment(this.receiptItems
                        .map((receiptItem) => new Date(receiptItem.orderDate))
                        .sort((a, b) => a - b)
                        .shift())
                        .format('YYYY-MM-DD');
                }

                return moment().startOf('month').format('YYYY-MM-DD');
            },
        },
        created() {
            this.getOrders(this.getStartDate, this.dateTo);
            this.dateFrom = this.getStartDate;
        },
        methods: {
            getOrders(dateFrom, dateTo) {
                this.httpPostRequest(`${this.apiUrl}/getOrders`, {
                    dateFrom,
                    dateTo,
                    seller: this.sellerId,
                }, true).then((response) => {
                    if (response.body.status === 'ok') {
                        this.$store.commit('setOrders', response.body.result);
                    }
                });
            },
        },
    };
</script>
<template>
    <div>
        <h4 class="orders-title">
            Заказы
        </h4>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <date-picker
                        v-model="dateFrom"
                        input-class="form-control"
                        lang="ru"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <date-picker
                        v-model="dateTo"
                        input-class="form-control"
                        lang="ru"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <div class="searchMenu">
                        <model-select
                            id="provider"
                            v-model="sellerId"
                            :options="sellers"
                            placeholder="Поставщик"
                        />
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-3">
                <span
                    v-if="dateFrom && dateTo"
                    class="btn btn-default filter-button"
                    @click="getOrders(dateFrom, dateTo)"
                >
                    Фильтр
                </span>
            </div>
        </div>
    </div>
</template>

<style>
    .filter-button {
        width: 100%;
    }
    .orders-title {
        text-align: left;
    }
</style>
