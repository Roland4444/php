<script>
    import ShipmentList from './shipment-list.vue';
    import ShipmentForm from './shipment-form.vue';

    export default {
        components: {
            'shipment-list': ShipmentList,
            'shipment-form': ShipmentForm,
        },
        props: {
            fullAccess: {
                type: Number,
                required: false,
                default: 0,
            },
            traders: {
                type: Array,
                required: true,
            },
            tariffs: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                state: 'shipment',
                listData: {
                    shipment: {},
                },
            };
        },
        mounted() {
            this.$events.$on('save-shipment', (shipment) => this.saveShipment(shipment));
        },
        methods: {
            saveShipment(shipment) {
                this.listData.shipment = shipment;
                this.state = 'container';
            },
        },
    };
</script>
<template>
    <div>
        <shipment-list v-model="listData" />
        <shipment-form
            v-if="state=='shipment'"
            :full-access="fullAccess"
            :traders="traders"
            :tariffs="tariffs"
        />
    </div>
</template>
