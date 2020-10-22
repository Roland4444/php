<script>
    import { ModelListSelect } from 'vue-search-select';
    import ReceiverModal from './receiver-modal.vue';

    export default {
        name: 'ReceiverList',

        components: {
            ModelListSelect,
            ReceiverModal,
        },

        props: {
            chosenReceiver: {
                type: Object,
                default() {
                    return {
                        id: null,
                        name: '',
                        legal_name: '',
                        address: '',
                        phone: '',
                        contract_number: '',
                        contract_date: '',
                        inn: '',
                        account: '',
                    };
                },
            },
            chosenPayer: {
                type: Object,
                default() {
                    return {
                        id: null,
                        name: '',
                        legal_name: '',
                        address: '',
                        phone: '',
                        contract_number: '',
                        contract_date: '',
                        inn: '',
                        account: '',
                    };
                },
            },
            receiversProp: {
                type: Array,
                default() {
                    return [{
                        account: '',
                        address: '',
                        contract_date: '',
                        contract_title: '',
                        created_at: '',
                        id: 0,
                        inn: '',
                        legal_name: '',
                        name: '',
                        phone: '',
                        updated_at: '',

                    }];
                },
            },
            saveReceiverUrl: {
                type: String,
                default: '',
            },
        },

        data() {
            return {
                receivers: this.receiversProp,

                // receiver | payer
                entityType: 'receiver',

                shipmentDoc: {
                    receiver: this.chosenReceiver,
                    payer: this.chosenPayer,
                },
            };
        },

        watch: {
            shipmentDoc: {
                handler() {
                    this.$emit('onReceiverChange', {
                        receiver: this.shipmentDoc.receiver,
                        payer: this.shipmentDoc.payer,
                    });
                },
                deep: true,
            },
            // eslint-disable-next-line
            'shipmentDoc.receiver': function () {
                this.shipmentDoc.payer = this.shipmentDoc.receiver;
            },
        },

        inject: ['parentValidator'],

        created() {
            this.$validator = this.parentValidator;
        },

        methods: {
            makePayerEqualsReceiver() {
                this.shipmentDoc.payer = this.shipmentDoc.receiver;
            },

            onReceiverAdd(newReceiver) {
                this.receivers.push(newReceiver);
                this.shipmentDoc.receiver = newReceiver;
            },

            onPayerAdd(newPayer) {
                this.receivers.push(newPayer);
                this.shipmentDoc.payer = newPayer;
            },
        },
    };
</script>

<template>
    <div>
        <h4>Грузополучатель</h4>

        <div class="row driver-inputs">
            <div class="col-md-4 mb-4">
                <model-list-select
                    id="receiverSelect"
                    v-model="shipmentDoc.receiver"
                    :list="receivers"
                    option-value="id"
                    option-text="name"
                />
            </div>
            <input
                v-model="shipmentDoc.receiver.name"
                v-validate="'required'"
                name="'грузополучатель'"
                type="hidden"
            >
            <span class="validation-error-message">{{ errors.first("'грузополучатель'") }}</span>
            <div class="col-md-2 mb-2">
                <button
                    data-toggle="modal"
                    data-target="#receiverAddModal"
                    class="btn btn-default"
                >
                    Добавить
                </button>
            </div>
        </div>

        <span v-if="shipmentDoc.receiver.name">
            <div class="row driver-inputs">
                <div class="col-md-2 mb-2">
                    <label>Наименование:</label>
                </div>
                <div class="col-md-10 mb-10">
                    <p>{{ shipmentDoc.receiver.name }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-2 mb-2">
                    <label>Юр. наименование:</label>
                </div>
                <div class="col-md-10 mb-10">
                    <p>{{ shipmentDoc.receiver.legal_name }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-1 mb-1">
                    <label>Адрес:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.receiver.address }}</p>
                </div>
                <div class="col-md-1 mb-1">
                    <label>Телефон:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.receiver.phone }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-1 mb-1">
                    <label>Договор:</label>
                </div>
                <div class="col-md-11 mb-11">
                    <p>{{ shipmentDoc.receiver.contract_title }} от {{ shipmentDoc.receiver.contract_date }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-1 mb-1">
                    <label>ИНН:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.receiver.inn }}</p>
                </div>
                <div class="col-md-1 mb-1">
                    <label>Счет:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.receiver.account }}</p>
                </div>
            </div>

            <hr>

            <h4>Плательщик</h4>
            <div class="row driver-inputs">
                <div class="col-md-4 mb-4">
                    <model-list-select
                        id="payerSelect"
                        v-model="shipmentDoc.payer"
                        :list="receivers"
                        option-value="id"
                        option-text="name"
                    />
                </div>
                <input
                    v-model="shipmentDoc.payer.name"
                    v-validate="'required'"
                    name="'плательщик'"
                    type="hidden"
                >
                <span class="validation-error-message">{{ errors.first("'плательщик'") }}</span>
                <div class="col-md-2 mb-2">
                    <button
                        data-toggle="modal"
                        data-target="#receiverAddModal"
                        class="btn btn-default"
                        @click="entityType = 'payer'"
                    >Добавить</button>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-2 mb-2">
                    <label>Наименование:</label>
                </div>
                <div class="col-md-10 mb-10">
                    <p>{{ shipmentDoc.payer.name }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-2 mb-2">
                    <label>Юр. наименование:</label>
                </div>
                <div class="col-md-10 mb-10">
                    <p>{{ shipmentDoc.payer.legal_name }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-1 mb-1">
                    <label>Адрес:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.payer.address }}</p>
                </div>
                <div class="col-md-1 mb-1">
                    <label>Телефон:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.payer.phone }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-1 mb-1">
                    <label>Договор:</label>
                </div>
                <div class="col-md-11 mb-11">
                    <p>{{ shipmentDoc.payer.contract_title }} от {{ shipmentDoc.payer.contract_date }}</p>
                </div>
            </div>

            <div class="row driver-inputs">
                <div class="col-md-1 mb-1">
                    <label>ИНН:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.payer.inn }}</p>
                </div>
                <div class="col-md-1 mb-1">
                    <label>Счет:</label>
                </div>
                <div class="col-md-5 mb-5">
                    <p>{{ shipmentDoc.payer.account }}</p>
                </div>
            </div>
        </span>

        <receiver-modal
            :entity-type="entityType"
            :save-receiver-url="saveReceiverUrl"
            @onReceiverAdd="onReceiverAdd"
            @onPayerAdd="onPayerAdd"
        />
    </div>
</template>
