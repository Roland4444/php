<script>
    import OwnerAddForm from '../owner/Form.vue';

    export default {
        name: 'OwnerModal',

        components: {
            OwnerAddForm,
        },

        methods: {
            onOwnerSave(newOwner) {
                this.$emit('onOwnerSave', newOwner);
                this.$refs.closeModalButton.click();
            },
            onOwnerAddCancel() {
                this.$refs.closeModalButton.click();
            },
        },
    };
</script>

<template>
    <div
        id="ownerAddModal"
        class="modal fade bs-example-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="ownerAddModalLabel"
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
                        id="ownerAddModalLabel"
                        class="modal-title"
                    >
                        Добавить перевозчика
                    </h4>
                </div>
                <div class="modal-body">
                    <owner-add-form
                        @onOwnerSave="onOwnerSave"
                        @onOwnerAddCancel="onOwnerAddCancel"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
