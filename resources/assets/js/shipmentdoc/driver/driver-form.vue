<script>
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'DriverForm',

        components: {
            DatePicker,
        },

        mixins: [httpRequestWrapperMixin],

        props: {
            saveDriverUrl: {
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
            driver: {
                type: Object,
                default: () => ({
                    name: '',
                    surname: '',
                    patronymic: '',
                    passport: {
                        series: '',
                        number: '',
                        issued_by: '',
                        date_of_issue: '',
                    },
                    license: '',
                }),
            },
        },

        data() {
            return {
                newDriver: this.driver,
                momentPlugin: Moment,
                waitWhileSaving: false,
            };
        },

        methods: {
            addDriver() {
                this.newDriver.passport.date_of_issue = this.momentPlugin(this.newDriver.passport.date_of_issue)
                    .format('YYYY-MM-DD');

                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.httpPostRequest(this.saveDriverUrl, this.newDriver).then((response) => {
                            this.clearFields();

                            const newDriver = JSON.parse(response.body.data);
                            this.$emit('onDriverAdd', newDriver);

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
                this.newDriver.name = null;
                this.newDriver.surname = null;
                this.newDriver.patronymic = null;
                this.newDriver.passport.series = null;
                this.newDriver.passport.number = null;
                this.newDriver.passport.issued_by = null;
                this.newDriver.passport.date_of_issue = null;
                this.newDriver.license = null;
            },
        },
    };
</script>

<template>
    <div>
        <notifications />
        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="driverSurname">Фамилия:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="driverSurname"
                    v-model="newDriver.surname"
                    v-validate="'required'"
                    name="'фамилия водителя'"
                    type="text"
                    data-vv-validate-on="change|blur"
                    class="form-control"
                >
                <span class="validation-error-message">{{ errors.first("'фамилия водителя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="driverName">Имя:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="driverName"
                    v-model="newDriver.name"
                    v-validate="'required'"
                    name="'имя водителя'"
                    type="text"
                    data-vv-validate-on="change|blur"
                    class="form-control"
                >
                <span class="validation-error-message">{{ errors.first("'имя водителя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="driverMiddlename">Отчетство:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="driverMiddlename"
                    v-model="newDriver.patronymic"
                    v-validate="'required'"
                    name="'отчество водителя'"
                    type="text"
                    data-vv-validate-on="change|blur"
                    class="form-control"
                >
                <span class="validation-error-message">{{ errors.first("'отчество водителя'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="driverPassportSeries">Серия паспорта:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="driverPassportSeries"
                    v-model="newDriver.passport.series"
                    v-validate="'required|numeric'"
                    name="'серия паспорта'"
                    type="number"
                    class="form-control"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'серия паспорта'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="driverPassportNumber">Номер паспорта:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="driverPassportNumber"
                    v-model="newDriver.passport.number"
                    v-validate="'required|numeric'"
                    name="'номер паспорта'"
                    type="number"
                    class="form-control"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'номер паспорта'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="driverPassportIssued">Кем выдан:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="driverPassportIssued"
                    v-model="newDriver.passport.issued_by"
                    v-validate="'required'"
                    name="'выдан'"
                    type="text"
                    class="form-control"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'выдан'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label>Дата выдачи:</label>
            </div>
            <div class="col-md-10 mb-10">
                <date-picker
                    v-model="newDriver.passport.date_of_issue"
                    v-validate="'required'"
                    name="'дата выдачи'"
                    data-vv-validate-on="change|blur"
                    input-class="form-control"
                    format="YYYY-MM-DD"
                    lang="ru"
                />
                <span class="validation-error-message">{{ errors.first("'дата выдачи'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="driverLicense">Номер водительского удостоверения:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="driverLicense"
                    v-model="newDriver.license"
                    v-validate="'required'"
                    name="'номер ву'"
                    type="text"
                    class="form-control"
                    data-vv-validate-on="change|blur"
                >
                <span class="validation-error-message">{{ errors.first("'номер ву'") }}</span>
            </div>
        </div>
        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-primary"
                :disabled="waitWhileSaving"
                @click="addDriver"
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
