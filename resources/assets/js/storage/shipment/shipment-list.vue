<script>
    export default {
        props: {
            value: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
            };
        },
        methods: {

        },
    };
</script>
<template>
    <div>
        <b-alert show>
            {{ value.shipment.date }}.
        </b-alert>
    </div>
</template>
