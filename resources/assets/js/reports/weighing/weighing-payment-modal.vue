<script>
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'WeighingPaymentModal',
        mixins: [httpRequestWrapperMixin],
        props: {
            payableWeighing: {
                type: Object,
                required: true,
            },
            payUrl: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                diamondPayment: 0,
                waitWhileSaving: false,
            };
        },
        methods: {
            pay() {
                const url = this.payUrl.replace('0', this.payableWeighing.departmentId);

                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.httpPostRequest(url, {
                            departmentId: this.payableWeighing.departmentId,
                            money: this.payableWeighing.payableAmount,
                            weighingId: this.payableWeighing.id,
                            customerId: this.payableWeighing.customer.value,
                            diamond: this.diamondPayment,
                        }).then(() => {
                            this.$refs.closeModalButton.click();
                            this.changeDataAfterUpdate(this.payableWeighing);
                        });
                    }
                });
            },
            changeDataAfterUpdate(payableWeighing) {
                this.$emit('onPaymentUpdate', payableWeighing);
            },
        },
    };
</script>
<template>
    <div>
        <div
            id="weighingPaymentModal"
            class="modal fade bs-example-modal-sm"
            tabindex="-1"
            role="dialog"
            aria-labelledby="weighingPaymentModalLabel"
        >
            <div
                class="modal-dialog modal-sm"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span
                                ref="closeModalButton"
                                aria-hidden="true"
                            >
                                &times;
                            </span>
                        </button>
                        <h4
                            id="weighingEditLabel"
                            class="modal-title"
                        >
                            Оплата
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-12">
                                <label for="amount">
                                    Сумма
                                </label>
                                <input
                                    id="amount"
                                    v-model="payableWeighing.payableAmount"
                                    v-validate="'required'"
                                    data-vv-validate-on="change|blur"
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    :name="'оплата'"
                                >
                                <span class="validation-error-message">
                                    {{ errors.first('оплата') }}
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-12">
                                <div class="checkbox">
                                    <label>
                                        <input
                                            v-model="diamondPayment"
                                            type="checkbox"
                                        >
                                        Оплата на карту
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-12">
                                <button
                                    type="button"
                                    class="btn btn-default"
                                    :disabled="waitWhileSaving"
                                    @click="pay"
                                >
                                    Оплатить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
