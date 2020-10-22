<script>
    import Moment from 'moment';
    import PlanningList from './spare-planning-list.vue';
    import PlanningForm from './spare-planning-form.vue';
    import PlanningModal from './spare-planning-modal.vue';

    export default {
        components: {
            'spare-list': PlanningList,
            'spare-form': PlanningForm,
            'spare-modal': PlanningModal,
        },
        props: {
            sparesObject: {
                type: Object,
                default() {
                    return {
                        isComposite: false,
                        text: '',
                        units: '',
                        value: 0,
                    };
                },
            },
            planning: {
                type: Object,
                default() {
                    return {
                        comment: '',
                        data: [{
                            countInPlanning: '',
                            countOrdered: 0,
                            id: 0,
                            isComposite: 0,
                            nameSpare: '',
                            planningItemId: 0,
                            spareId: 0,
                            spareUnits: '',
                        }],
                        date: '',
                        employee: '',
                        number: 0,
                        status: {},
                        vehicle: '',
                    };
                },
            },
            vehiclesObject: {
                type: Array,
                default() {
                    return {
                        0: {
                            text: '',
                            value: '',
                        },
                    };
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
            cancelUri: {
                type: String,
                default: '',
            },
            saveUri: {
                type: String,
                default: '',
            },
            accesses: {
                type: Object,
                default() {
                    return {};
                },
            },
        },
        data() {
            return {
                positions: this.planning.data ? this.planning.data : [],
                spares: this.sparesObject ? Object.values(this.sparesObject) : [],
                employees: this.employeesObject ? Object.values(this.employeesObject) : [],
                vehicles: this.vehiclesObject ? Object.values(this.vehiclesObject) : [],
                editLinePosition: {},
                addLinePosition: this.newSpare(),
                editMsg: '',
                addMsg: '',
                msgResponse: '',
                saving: true,
            };
        },
        methods: {
            validateForm(linePosition, isAdding = true) {
                let msg = '';
                this.editMsg = '';
                this.addMsg = '';

                for (const key in this.positions) {
                    if (isAdding) {
                        if (this.positions[key].id === linePosition.spare) {
                            msg = 'Данная позиция уже есть в списке.';
                        }
                    } else if (this.positions[key].id === linePosition.spare
                        && linePosition.idList !== parseInt(key, 10)) {
                        msg = 'Данная позиция уже есть в списке.';
                    }
                }

                if (!linePosition.spare) {
                    msg = 'Поле "Позиция" не должно быть пустыми.';
                }
                if (!linePosition.count || linePosition.count === 0) {
                    msg += 'Поле "Количество" должно быть больше нуля';
                }
                if (linePosition.count && typeof Number(linePosition.count) !== 'number') {
                    msg += 'Поле "Количество" должно быть числом';
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
            newSpare(id = '', countInPlanning = '') {
                return { id, countInPlanning };
            },
            addPosition(addLinePosition) {
                if (!this.validateForm(addLinePosition, true)) {
                    return;
                }

                this.positions.push(this.newSpare(addLinePosition.spare, addLinePosition.count));
                this.addLinePosition = this.newSpare();
            },
            delPosition(index) {
                this.positions.splice(index, 1);
            },
            newEditLine(idList, spare, count) {
                return { idList, spare, count };
            },
            openEditForm(index) {
                this.editLinePosition = this.newEditLine(
                    index, this.positions[index].id,
                    this.positions[index].countInPlanning,
                );
            },
            editPosition(editLinePosition) {
                if (!this.validateForm(editLinePosition, false)) {
                    return;
                }

                this.positions[editLinePosition.idList] = this.newSpare(
                    editLinePosition.spare,
                    editLinePosition.count,
                );
                this.$refs.spareListComponent.$forceUpdate();

                this.closeEditForm();
            },
            closeEditForm() {
                this.editLinePosition = {};
            },
            save() {
                this.msgResponse = '';
                this.saving = false;
                if (this.positions.length < 1) {
                    this.msgResponse = 'Заявка должна содержать как минимум одну позицию заказа';
                    return;
                }

                const formData = new FormData();
                formData.append('positions', JSON.stringify(this.positions));
                formData.append('date', this.$refs.form.dateForm
                    ? Moment(this.$refs.form.dateForm).format('YYYY-MM-DD')
                    : '');

                formData.append('comment', this.$refs.form.comment
                    ? this.$refs.form.comment
                    : '');

                formData.append('planningNumber', this.planning.number
                    ? this.planning.number
                    : '');

                formData.append('status', this.planning.status
                    ? this.planning.status
                    : '');

                formData.append('employee', this.$refs.form.employee
                    ? this.$refs.form.employee
                    : '');

                formData.append('vehicle', this.$refs.form.vehicle
                    ? this.$refs.form.vehicle
                    : '');

                this.$http.post(this.saveUri, formData).then((response) => {
                    if (response.body.status === 'ok') {
                        this.msgResponse = 'Сохранено успешно';
                        window.location.href = this.cancelUri;
                    }
                }, (response) => {
                    this.msgResponse = response.body.error;
                });
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
            :spares="spares"
            :edit-msg="editMsg"
            :edit-line-position="editLinePosition"
            @closeEditForm="closeEditForm"
            @editPosition="editPosition"
        />

        <spare-form
            ref="form"
            :planning="planning"
            :spares="spares"
            :employees="employees"
            :vehicles="vehicles"
            :add-msg="addMsg"
            :add-line-position="addLinePosition"
            :accesses="accesses"
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
                v-if="positions.length > 0 && saving"
                class="btn btn-default"
                @click="save()"
            >Сохранить</span>
            <a
                :href="cancelUri"
                class="btn btn-default"
            >Отмена</a>
        </p>
    </form>
</template>
