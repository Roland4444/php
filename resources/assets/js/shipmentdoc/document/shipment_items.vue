<script>
    import Moment from 'moment';
    import { ModelListSelect } from 'vue-search-select';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'ShipmentItems',
        components: {
            ModelListSelect,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            date: Date,
            getContainersUrl: {
                type: String,
                default: '',
            },
            value: {
                type: Object,
                default() {
                    return {
                        container_items: [{
                            alias: '',
                            code: '',
                            name: '',
                            price: '',
                            quantity: '',
                        }],
                        id: 0,
                        name: '',
                        shipment_date: '',
                        tariff: '',
                        trader: '',
                    };
                },
            },
        },
        data() {
            return {
                containers: [],
                container: {},
            };
        },
        watch: {
            date(newVal) {
                this.getContainersByDate(newVal);
            },
        },
        created() {
            if (this.date) {
                this.getContainersByDate(this.date);
            }
        },
        methods: {
            getContainersByDate(date) {
                const dateString = Moment(date).format('YYYY-MM-DD');

                this.httpGetRequest(`${this.getContainersUrl}/${dateString}`).then((response) => {
                    this.containers = response.body.data;
                });
            },
            containerDesignation(container) {
                return `${container.name} ${container.shipment_date}`;
            },
            onContainerSelect() {
                this.$emit('input', this.container);
            },
            clearContainer() {
                this.container = {
                    id: null,
                    name: '',
                    shipment_date: '',
                    container_items: [],
                };
                this.$emit('input', this.container);
            },
        },
    };
</script>
<template>
    <div>
        <div
            v-if="containers.length !== 0"
            class="row driver-inputs"
        >
            <div class="col-md-2 mb-2">
                <label for="shipmentsList">Контейнеры</label>
            </div>
            <div class="col-md-4 mb-4">
                <model-list-select
                    id="shipmentsList"
                    v-model="container"
                    :list="containers"
                    option-value="id"
                    :custom-text="containerDesignation"
                    @input="onContainerSelect"
                />
                <div
                    v-show="container.name"
                    style="cursor: pointer; text-align: right;"
                    @click="clearContainer"
                >
                    Очистить
                </div>
            </div>
        </div>
    </div>
</template>
