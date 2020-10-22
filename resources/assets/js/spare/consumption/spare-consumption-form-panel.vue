<script>
    import Moment from 'moment';
    import ConsumptionList from './spare-consumption-list.vue';
    import ConsumptionForm from './spare-consumption-form.vue';
    import ConsumptionModal from './spare-consumption-modal.vue';

    export default {
        components: {
            'spare-list': ConsumptionList,
            'spare-form': ConsumptionForm,
            'spare-modal': ConsumptionModal,
        },
        props: {
            roles: {
                type: Array,
                default() {
                    return [];
                },
            },
            sparesObject: {
                type: Object,
                default() {
                    return {
                        0: {
                            spareUnits: '',
                            spare_id: 0,
                            text: '',
                            total: 0,
                        },
                    };
                },
            },
            vehiclesArray: {
                type: Array,
                default() {
                    return [
                        {
                            text: '',
                            value: '',
                        },
                    ];
                },
            },
            employeesObject: {
                type: Object,
                default() {
                    return {
                        0: {
                            text: '',
                            value: 0,
                        },
                    };
                },
            },
            consumptions: {
                type: Object,
                default() {
                    return {
                        data: [{
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
                                value: 0,
                            },
                        }],
                        date: '',
                        employee: {
                            id: 0,
                            license: '',
                            name: '',
                        },
                        id: 0,
                    };
                },
            },
            cancelUri: {
                type: String,
                default: '',
            },
            saveUri: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                positions: this.consumptions.data ? Object.values(this.consumptions.data) : [],
                spares: this.sparesObject ? this.getTotalSpare() : [],
                employees: this.employeesObject ? Object.values(this.employeesObject) : [],
                vehicles: this.vehiclesArray ? Object.values(this.vehiclesArray) : [],
                editLinePosition: {
                    spare: {
                        value: '',
                    },
                },
                addLinePosition: {
                    vehicle: {
                        value: '',
                        text: '',
                    },
                    comment: '',
                    spare: {
                        value: 0,
                        text: '',
                        spareUnits: '',
                    },
                    quantity: 0,
                },
                editMsg: '',
                addMsg: '',
                msgResponse: '',
                savingEnable: true,
                consumptionId: this.consumptions ? this.consumptions.id : '',
            };
        },
        methods: {
            getTotalSpare() {
                const spares = [];

                Object.keys(this.sparesObject).forEach((key) => {
                    spares.push({
                        value: parseInt(key, 10),
                        text: this.sparesObject[key].text,
                        total: this.sparesObject[key].total,
                        spareUnits: this.sparesObject[key].spareUnits,
                    });
                });

                return spares;
            },
            validateForm(linePosition, isAdding = true) {
                let msg = '';
                this.editMsg = '';
                this.addMsg = '';

                for (const position of this.positions) {
                    if (isAdding) {
                        if (position.spare.value === linePosition.editableSpare.value) {
                            msg = 'Данная позиция уже есть в списке.';
                        }
                    } else if (linePosition.spare.value !== linePosition.editableSpare.value
                        && position.spare.value === linePosition.editableSpare.value) {
                        msg = 'Данная позиция уже есть в списке.';
                    }
                }

                if (!linePosition.spare) {
                    msg = 'Поле "Позиция" не должно быть пустым.';
                }
                if (!linePosition.newQuantity || linePosition.newQuantity === 0) {
                    msg += 'Поле "Количество" должно быть больше нуля';
                }

                const { total } = this.sparesObject[linePosition.spare.value];

                if (isAdding) {
                    if (total > 0 && total < parseFloat(linePosition.newQuantity)) {
                        msg = `&#10008; Превышено количество остатка по данной позиции. Остаток:${total}<br> `;
                    }
                } else {
                    const summedTotal = parseFloat(total) + parseFloat(linePosition.quantity);

                    if (summedTotal > 0 && summedTotal < parseFloat(linePosition.newQuantity)) {
                        msg = `&#10008; Превышено количество остатка по данной позиции. Остаток: ${summedTotal}<br> `;
                    }
                }

                if (msg) {
                    if (isAdding) {
                        this.addMsg = msg;
                    } else {
                        this.editMsg = msg;
                    }
                    return false;
                }
                return true;
            },
            newSpare(addLinePosition) {
                if (!addLinePosition) {
                    return {};
                }
                return {
                    spare: addLinePosition.spare,
                    editableSpare: addLinePosition.spare,
                    quantity: addLinePosition.quantity,
                    comment: addLinePosition.comment,
                    vehicle: addLinePosition.vehicle,
                    spareUnits: addLinePosition.spare.spareUnits,
                };
            },
            addPosition(addLinePosition) {
                const positionToAdd = addLinePosition;
                positionToAdd.editableSpare = addLinePosition.spare;
                positionToAdd.newQuantity = addLinePosition.quantity;

                if (!this.validateForm(positionToAdd, true)) {
                    return;
                }

                this.positions.push(this.newSpare(positionToAdd));
                this.addLinePosition = {
                    vehicle: {
                        value: '',
                        text: '',
                    },
                    comment: '',
                    spare: {
                        value: 0,
                        text: '',
                        spareUnits: '',
                    },
                    quantity: 0,
                };
            },
            delPosition(index) {
                this.positions.splice(index, 1);
            },
            openEditForm(index) {
                if (!this.positions[index].vehicle) {
                    this.positions[index].vehicle = {
                        text: '',
                        value: null,
                    };
                }
                this.editLinePosition = this.positions[index];
                this.editLinePosition.newQuantity = this.positions[index].quantity;
            },
            editPosition(editablePos) {
                if (!this.validateForm(editablePos, false)) {
                    return;
                }
                this.$refs.sparemodal.closeModal();

                // Обновить количество total для выбранной запчасти
                const total = this.editLinePosition.quantity - this.editLinePosition.newQuantity;
                this.sparesObject[editablePos.spare.value].total += total;

                this.editLinePosition.spare = this.editLinePosition.editableSpare;
                this.editLinePosition.quantity = this.editLinePosition.newQuantity;
            },
            validateBeforeSaving() {
                this.msgResponse = '';
                if (this.positions.length < 1) {
                    this.msgResponse = 'Заявка должна содержать как минимум одну позицию заказа';
                    return false;
                }

                if (!this.$refs.form.dateForm || !this.$refs.form.employee) {
                    this.msgResponse = 'Поля Дата, Сотрудник обязательны для заполнения';
                    return false;
                }

                const dateToSave = Moment(this.$refs.form.dateForm);
                const currentDate = Moment();

                if (!this.roles.includes('admin')
                    && !this.roles.includes('officecash')
                    && currentDate.diff(dateToSave, 'days') > 14) {
                    this.msgResponse = 'Дата изменения не должна превышать 14 дней';
                    return false;
                }

                return true;
            },
            save() {
                if (this.validateBeforeSaving()) {
                    this.savingEnable = false;
                    const formData = new FormData();
                    formData.append('positions', JSON.stringify(this.positions));

                    formData.append('date', this.$refs.form.dateForm
                        ? Moment(this.$refs.form.dateForm).format('YYYY-MM-DD')
                        : '');

                    formData.append('consumptionId', this.consumptionId ? this.consumptionId : '');

                    formData.append('employee', this.$refs.form.employee
                        ? this.$refs.form.employee
                        : '');

                    this.$http.post(this.saveUri, formData).then((response) => {
                        if (response.body.status === 'ok') {
                            this.msgResponse = 'Сохранено успешно';
                            window.location.href = this.cancelUri;
                        }
                    }, (response) => {
                        this.msgResponse = response.body.error;
                        this.savingEnable = true;
                    });
                }
            },
        },
    };
</script>
<template>
    <form
        method="post"
        style="margin-left: 20px"
    >
        <spare-modal
            ref="sparemodal"
            :spares="spares"
            :vehicles="vehicles"
            :edit-msg="editMsg"
            :edit-line-position="editLinePosition"
            :positions="positions"
            @editPosition="editPosition"
        />

        <spare-form
            ref="form"
            :employees="employees"
            :roles="roles"
            :vehicles="vehicles"
            :consumptions="consumptions"
            :spares="spares"
            :spares-object="sparesObject"
            :add-msg="addMsg"
            :add-line-position="addLinePosition"
            @addPosition="addPosition"
        />

        <spare-list
            ref="spareListComponent"
            :positions="positions"
            :spares="spares"
            :spares-object="sparesObject"
            @delPosition="delPosition"
            @openEditForm="openEditForm"
        />

        <p
            v-if="msgResponse"
            style="color: red"
        >
            {{ msgResponse }}
        </p>
        <p style="text-align: right; margin-top: 10px;">
            <span
                v-if="positions.length > 0"
                class="btn btn-default"
                @click="save()"
            >
                Сохранить
            </span>
            <a
                :href="cancelUri"
                class="btn btn-default"
            >Отмена</a>
        </p>
    </form>
</template>
