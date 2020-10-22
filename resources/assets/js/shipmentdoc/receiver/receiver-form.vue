<script>
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'ReceiverForm',

        components: {
            DatePicker,
        },

        mixins: [httpRequestWrapperMixin],

        props: {
            saveReceiverUrl: {
                type: String,
                default: '',
            },
            type: {
                type: String,
                default: 'separate',
            },
            backUrl: {
                type: String,
                default: '',
            },
            receiver: {
                type: Object,
                default: () => ({
                    name: '',
                    legal_name: '',
                    inn: '',
                    address: '',
                    phone: '',
                    account: '',
                    contract_title: '',
                    contract_date: '',
                }),
            },
        },

        data() {
            return {
                newReceiver: this.receiver,
                waitWhileSaving: false,
                momentPlugin: Moment,
            };
        },

        methods: {
            saveReceiver() {
                this.newReceiver.contract_date = this.momentPlugin(this.newReceiver.contract_date).format('YYYY-MM-DD');

                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.httpPostRequest(this.saveReceiverUrl, this.newReceiver).then((response) => {
                            this.clearFields();

                            const newReceiver = JSON.parse(response.body.data);

                            this.$emit('onReceiverSave', newReceiver);

                            this.backToIndexAfterSave();
                        });
                    }
                });
            },

            backToIndex() {
                window.location.href = this.backUrl;
            },

            backToIndexAfterSave() {
                if (this.type === 'separate') {
                    window.location.href = this.backUrl;
                }
            },

            clearFields() {
                this.newReceiver.name = '';
                this.newReceiver.legal_name = '';
                this.newReceiver.inn = '';
                this.newReceiver.address = '';
                this.newReceiver.phone = '';
                this.newReceiver.account = '';
                this.newReceiver.contract_title = '';
                this.newReceiver.contract_date = '';
            },
        },
    };
</script>

<template>
    <div>
        <notifications />
        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="payerName">Наименование:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="payerName"
                    v-model="newReceiver.name"
                    v-validate="'required'"
                    type="text"
                    class="form-control"
                    name="'наименование грузополучателя'"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'наименование грузополучателя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="payerLegalName">Юр. наименование:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="payerLegalName"
                    v-model="newReceiver.legal_name"
                    v-validate="'required'"
                    type="text"
                    class="form-control"
                    name="'юр.наименование грузополучателя'"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'юр.наименование грузополучателя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="payerAddress">Адрес:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="payerAddress"
                    v-model="newReceiver.address"
                    v-validate="'required'"
                    type="text"
                    class="form-control"
                    name="'адрес грузополучателя'"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'адрес грузополучателя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="payerPhone">Телефон:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="payerPhone"
                    v-model="newReceiver.phone"
                    v-validate="'required'"
                    type="text"
                    class="form-control"
                    name="'телефон грузополучателя'"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'телефон грузополучателя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="payerContractNumber">Номер договора:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="payerContractNumber"
                    v-model="newReceiver.contract_title"
                    v-validate="'required'"
                    type="text"
                    class="form-control"
                    name="'номер договора грузополучателя'"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'номер договора грузополучателя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label>Дата договора:</label>
            </div>
            <div class="col-md-10 mb-10">
                <date-picker
                    v-model="newReceiver.contract_date"
                    v-validate="'required'"
                    name="'дата договора грузополучателя'"
                    data-vv-validate-on="change|blur"
                    input-class="form-control"
                    format="YYYY-MM-DD"
                    lang="ru"
                />
                <span class="validation-error-message">{{ errors.first("'дата договора грузополучателя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="payerInn">ИНН:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="payerInn"
                    v-model="newReceiver.inn"
                    v-validate="'required'"
                    type="text"
                    class="form-control"
                    name="'инн грузополучателя'"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'инн грузополучателя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="payerAccount">Счет:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="payerAccount"
                    v-model="newReceiver.account"
                    v-validate="'required'"
                    type="text"
                    class="form-control"
                    name="'счет грузополучателя'"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'счет грузополучателя'") }}</span>
            </div>
        </div>

        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-primary"
                :disabled="waitWhileSaving"
                @click="saveReceiver"
            >
                Сохранить
            </button>
            <button
                v-if="type === 'wizard'"
                type="button"
                class="btn btn-default"
                data-dismiss="modal"
            >
                Закрыть
            </button>
            <button
                v-else
                type="button"
                class="btn btn-default"
                @click="backToIndex"
            >
                Назад
            </button>
        </div>
    </div>
</template>

<style>
    .driver-inputs {
        margin-bottom: 20px;
    }

    .validation-error-message {
        color: #ff0000;
        font-size: 10pt
    }
</style>
