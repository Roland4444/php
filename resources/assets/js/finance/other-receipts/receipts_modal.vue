<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';
    import Modal from '../../common/modal.vue';

    export default {
        name: 'ReceiptsModal',
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
                formData.append('bank', this.item.bank.id);
                formData.append('money', this.item.money);
                formData.append('comment', this.item.comment);
                formData.append('inn', this.item.inn);
                formData.append('sender', this.item.sender);
                formData.append('overdraft', this.item.overdraft ? 1 : 0);

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
                if (!this.item.comment) {
                    errorFields.push('Комментарий');
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
                <label for="money">
                    Сумма
                </label>
                <input
                    id="money"
                    v-model="item.money"
                    required
                    type="number"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <label for="comment">
                    Комментарий
                </label>
                <input
                    id="comment"
                    v-model="item.comment"
                    type="text"
                    class="form-control"
                >
            </div>

            <div class="form-group">
                <label for="inn">
                    ИНН
                </label>
                <input
                    id="inn"
                    v-model="item.inn"
                    type="number"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <label for="sender">
                    Отправитель
                </label>
                <input
                    id="sender"
                    v-model="item.sender"
                    type="text"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input
                            v-model="item.overdraft"
                            type="checkbox"
                        >
                        Овердрафт
                    </label>
                </div>
            </div>
        </form>
    </modal>
</template>
