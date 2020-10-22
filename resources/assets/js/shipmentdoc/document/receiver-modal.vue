<script>
    import ReceiverAddForm from '../receiver/receiver-form.vue';

    export default {
        name: 'ReceiverModal',

        components: {
            ReceiverAddForm,
        },

        props: {
            saveReceiverUrl: {
                type: String,
                default: '',
            },
            entityType: {
                type: String,
                default: '',
            },
        },

        data() {
            return {
                newReceiver: {
                    name: '',
                    legal_name: '',
                    inn: '',
                    address: '',
                    phone: '',
                    account: '',
                    contract_title: '',
                    contract_date: '',
                },
            };
        },

        methods: {
            emitNewEntity(newEntity) {
                if (this.entityType === 'receiver') {
                    this.$emit('onReceiverAdd', newEntity);
                } else {
                    this.$emit('onPayerAdd', newEntity);
                }

                this.$refs.closeModalButton.click();
            },
        },
    };
</script>

<template>
    <div
        id="receiverAddModal"
        class="modal fade bs-example-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="receiverAddModalLabel"
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
                        id="receiverAddModalLabel"
                        class="modal-title"
                    >
                        Добавить
                        <span v-if="entityType === 'receiver'">грузополучателя</span>
                        <span v-else>плательщика</span>
                    </h4>
                </div>
                <div class="modal-body">
                    <receiver-add-form
                        :entity-type="entityType"
                        :save-receiver-url="saveReceiverUrl"
                        :type="'wizard'"
                        @onReceiverSave="emitNewEntity"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
