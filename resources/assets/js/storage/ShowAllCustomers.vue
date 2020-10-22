<script>
    export default {
        name: 'ShowAllCustomers',
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
            departmentId: {
                type: Number,
                required: true,
            },
        },
        data() {
            return {
                customers: [],
            };
        },
        created() {
            const formData = new FormData();
            formData.append('department', this.departmentId);
            this.getCustomers('list-used', formData);
        },
        methods: {
            showAllCustomers() {
                this.getCustomers('list', {});
            },
            getCustomers(action, params) {
                this.$http.post(`${this.apiUrl}/${action}`, params).then((response) => {
                    this.customers = response.body;
                    this.$emit('customersUpdated', this.customers);
                }, (response) => {
                    this.showError(response.body.error);
                });
            },
        },
    };
</script>
<template>
    <div
        style="margin-top:25px;"
        class="btn btn-default"
        @click="showAllCustomers"
    >
        все поставщики
    </div>
</template>
