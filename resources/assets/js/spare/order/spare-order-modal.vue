<script>
    export default {
        props: {
            editMsg: {
                type: String,
                default: '',
            },
            editablePosition: {
                type: Object,
                default() {
                    return {
                        count: '',
                        countInOrder: '',
                        countRestForOrder: '',
                        date: '',
                        isComposite: 0,
                        itemId: 0,
                        key: 0,
                        nameSpare: '',
                        number: 0,
                        planningItemId: 0,
                        price: '',
                        spareId: 0,
                        spareUnits: '',
                        subCount: null,
                    };
                },
            },
        },
        data() {
            return {
                msg: '',
            };
        },
        methods: {
            closeEditForm() {
                this.msg = '';
                this.editablePosition.count = this.editablePosition.countRestForOrder;
                this.editablePosition.subCount = 1;
                this.$emit('closeEditForm');
            },
            validateIsNumber(data, msg, returnData = '') {
                if (typeof (Number(data)) !== 'number') {
                    this.msg = `${this.msg} ${msg} <br>`;
                    return returnData || '';
                }
                return data;
            },
            validateIsNotZero(data, msg, returnData = '') {
                if (data === 0) {
                    this.msg = `${this.msg} ${msg} <br>`;
                    return returnData || '';
                }
                return data;
            },
            calcMaxCount() {
                if (!this.editablePosition.isComposite) {
                    if (this.editablePosition.count > this.editablePosition.countRestForOrder) {
                        this.msg = `${this.msg} Не должно превышать количество указанное в плане`;
                        this.editablePosition.count = this.editablePosition.countRestForOrder;
                    }
                    return this.editablePosition.countRestForOrder;
                }

                const countInWindow = this.editablePosition.subCount * this.editablePosition.count;
                if (countInWindow <= this.editablePosition.countRestForOrder) {
                    return countInWindow;
                }
                const surplus = countInWindow - this.editablePosition.countRestForOrder;

                if (surplus < this.editablePosition.subCount) {
                    return countInWindow;
                }

                this.msg = `${this.msg} Не должно превышать количество указанное в плане`;
                this.editablePosition.subCount = this.editablePosition.countRestForOrder;
                this.editablePosition.count = 1;
                return this.editablePosition.countRestForOrder;
            },
            validateForm() {
                this.msg = '';

                if (this.editablePosition.isComposite) {
                    this.editablePosition.subCount = this.validateIsNotZero(
                        this.editablePosition.subCount,
                        'Количество единиц в одной упаковке должно быть больше нуля',
                        1,
                    );
                    this.editablePosition.subCount = this.validateIsNumber(
                        this.editablePosition.subCount,
                        'Количество единиц в одной упаковке должно быть числом ',
                        1,
                    );
                }

                this.editablePosition.count = this.validateIsNumber(
                    this.editablePosition.count,
                    'Количество должно быть числом',
                    this.editablePosition.countRestForOrder,
                );
                this.editablePosition.count = this.validateIsNotZero(
                    this.editablePosition.count,
                    'Количество должно быть больше нуля',
                    this.editablePosition.countRestForOrder,
                );

                if (this.editablePosition.countRestForOrder !== '') {
                    this.calcMaxCount();
                }

                this.editablePosition.price = this.validateIsNotZero(
                    this.editablePosition.price,
                    'Цена должна быть больше нуля',
                );
                this.editablePosition.price = this.validateIsNumber(
                    this.editablePosition.price,
                    'Цена должна быть числом',
                );
            },
            editPosition() {
                this.validateForm();
                if (this.msg) {
                    return;
                }
                this.$emit('editPosition', this.editablePosition);
            },
        },
    };
</script>
<template>
    <div
        v-if="Object.entries(editablePosition).length !== 0"
        id="modalCenter"
    >
        <div
            id="modalWindow"
            class="modalWindow"
        >
            <div class="modal-backdrop fade in" />
            <div
                id="myModal"
                class="modal fade in"
                tabindex="-1"
                role="dialog"
                aria-labelledby="myModalLabel"
                aria-hidden="false"
                style="display: block;"
                @click="closeEditForm()"
            />
            <div
                class="modal-dialog"
                style="z-index: 1050"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-hidden="true"
                            @click="closeEditForm()"
                        >
                            ×
                        </button>
                        <h4
                            id="myLargeModalLabel"
                            class="modal-title"
                            style="text-align: left;"
                        >
                            Позиция {{ editablePosition.nameSpare }}
                            <span v-if="editablePosition.number">
                                плана №{{ editablePosition.number }} от {{ editablePosition.date }}
                            </span>
                        </h4>
                        <p
                            v-if="msg"
                            style="color: red; margin-top: 15px; margin-bottom: 0;"
                        >
                            {{ msg }}
                        </p>
                    </div>
                    <div
                        id="modal-body"
                        class="modal-body"
                    >
                        <div
                            v-if="editablePosition.isComposite"
                            class="row"
                            style="margin-top: 5px;"
                        >
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="modalSubCount">Количество единиц в одной упаковке</label>
                                    <input
                                        id="modalSubCount"
                                        v-model="editablePosition.subCount"
                                        type="number"
                                        class="form-control"
                                        autocomplete="off"
                                        @keyup.enter="editPosition()"
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            class="row"
                            style="margin-top: 5px; "
                        >
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="modalCount">Количество</label>
                                    <input
                                        id="modalCount"
                                        v-model="editablePosition.count"
                                        type="number"
                                        class="form-control"
                                        autocomplete="off"
                                        autofocus
                                        @keyup.enter="editPosition()"
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            class="row"
                            style="margin-top: 5px; "
                        >
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="price">Цена за комплект (упаковку)</label>
                                    <input
                                        id="price"
                                        v-model="editablePosition.price"
                                        type="number"
                                        class="form-control"
                                        autocomplete="off"
                                        autofocus
                                        @keyup.enter="editPosition()"
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
                            @click="closeEditForm()"
                        >
                            Закрыть
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="editPosition()"
                        >
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
