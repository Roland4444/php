<script>
    export default {
        name: 'Modal',
        methods: {
            ok() {
                this.$emit('onModalOk');
            },
            cancel() {
                this.$emit('onModalCancel');
            },
        },
    };
</script>
<template>
    <div>
        <div
            id="customModal"
            class="modal fade bs-example-modal-sm"
            tabindex="-1"
            role="dialog"
            aria-labelledby="confirmationModalLabel"
        >
            <div
                class="modal-dialog modal-sm"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-body">
                        <slot />
                        <div style="text-align: center">
                            <button
                                type="button"
                                class="btn btn-primary control-buttons"
                                @click="ok"
                            >
                                Сохранить
                            </button>
                            <button
                                id="modalCancelBtn"
                                type="button"
                                class="btn btn-default control-buttons"
                                data-dismiss="modal"
                                @click="cancel"
                            >
                                Отмена
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .control-buttons {
        width: 100px;
    }
</style>
