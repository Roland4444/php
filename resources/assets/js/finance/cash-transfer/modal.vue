<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';
    import Modal from '../../common/modal.vue';

    export default {
        name: 'CashTransferModal',
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
                formData.append('source', this.item.source.id);
                formData.append('dest', this.item.dest.id);
                formData.append('money', this.item.money);

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
                <label for="source">Источник</label>
                <select
                    id="source"
                    v-model="item.source.id"
                    class="form-control"
                >
                    <option
                        v-for="source in banks"
                        :key="source.id"
                        :value="source.id"
                    >
                        {{ source.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="dest">Получатель</label>
                <select
                    id="dest"
                    v-model="item.dest.id"
                    class="form-control"
                >
                    <option
                        v-for="dest in banks"
                        :key="dest.id"
                        :value="dest.id"
                    >
                        {{ dest.name }}
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
