<script>
    import DriverAddForm from '../driver/driver-form.vue';

    export default {
        name: 'DriverModal',

        components: {
            DriverAddForm,
        },

        props: {
            saveDriverUrl: {
                type: String,
                default: '',
            },
            formType: {
                type: String,
                default: '',
            },
        },

        methods: {
            onDriverAdd(newDriver) {
                this.$emit('onDriverAdd', newDriver);
                this.$refs.closeModalButton.click();
            },
        },
    };
</script>

<template>
    <div
        id="driverAddModal"
        class="modal fade bs-example-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="driverAddModalLabel"
    >
        <div
            class="modal-dialog modal-lg"
            role="document"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span
                            ref="closeModalButton"
                            aria-hidden="true"
                        >&times;</span>
                    </button>
                    <h4
                        id="driverAddModalLabel"
                        class="modal-title"
                    >
                        Добавить водителя
                    </h4>
                </div>
                <div class="modal-body">
                    <driver-add-form
                        :save-driver-url="saveDriverUrl"
                        :type="formType"
                        @onDriverAdd="onDriverAdd"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
