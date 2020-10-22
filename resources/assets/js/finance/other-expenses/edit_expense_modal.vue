<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';
    import { ModelSelect } from 'vue-search-select';
    import Modal from '../../common/modal.vue';

    export default {
        name: 'OtherExpensesModal',
        components: {
            Modal,
            'date-picker': DatePicker,
            'model-select': ModelSelect,
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
            categories: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                id: 0,
                recipient: '',
                inn: '',
                comment: '',
                date: '',
                realdate: '',
                category: '',
                bank: '',
                money: '',
            };
        },
        watch: {
            item: {
                handler() {
                    this.id = this.item.id;
                    this.recipient = this.item.recipient;
                    this.inn = this.item.inn;
                    this.comment = this.item.comment;
                    this.date = moment(this.item.date).format('YYYY-MM-DD');
                    this.realdate = moment(this.item.realdate).format('YYYY-MM-DD');
                    this.category = this.item.category;
                    this.bank = this.item.bank;
                    this.money = this.item.money;
                },
            },
        },
        methods: {
            save() {
                if (!this.validate()) {
                    return;
                }

                const formData = new FormData();
                formData.append('id', this.id);
                formData.append('recipient', this.recipient);
                formData.append('inn', this.inn);
                formData.append('comment', this.comment);
                formData.append('date', moment(this.date).format('YYYY-MM-DD'));
                let realdate = '';
                if (this.realdate && this.realdate !== 'Invalid date') {
                    realdate = moment(this.realdate).format('YYYY-MM-DD');
                }
                formData.append('realdate', realdate);
                formData.append('category', this.category.id);
                formData.append('bank', this.bank.id);
                formData.append('money', this.money);

                this.$http.post(`${this.apiUrl}/save`, formData).then(() => {
                    this.$emit('onEntitySaved');
                    document.getElementById('modalCancelBtn').click();
                }, (response) => {
                    this.showError(response.body.error);
                });
            },
            validate() {
                const errorFields = [];
                if (!this.date) {
                    errorFields.push('Дата');
                }
                if (!this.money) {
                    errorFields.push('Сумма');
                }
                if (!this.comment) {
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
                <label for="recipient">Получатель</label>
                <input
                    id="recipient"
                    v-model="recipient"
                    required
                    type="text"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <label for="date">Дата</label>
                <date-picker
                    id="date"
                    v-model="date"
                    input-class="form-control"
                    format="YYYY-MM-DD"
                    lang="ru"
                />
            </div>
            <div class="form-group">
                <label for="inn">ИНН</label>
                <input
                    id="inn"
                    v-model="inn"
                    required
                    type="number"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <label for="comment">Комментарий</label>
                <input
                    id="comment"
                    v-model="comment"
                    required
                    type="text"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <label for="realdate">Дата расхода</label>
                <date-picker
                    id="realdate"
                    v-model="realdate"
                    input-class="form-control"
                    format="YYYY-MM-DD"
                    lang="ru"
                />
            </div>
            <div class="form-group">
                <label for="category">Категория</label>
                <model-select
                    id="category"
                    v-model="category"
                    class="form-control"
                    :options="categories"
                    placeholder="Категория"
                />
            </div>
            <div class="form-group">
                <label for="bank">Счет</label>
                <select
                    id="bank"
                    v-model="bank.id"
                    class="form-control"
                >
                    <option
                        v-for="editBank in banks"
                        :key="editBank.id"
                        :value="editBank.id"
                    >
                        {{ editBank.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="money">Сумма</label>
                <input
                    id="money"
                    v-model="money"
                    required
                    type="number"
                    class="form-control"
                >
            </div>
        </form>
    </modal>
</template>
