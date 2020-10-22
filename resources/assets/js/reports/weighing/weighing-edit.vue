<script>
    import { ModelSelect } from 'vue-search-select';
    import { formatPrice, formatWeight } from '../../util/formatter';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'WeighingEdit',
        components: {
            ModelSelect,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            editableWeighing: {
                type: Object,
                required: true,
            },
            updateUrl: {
                type: String,
                required: true,
            },
            customers: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                formatters: {
                    formatPrice,
                    formatWeight,
                },
                waitWhileSaving: false,
            };
        },
        computed: {
            editableWeighingData() {
                return this.editableWeighing;
            },
        },
        methods: {
            updateInfo() {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        if (this.editableWeighingData.customer.value) {
                            this.editableWeighingData.customer = this.editableWeighingData.customer.value;
                        }

                        this.castWeighingItems(this.editableWeighingData.weighings);

                        this.changeDataBeforeUpdate();

                        this.httpPostRequest(this.updateUrl, this.editableWeighingData).then((response) => {
                            this.$emit('onEdit', response.body.data);
                            this.$refs.closeModalButton.click();
                        });
                    }
                });
            },
            castWeighingItems(weighingItems) {
                for (const weighingItem of weighingItems) {
                    weighingItem.clogging = parseFloat(weighingItem.clogging);
                    weighingItem.brutto = parseFloat(weighingItem.brutto);
                    weighingItem.tare = parseFloat(weighingItem.tare);
                    weighingItem.trash = parseFloat(weighingItem.trash);
                }
            },
            updateMassCalculation(key) {
                const weighingItem = this.editableWeighingData.weighings[key];
                weighingItem.calculatedMass = (weighingItem.mass * weighingItem.newPrice).toFixed(2);
                this.$forceUpdate();
            },
            closeModal() {
                this.editableWeighing.customer = this.editableWeighing.oldCustomer;
                for (const weighingItem of this.editableWeighing.weighings) {
                    weighingItem.newPrice = weighingItem.price;
                }
                this.$forceUpdate();
            },
            // Обновить данные в основном списке
            changeDataBeforeUpdate() {
                this.editableWeighing.oldCustomer = this.editableWeighing.customer;
                for (const weighingItem of this.editableWeighing.weighings) {
                    weighingItem.price = weighingItem.newPrice;
                }
            },
        },
    };
</script>
<template>
    <div>
        <div
            id="weighingEditModal"
            class="modal fade bs-example-modal-lg"
            tabindex="-1"
            role="dialog"
            aria-labelledby="weighingEditLabel"
        >
            <div
                class="modal-dialog modal-lg"
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
                            Редактировать взвешивание
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customersSelect">
                                Поставщик:
                            </label>
                            <model-select
                                id="customersSelect"
                                v-model="editableWeighingData.customer"
                                v-validate="'required'"
                                data-vv-validate-on="change|blur"
                                name="поставщик"
                                :options="customers"
                            />
                            <span class="validation-error-message">
                                {{ errors.first('поставщик') }}
                            </span>

                            <table class="table table-striped table-bordered main-table">
                                <thead>
                                    <tr>
                                        <th class="metal-tr-width">
                                            Металл
                                        </th>
                                        <th>
                                            Масса (кг.)
                                        </th>
                                        <th>
                                            Цена за единицу (руб.)
                                        </th>
                                        <th>
                                            Итого (руб.)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(weighingItem, key) in editableWeighingData.weighings"
                                        :key="weighingItem.id"
                                    >
                                        <td class="metal-tr-width">
                                            {{ weighingItem.metal.name }}
                                        </td>
                                        <td>
                                            {{ formatters.formatWeight(weighingItem.mass) }} кг.
                                        </td>
                                        <td>
                                            <input
                                                v-model="weighingItem.newPrice"
                                                v-validate="'required'"
                                                data-vv-validate-on="change|blur"
                                                :name="'цена позиции ' + key"
                                                type="number"
                                                step="0.01"
                                                class="form-control"
                                                @keyup="updateMassCalculation(key)"
                                            >
                                            <span class="validation-error-message">
                                                {{ errors.first('цена позиции ' + key) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ formatters.formatPrice(weighingItem.calculatedMass) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="waitWhileSaving"
                            @click="updateInfo"
                        >
                            Сохранить
                        </button>
                        <button
                            type="button"
                            class="btn btn-default"
                            data-dismiss="modal"
                            @click="closeModal"
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
    .validation-error-message {
        color: #ff0000;
        font-size: 10pt
    }
    .metal-tr-width {
        width: 300px
    }
</style>
