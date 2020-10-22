<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker';
    import Modal from '../../common/modal.vue';

    export default {
        name: 'TransportIncomeModal',
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
        },
        methods: {
            save() {
                const formData = new FormData();
                formData.append('id', this.item.id);
                formData.append('date', moment(this.item.date).format('YYYY-MM-DD'));
                formData.append('money', this.item.money);

                this.$http.post(`${this.apiUrl}/save`, formData).then(() => {
                    this.$emit('onEntitySaved');
                }, (response) => {
                    this.$notify({
                        title: 'Ошибка',
                        text: response.body.error,
                        type: 'error',
                    });
                });
            },
        },
    };
</script>

<template>
    <modal @onModalOk="save">
        <form>
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
                <label for="money">Сумма</label>
                <input
                    id="money"
                    v-model="item.money"
                    type="number"
                    class="form-control"
                    autocomplete="off"
                >
            </div>
        </form>
    </modal>
</template>
