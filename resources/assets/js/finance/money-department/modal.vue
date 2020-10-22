<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';
    import Modal from '../../common/modal.vue';

    export default {
        name: 'MoneyDepartmentModal',
        components: {
            Modal,
            'date-picker': DatePicker,
        },
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
            item: {
                type: Object,
                required: true,
            },
            banks: {
                type: Array,
                required: true,
            },
            departments: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
            };
        },
        methods: {
            save() {
                if (!this.validate()) {
                    return;
                }

                document.getElementById('modalCancelBtn').click();
                const formData = new FormData();
                formData.append('id', this.item.id);
                formData.append('date', moment(this.item.date).format('YYYY-MM-DD'));
                formData.append('department', this.item.department.id);
                formData.append('bank', this.item.bank.id);
                formData.append('money', this.item.money);
                formData.append('verified', this.item.verified ? 1 : 0);

                this.$http.post(`${this.apiUrl}/save`, formData).then(() => {
                    this.$emit('onEntitySaved');
                }, (response) => {
                    this.showError(response.body.error);
                });
            },
            validate() {
                const errorFields = [];
                if (!this.item.date) {
                    errorFields.push('Дата');
                }
                if (!this.item.money) {
                    errorFields.push('Сумма');
                }
                if (errorFields.length > 0) {
                    this.showError(`Поля ${errorFields.join()} заполнены не корректно`);
                    return false;
                }
                return true;
            },
            showError(msg) {
                this.$notify({
                    title: 'Ошибка',
                    text: msg,
                    type: 'error',
                });
            },
        },
    };
</script>

<template>
    <modal @onModalOk="save">
        <form @keyup.enter="save">
            <div class="form-group">
                <label for="date">Дата</label>
                <date-picker
                    id="date"
                    v-model="item.date"
                    input-class="form-control"
                    format="YYYY-MM-DD"
                    lang="ru"
                />
            </div>
            <div class="form-group">
                <label for="department">Подразделение</label>
                <select
                    id="department"
                    v-model="item.department.id"
                    class="form-control"
                >
                    <option
                        v-for="department in departments"
                        :key="department.id"
                        :value="department.id"
                    >
                        {{ department.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="bank">Счет</label>
                <select
                    id="bank"
                    v-model="item.bank"
                    class="form-control"
                >
                    <option
                        v-for="bank in banks"
                        :key="'modalBank' + bank.id"
                        :value="bank"
                    >
                        {{ bank.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="money">Сумма</label>
                <input
                    id="money"
                    v-model="item.money"
                    required
                    type="number"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input
                            v-model="item.verified"
                            type="checkbox"
                        >
                        Проверено
                    </label>
                </div>
            </div>
        </form>
    </modal>
</template>
