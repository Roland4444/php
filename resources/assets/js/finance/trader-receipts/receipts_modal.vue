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
            traders: {
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
                formData.append('trader', this.item.trader.id);
                formData.append('bank', this.item.bank.id);
                formData.append('money', this.item.money);
                formData.append('type', this.item.type);

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
                <label for="trader">Трейдер</label>
                <select
                    id="trader"
                    v-model="item.trader.id"
                    class="form-control"
                >
                    <option
                        v-for="(trader, index) in traders"
                        :key="index"
                        :value="trader.id"
                    >
                        {{ trader.name }}
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
                <label for="type">Тип</label>
                <select
                    id="type"
                    v-model="item.type"
                    class="form-control"
                >
                    <option value="black">
                        Черный
                    </option>
                    <option value="color">
                        Цветной
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
        </form>
    </modal>
</template>
