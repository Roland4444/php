<script>
    import DatePicker from 'vue2-datepicker';
    import { ModelSelect } from 'vue-search-select';
    import Moment from 'moment';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'WeighingPurchaseModal',
        components: {
            DatePicker, ModelSelect,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            departments: {
                type: Array,
                required: true,
            },
            currentDepartment: {
                type: Object,
                required: false,
                default: null,
            },
            fullAccess: {
                type: Number,
                required: false,
                default: 0,
            },
            purchaseSaveUrl: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                receipt: {
                    date: new Date(),
                    department: this.currentDepartment,
                },
                momentPlugin: Moment,
                waitWhileSaving: false,
            };
        },
        methods: {
            render() {
                this.checkDepartmentValue();

                this.httpPostRequest(this.purchaseSaveUrl, {
                    date: this.momentPlugin(this.receipt.date).format('YYYY-MM-DD'),
                    departmentId: this.receipt.department.value,
                }).then(() => {
                    this.$refs.closeModalButton.click();
                });
            },
            /**
             * Проверяет наличие id подразделения при отработке Model-Select
             */
            checkDepartmentValue() {
                if (!this.receipt.department.value) {
                    const val = this.receipt.department;
                    this.receipt.department = {};
                    this.receipt.department.value = val;
                }
            },
        },
    };
</script>
<template>
    <div
        id="weighingReceiptModal"
        class="modal fade bs-example-modal-sm"
        tabindex="-1"
        role="dialog"
        aria-labelledby="weighingReceiptModalLabel"
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
                        Оформление прихода
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row row-elements-margin">
                        <div
                            v-if="fullAccess"
                            class="col-md-12 mb-12"
                        >
                            <date-picker
                                v-model="receipt.date"
                                input-class="form-control"
                                format="YYYY-MM-DD"
                                lang="ru"
                            />
                        </div>
                        <div
                            v-else
                            class="col-md-12 mb-12"
                        >
                            Дата: {{ momentPlugin().format('YYYY-MM-DD') }}
                        </div>
                    </div>
                    <div class="row row-elements-margin">
                        <div
                            v-if="fullAccess"
                            class="col-md-12 mb-12"
                        >
                            <model-select
                                id="departmentsSelect"
                                v-model="receipt.department"
                                :options="departments"
                            />
                        </div>
                        <div
                            v-else
                            class="col-md-12 mb-12"
                        >
                            {{ receipt.department.text }}
                        </div>
                    </div>

                    <div style="text-align: center">
                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="waitWhileSaving"
                            @click="render"
                        >
                            Ок
                        </button>
                        <button
                            type="button"
                            class="btn btn-default"
                            data-dismiss="modal"
                        >
                            Отмена
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .row-elements-margin {
        margin-bottom: 10px
    }
</style>
