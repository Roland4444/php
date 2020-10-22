<script>
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';
    import { ModelListSelect } from 'vue-search-select';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        components: {
            DatePicker,
            ModelListSelect,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            sellers: {
                type: Array,
                default() {
                    return [{
                        id: 0,
                        name: '',
                    }];
                },
            },
        },
        data() {
            return {
                filter: {
                    startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
                    endDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0),
                    seller: {},
                },
            };
        },
        computed: {
            formattedStartDate: {
                set(value) {
                    this.filter.startDate = Moment(value)
                        .format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.startDate)
                        .format('YYYY-MM-DD');
                },
            },
            formattedEndDate: {
                set(value) {
                    this.filter.endDate = Moment(value)
                        .format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.endDate)
                        .format('YYYY-MM-DD');
                },
            },
        },
        methods: {
            search() {
                this.$emit('onSearch', {
                    startDate: this.formattedStartDate,
                    endDate: this.formattedEndDate,
                    seller: this.filter.seller,
                });
            },
        },
    };
</script>

<template>
    <div
        id="SellerReturnsListFilter"
        class="form-inline form-filter"
    >
        <span class="seller-model-select">
            <model-list-select
                id="modalPosition"
                v-model="filter.seller"
                :list="sellers"
                option-value="id"
                option-text="name"
                placeholder="Поставщик"
            />
        </span>
        <span style="display: inline-block;">
            <date-picker
                v-model="filter.startDate"
                input-class="form-control"
                format="YYYY-MM-DD"
                lang="ru"
            />
        </span>
        <span style="display: inline-block;">
            <date-picker
                v-model="filter.endDate"
                input-class="form-control"
                format="YYYY-MM-DD"
                lang="ru"
            />
        </span>
        <input
            id="submitbutton"
            type="submit"
            name="submit"
            class="btn btn-default"
            value="Фильтр"
            @click="search"
        >
    </div>
</template>

<style>
    .seller-model-select {
        display: inline-block;
        width: 200px;
    }
</style>
