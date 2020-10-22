<script>
    export default {
        name: 'ConfirmationModal',
        methods: {
            yes() {
                this.$emit('onConfirmation');
            },
            no() {
                this.$emit('onDeny');
            },
            enter() {
                document.getElementById('btnYes').click();
            },
        },
    };
</script>
<template>
    <div>
        <div
            id="confirmationModal"
            class="modal fade bs-example-modal-sm"
            tabindex="-1"
            role="dialog"
            aria-labelledby="confirmationModalLabel"
            @keyup.enter="enter"
        >
            <div
                class="modal-dialog modal-sm"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>
                            <slot />
                        </h4>
                        <div style="text-align: center">
                            <button
                                id="btnYes"
                                type="button"
                                class="btn btn-primary control-buttons"
                                data-dismiss="modal"
                                @click="yes"
                            >
                                Да
                            </button>
                            <button
                                type="button"
                                class="btn btn-default control-buttons"
                                data-dismiss="modal"
                                @click="no"
                            >
                                Нет
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
