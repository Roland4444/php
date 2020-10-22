<script>
    import DatePicker from 'vue2-datepicker';
    import { ModelListSelect } from 'vue-search-select';
    import Moment from 'moment';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        components: {
            DatePicker,
            ModelListSelect,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            sellers: {
                type: Array,
                default() {
                    return [{
                        id: 0,
                        name: '',
                    }];
                },
            },
            apiUrl: {
                type: String,
                default: '',
            },
            sellerReturnItem: {
                type: Object,
                default() {
                    return {
                        id: null,
                        money: null,
                        date: new Date(),
                        seller: null,
                    };
                },
            },
        },
        methods: {
            save() {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.sellerReturnItem.date = Moment(this.sellerReturnItem.date)
                            .format('YYYY-MM-DD');

                        this.httpPostRequest(`${this.apiUrl}/save`, this.sellerReturnItem)
                            .then((response) => {
                                if (!this.sellerReturnItem.id) {
                                    this.$emit('onSellerReturnAdd', response.data.seller);
                                }

                                this.$refs.closeModalButton.click();
                            });
                    }
                });
            },
        },
    };
</script>

<template>
    <div
        id="sellerReturnFormModal"
        class="modal fade bs-example-modal-md"
        tabindex="-1"
        role="dialog"
        aria-labelledby="sellerReturnFormModalLabel"
    >
        <div
            class="modal-dialog modal-md"
            role="document"
        >
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Создать возврат поставщика</h4>
                    <div class="row driver-inputs rows-padding">
                        <div class="col-md-2 mb-2">
                            <label for="sellerReturnDate">Дата:</label>
                        </div>
                        <div class="col-md-10 mb-10">
                            <date-picker
                                id="sellerReturnDate"
                                v-model="sellerReturnItem.date"
                                v-validate="'required'"
                                input-class="form-control"
                                format="YYYY-MM-DD"
                                lang="ru"
                                name="'Дата'"
                                data-vv-validate-on="change|blur"
                            />
                            <span class="validation-error-message">{{ errors.first("'Дата'") }}</span>
                        </div>
                    </div>
                    <div class="row driver-inputs rows-padding">
                        <div class="col-md-2 mb-2">
                            <label for="sellerForReturn">Поставщик:</label>
                        </div>
                        <div class="col-md-10 mb-10">
                            <model-list-select
                                id="sellerForReturn"
                                v-model="sellerReturnItem.seller"
                                v-validate="'required'"
                                :list="sellers"
                                option-value="id"
                                option-text="name"
                                placeholder="Поставщик"
                                name="'Поставщик'"
                                data-vv-validate-on="change|blur"
                            />
                            <span class="validation-error-message">{{ errors.first("'Поставщик'") }}</span>
                        </div>
                    </div>
                    <div class="row driver-inputs rows-padding">
                        <div class="col-md-2 mb-2">
                            <label for="sellerReturnMoney">Сумма:</label>
                        </div>
                        <div class="col-md-10 mb-10">
                            <input
                                id="sellerReturnMoney"
                                v-model="sellerReturnItem.money"
                                v-validate="'required'"
                                name="'сумма'"
                                type="number"
                                class="form-control"
                                data-vv-validate-on="change|blur"
                                min="1"
                            >

                            <span class="validation-error-message">{{ errors.first("'сумма'") }}</span>
                        </div>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn btn-primary control-buttons"
                            @click="save"
                        >
                            Сохранить
                        </button>
                        <button
                            ref="closeModalButton"
                            type="button"
                            class="btn btn-default control-buttons"
                            data-dismiss="modal"
                        >
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .rows-padding {
        padding-bottom: 10px
    }

    .validation-error-message {
        color: #ff0000;
        font-size: 10pt
    }
</style>
