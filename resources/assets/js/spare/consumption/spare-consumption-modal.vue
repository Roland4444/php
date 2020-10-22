<script>
    import { ModelSelect } from 'vue-search-select';

    export default {
        components: {
            'model-select': ModelSelect,
        },
        props: {
            spares: {
                type: Array,
                default() {
                    return {
                        spareUnits: '',
                        text: '',
                        total: 0,
                        value: 0,
                    };
                },
            },
            editMsg: {
                type: String,
                default: '',
            },
            editLinePosition: {
                type: Object,
                default() {
                    return {
                        comment: '',
                        editableSpare: {
                            isComposite: 0,
                            spareUnits: '',
                            text: '',
                        },
                        id: 0,
                        newQuantity: '',
                        quantity: '',
                        spare: {
                            isComposite: 0,
                            spareUnits: '',
                            text: '',
                            value: 0,
                        },
                        vehicle: {
                            text: '',
                            value: null,
                        },
                    };
                },
            },
            vehicles: {
                type: Array,
                default() {
                    return [{
                        text: '',
                        value: '',
                    }];
                },
            },
            positions: {
                type: Array,
                default() {
                    return [{
                        comment: '',
                        editableSpare: {
                            isComposite: 0,
                            spareUnits: '',
                            text: '',
                            value: 0,
                        },
                        id: 0,
                        newQuantity: '',
                        quantity: '',
                        spare: {
                            isComposite: 0,
                            spareUnits: '',
                            text: '',
                            value: 0,
                        },
                        vehicle: {
                            text: '',
                            value: null,
                        },
                    }];
                },
            },
        },
        data() {
            return {
                spareEdit: '',
                countEdit: '',
                sparesData: this.spares,
            };
        },
        computed: {
            editLinePositionIndex() {
                for (const [index, position] of this.positions.entries()) {
                    if (position.id === this.editLinePosition.id) {
                        return index + 1;
                    }
                }

                return 0;
            },
        },
        methods: {
            editPosition() {
                this.$emit('editPosition', this.editLinePosition);
            },
            closeEditForm() {
                this.$emit('closeEditForm');
            },
            closeModal() {
                this.$refs.closeModalButton.click();
            },
        },
    };
</script>
<template>
    <!-- Modal - edit weighing -->
    <div
        id="consumptionModalForm"
        class="modal fade bs-example-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="consumptionModalFormLabel"
    >
        <div
            class="modal-dialog modal-lg"
            role="document"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <button
                        ref="closeModalButton"
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4
                        id="consumptionModalFormLabel"
                        class="modal-title"
                    >
                        Редактирование позиции #{{ editLinePositionIndex }}
                    </h4>
                    <p
                        v-if="editMsg"
                        style="color: red"
                    >
                        {{ editMsg }}
                    </p>
                </div>
                <div class="modal-body">
                    <div
                        class="row"
                        style="margin-top: 5px; "
                    >
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            <div class="form-group">
                                <label for="modalPosition">Позиция</label>
                                <div class="searchMenu">
                                    <model-select
                                        id="modalPosition"
                                        v-model="editLinePosition.editableSpare"
                                        :options="sparesData"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            <div class="form-group">
                                <label for="modalPosition">Техника</label>
                                <div class="searchMenu">
                                    <model-select
                                        id="modalVehicle"
                                        v-model="editLinePosition.vehicle"
                                        :options="vehicles"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="form-group">
                                <label for="modalCount">Кол-во</label>
                                <input
                                    id="modalCount"
                                    v-model="editLinePosition.newQuantity"
                                    type="number"
                                    min="1"
                                    class="form-control"
                                    autocomplete="off"
                                    @keyup.enter="editPosition()"
                                >
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="comment">Комментарий</label>
                                <textarea
                                    id="comment"
                                    v-model="editLinePosition.comment"
                                    class="form-control"
                                    autocomplete="off"
                                    @keyup.enter="editPosition()"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                        @click="closeEditForm()"
                    >
                        Закрыть
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="editPosition()"
                    >
                        Сохранить изменения
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end of modal -->
</template>
