<script>
    export default {
        props: {
            addableReceiptItem: {
                type: Object,
                default() {
                    return {
                        countInOrder: '',
                        countReceipted: 0,
                        countRestForReceipt: 0,
                        id: 0,
                        isComposite: false,
                        itemPrice: '',
                        nameSpare: '',
                        orderItemId: 0,
                        spareUnits: '',
                        subCount: null,
                    };
                },
            },
        },
        methods: {
            closeAddForm() {
                this.addableReceiptItem.count = this.addableReceiptItem.countInOrder;
                this.$emit('closeAddForm');
            },

            validateForm() {
                if (parseFloat(this.addableReceiptItem.countRestForReceipt) <= 0) {
                    this.$notify({ text: 'Количество должно быть не нулевым' });
                    this.addableReceiptItem.countRestForReceipt = parseFloat(this.addableReceiptItem.countInOrder)
                        - this.addableReceiptItem.countReceipted;
                    this.$forceUpdate();
                    return false;
                }

                if (parseFloat(this.addableReceiptItem.countInOrder)
                    < (parseFloat(this.addableReceiptItem.countRestForReceipt)
                        + this.addableReceiptItem.countReceipted)) {
                    this.$notify({ text: 'Количество не должно превышать указанное в заявке' });
                    this.addableReceiptItem.countRestForReceipt = parseFloat(this.addableReceiptItem.countInOrder)
                        - this.addableReceiptItem.countReceipted;
                    this.$forceUpdate();
                    return false;
                }

                return true;
            },
            addReceiptItem() {
                if (!this.validateForm()) {
                    return;
                }
                this.$store.commit('addReceiptItem', this.addableReceiptItem);
                this.$refs.closeModalButton.click();
            },
        },
    };
</script>
<template>
    <div
        id="addReceiptModal"
        class="modal fade bs-example-modal-sm"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addReceiptModalLabel"
        data-backdrop="static"
        data-keyboard="false"
    >
        <div
            class="modal-dialog modal-sm"
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
                        id="addReceiptModalLabel"
                        class="modal-title"
                    >
                        Добавить
                        {{ addableReceiptItem.nameSpare }}
                        {{ Number(addableReceiptItem.itemPrice).toFixed(2) }}
                    </h4>
                </div>
                <div class="modal-body">
                    <div
                        class="row"
                        style="margin-top: 5px; "
                    >
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="modalCount">Количество</label>
                                <input
                                    id="modalCount"
                                    v-model="addableReceiptItem.countRestForReceipt"
                                    type="number"
                                    class="form-control"
                                    autocomplete="off"
                                    autofocus
                                    min="1"
                                    @keyup.enter="addReceiptItem()"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                        @click="closeAddForm()"
                    >
                        Закрыть
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="addReceiptItem()"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
