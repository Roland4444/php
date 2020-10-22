<script>
    import Moment from 'moment';
    import TransferList from './spare-transfer-list.vue';
    import TransferForm from './spare-transfer-form.vue';
    import TransferModal from './spare-transfer-modal.vue';

    export default {
        components: {
            'transfer-list': TransferList,
            'transfer-form': TransferForm,
            'transfer-modal': TransferModal,
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
                    return {};
                },
            },
            warehousesObject: {
                type: Object,
                default() {
                    return {};
                },
            },
            transfers: {
                type: Object,
                default() {
                    return {
                        data: [{
                            count: 0,
                            spare: 0,
                        }],
                        date: '',
                        transferId: 0,
                        warehouse: 0,
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
                positions: this.transfers.data ? Object.values(this.transfers.data) : [],
                spares: this.sparesObject ? this.getTotalSpare() : [],
                warehouses: this.warehousesObject ? Object.values(this.warehousesObject) : [],
                editLinePosition: {},
                addLinePosition: this.newSpare(),
                editMsg: '',
                addMsg: '',
                msgResponse: '',
                savingEnable: true,
                transferId: this.transfers ? this.transfers.transferId : '',
            };
        },
        methods: {
            getTotalSpare() {
                const spares = [];

                Object.keys(this.sparesObject).forEach((key) => {
                    spares.push({
                        value: this.sparesObject[key].spare_id,
                        text: this.sparesObject[key].text,
                    });
                });

                return spares;
            },
            validateForm(linePosition, isAdding = true) {
                let msg = '';
                this.editMsg = '';
                this.addMsg = '';

                Object.keys(this.positions).forEach((key) => {
                    if (isAdding) {
                        if (this.positions[key].spare === linePosition.spare) {
                            msg = 'Данная позиция уже есть в списке.';
                        }
                    } else if (this.positions[key].spare === linePosition.spare
                        && linePosition.idList !== parseInt(key, 10)) {
                        msg = 'Данная позиция уже есть в списке.';
                    }
                });

                if (!linePosition.spare) {
                    msg = 'Поле "Позиция" не должно быть пустыми.';
                }
                if (!linePosition.count || linePosition.count === 0) {
                    msg += 'Поле "Количество" должно быть больше нуля';
                }
                if (linePosition.count && typeof Number(linePosition.count) !== 'number') {
                    msg += 'Поле "Количество" должно быть числом';
                }

                let total = linePosition.spare
                    ? this.sparesObject[linePosition.spare].total
                    : '';

                if (this.transfers.data && this.transfers.data[0]) {
                    if (this.transfers.data[0].spare === this.editLinePosition.spare) {
                        total = total * 1 + this.transfers.data[0].count * 1;
                    }
                }

                if (total !== '' && total < linePosition.count) {
                    msg = `Превышено количество остатка по данной позиции. Остаток: ${total}`;
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
                    count: addLinePosition.count,
                };
            },
            addPosition(addLinePosition) {
                if (!this.validateForm(addLinePosition, true)) {
                    return;
                }

                this.positions.push(this.newSpare(addLinePosition));
                this.addLinePosition = {};
            },
            delPosition(index) {
                this.positions.splice(index, 1);
            },
            openEditForm(index) {
                this.editLinePosition = {
                    idList: index,
                    spare: this.positions[index].spare,
                    count: this.positions[index].count,
                };
            },
            editPosition(editLinePosition) {
                if (!this.validateForm(editLinePosition, false)) {
                    return;
                }

                this.positions[editLinePosition.idList] = this.newSpare(editLinePosition);
                this.$refs.spareListComponent.$forceUpdate();
                this.closeEditForm();
            },
            closeEditForm() {
                this.editLinePosition = {};
            },
            save() {
                this.msgResponse = '';
                if (this.positions.length < 1) {
                    this.msgResponse = 'Заявка должна содержать как минимум одну позицию заказа';
                    return;
                }

                if (!this.$refs.form.dateForm || !this.$refs.form.warehouse) {
                    this.msgResponse = 'Поле Дата и Склад назначения обязательны для заполнения';
                    return;
                }

                if (!this.hasEnableSave()) {
                    return;
                }

                this.savingEnable = false;
                const formData = new FormData();
                formData.append('positions', JSON.stringify(this.positions));

                formData.append('date', this.$refs.form.dateForm
                    ? Moment(this.$refs.form.dateForm).format('YYYY-MM-DD')
                    : '');

                formData.append('transferId', this.transferId ? this.transferId : '');

                formData.append('warehouse', this.$refs.form.warehouse
                    ? this.$refs.form.warehouse
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
            },
            hasEnableSave() {
                return this.positions.length > 0
                    && this.isEnableSave()
                    && this.$refs.form.dateForm
                    && this.$refs.form.warehouse;
            },
            isEnableSave() {
                if (!this.roles.includes('admin') && !this.roles.includes('officecash')) {
                    const date = this.transfers.date ? this.transfers.date : Moment(Date.now()).format('YYYY-MM-DD');
                    const currentDate = Moment().format('YYYY-MM-DD');
                    if (date !== currentDate) {
                        return false;
                    }
                }
                return this.savingEnable;
            },
        },
    };
</script>
<template>
    <form
        method="post"
        style="margin-left: 20px"
    >
        <transfer-modal
            :spares="spares"
            :edit-msg="editMsg"
            :edit-line-position="editLinePosition"
            @closeEditForm="closeEditForm"
            @editPosition="editPosition"
        />

        <transfer-form
            ref="form"
            :roles="roles"
            :warehouses="warehouses"
            :transfers="transfers"
            :spares="spares"
            :spares-object="sparesObject"
            :add-msg="addMsg"
            :add-line-position="addLinePosition"
            @addPosition="addPosition"
        />

        <transfer-list
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
                v-if="Object.values(positions).length > 0 && isEnableSave()"
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
