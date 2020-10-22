<script>
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';
    import { ModelListSelect } from 'vue-search-select';
    import ReceiverList from './receiver-list.vue';
    import DriverModal from './driver-modal.vue';
    import OwnerModal from './owner-modal.vue';
    import ShipmentItems from './shipment_items.vue';
    import { formatPrice, formatWeight } from '../../util/formatter';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'ShipmentdocAdd',

        components: {
            OwnerModal,
            DatePicker,
            ModelListSelect,
            ReceiverList,
            DriverModal,
            ShipmentItems,
        },

        mixins: [httpRequestWrapperMixin],

        props: {
            apiUrl: {
                type: String,
                default: '',
            },
            shipmentDocProp: {
                type: Object,
                default() {
                    return {
                        document_items: [],
                        driver: {
                            name: '',
                            passport_series: '',
                            passport_number: '',
                            issued_by: '',
                            date_of_issue: '',
                        },

                        car_number: '',
                        trailer_number: '',
                        car_model: '',
                        seals: '',
                        date: '',

                        receiver: {
                            id: null,
                            name: '',
                            legalName: '',
                            address: '',
                            phone: '',
                            contractNumber: '',
                            contractDate: '',
                            inn: '',
                            account: '',
                        },

                        payer: {
                            id: null,
                            name: '',
                            legalName: '',
                            address: '',
                            phone: '',
                            contractNumber: '',
                            contractDate: '',
                            inn: '',
                            account: '',
                        },

                        owner: {
                            id: null,
                            name: '',
                            phone: '',
                        },

                        letter_of_authority_number: null,
                        packingListNumber: null,
                        explosive_number: null,
                        radiation_number: null,
                        transport_doc_number: null,
                        id: null,
                    };
                },
            },
            driversProp: {
                type: Array,
                default() {
                    return [{
                        created_at: '',
                        date_of_issue: '',
                        id: 0,
                        initials: '',
                        issued_by: '',
                        license: '',
                        name: '',
                        passport: {
                            date_of_issue: '',
                            issued_by: '',
                            number: 0,
                            series: 0,
                        },
                        passport_number: 0,
                        passport_series: 0,
                        patronymic: '',
                        surname: '',
                        updated_at: '',
                    }];
                },
            },
            ownersProp: {
                type: Array,
                default() {
                    return [{
                        account: '',
                        created_at: '',
                        id: 0,
                        inn: '',
                        name: '',
                        phone: '',
                        updated_at: '',
                    }];
                },
            },
            receivers: {
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
            // urls
            pdfPackingListUrl: {
                type: String,
                default: '',
            },
            pdfTransportWaybillUrl: {
                type: String,
                default: '',
            },
            pdfPackingTransportUrl: {
                type: String,
                default: '',
            },
            pdfLetterOfAuthorityUrl: {
                type: String,
                default: '',
            },
            pdfExplosiveRadiationCertificateUrl: {
                type: String,
                default: '',
            },
            saveDriverUrl: {
                type: String,
                default: '',
            },
            saveReceiverUrl: {
                type: String,
                default: '',
            },
            saveDocumentUrl: {
                type: String,
                default: '',
            },
            getContainersUrl: {
                type: String,
                default: '',
            },
        },

        provide() {
            return {
                parentValidator: this.$validator,
            };
        },

        data() {
            return {
                drivers: this.driversProp,
                owners: this.ownersProp,
                containers_date: this.shipmentDocProp.date === '' ? new Date() : new Date(this.shipmentDocProp.date),
                containers: [],

                shipmentDoc: {
                    container: {
                        id: null,
                        name: '',
                        shipment_date: '',
                        container_items: this.shipmentDocProp.document_items,
                    },
                    driver: this.shipmentDocProp.driver,

                    vehicle: {
                        car_number: this.shipmentDocProp.car_number,
                        trailer_number: this.shipmentDocProp.trailer_number,
                        car_model: this.shipmentDocProp.car_model,
                        seals: this.shipmentDocProp.seals,
                    },

                    receiver: this.shipmentDocProp.receiver,

                    payer: this.shipmentDocProp.payer,

                    owner: this.shipmentDocProp.owner,

                    letterOfAuthorityNumber: this.shipmentDocProp.letter_of_authority_number,
                    packingListNumber: this.shipmentDocProp.number,
                    transportWaybillNumber: this.shipmentDocProp.transport_doc_number,
                    explosiveDocNumber: this.shipmentDocProp.explosive_number,
                    radiationDocNumber: this.shipmentDocProp.radiation_number,
                },

                shipmentDocId: this.shipmentDocProp.id,

                steps: [
                    {
                        label: 'Отгрузка',
                        slot: 'shipment',
                    },
                    {
                        label: 'Водитель / Транспорт',
                        slot: 'driver',
                    },
                    {
                        label: 'Перевозчик',
                        slot: 'shipper',
                    },
                    {
                        label: 'Грузополучатель / Плательщик',
                        slot: 'receiver',
                    },
                    {
                        label: 'Номер документа',
                        slot: 'doc_number',
                        options: {
                            nextDisabled: false,
                        },
                    },
                    {
                        label: 'Готовые документы',
                        slot: 'ready_docs',
                        options: {
                            nextDisabled: true,
                        },
                    },
                ],

                momentPlugin: Moment,
                formatters: {
                    formatPrice, formatWeight,
                },
            };
        },

        created() {
            this.$store.commit('setApiUrl', `${this.apiUrl}`);
        },

        methods: {
            nextClicked(currentPage) {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        if (currentPage === 0 && this.shipmentDoc.container.container_items.length === 0) {
                            this.$notify({
                                title: 'Ошибка',
                                text: 'Не выбрано ни одного контейнера',
                                type: 'error',
                            });
                            return false;
                        }

                        // 4 current page - сохранение после "номера документа"
                        if (currentPage === 4) {
                            this.saveDocument();
                        } else {
                            this.$refs.shipmentDocWizard.goTo(currentPage + 1);
                        }
                    }
                    return true;
                });
            },
            exportTnToPdf() {
                window.open(`${this.pdfPackingListUrl}/${this.shipmentDocId}`);
            },
            exportTransportWaybill() {
                window.open(`${this.pdfTransportWaybillUrl}/${this.shipmentDocId}`);
            },
            exportPackingTransportDocument() {
                window.open(`${this.pdfPackingTransportUrl}/${this.shipmentDocId}`);
            },
            exportLetterOfAuthorityDocument() {
                window.open(`${this.pdfLetterOfAuthorityUrl}/${this.shipmentDocId}`);
            },
            exportExplosiveRadiationCertificate() {
                window.open(`${this.pdfExplosiveRadiationCertificateUrl}/${this.shipmentDocId}`);
            },
            normalizePostData() {
                return {
                    driverId: this.shipmentDoc.driver.id,
                    ownerId: this.shipmentDoc.owner.id,
                    payerId: this.shipmentDoc.payer.id,
                    receiverId: this.shipmentDoc.receiver.id,
                    vehicle: JSON.stringify(this.shipmentDoc.vehicle),
                    letterOfAuthorityNumber: this.shipmentDoc.letterOfAuthorityNumber,
                    packingListNumber: this.shipmentDoc.packingListNumber,
                    transportWaybillNumber: this.shipmentDoc.transportWaybillNumber,
                    explosiveDocNumber: this.shipmentDoc.explosiveDocNumber,
                    radiationDocNumber: this.shipmentDoc.radiationDocNumber,
                    container: JSON.stringify(this.shipmentDoc.container),
                    id: this.shipmentDocId,
                    date: Moment(this.containers_date).format('YYYY-MM-DD'),
                };
            },
            driverFullName(driver) {
                return `${driver.surname} ${driver.name} ${driver.patronymic}`;
            },
            containerDesignation(container) {
                return `${container.name} ${container.shipment_date}`;
            },
            onDriverAdd(newDriver) {
                this.drivers.push(newDriver);
                this.shipmentDoc.driver = newDriver;
            },
            onOwnerAdd(newOwner) {
                this.owners.push(newOwner);
                this.shipmentDoc.owner = newOwner;
            },
            onReceiverChange(newData) {
                this.shipmentDoc.receiver = newData.receiver;
                this.shipmentDoc.payer = newData.payer;
            },
            saveDocument() {
                const docForExport = this.normalizePostData();

                this.steps[4].options.nextDisabled = true;

                this.httpPostRequest(this.saveDocumentUrl, docForExport).then((response) => {
                    this.shipmentDocId = JSON.parse(response.body.data);

                    this.steps[4].options.nextDisabled = false;
                    this.$refs.shipmentDocWizard.goTo(5);

                    return true;
                });
            },
            priceIsZero(price) {
                return parseFloat(price) === 0.00;
            },
        },
    };
</script>

<template>
    <div>
        <notifications />

        <vue-good-wizard
            ref="shipmentDocWizard"
            :steps="steps"
            :on-next="nextClicked"
            :previous-step-label="'Назад'"
            :next-step-label="'Вперед'"
            :final-step-label="' '"
        >
            <div slot="shipment">
                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="date">Дата отгрузки</label>
                    </div>
                    <div class="col-md-4 mb-4">
                        <date-picker
                            id="date"
                            v-model="containers_date"
                            v-validate="'required'"
                            name="'Дата отгрузки'"
                            data-vv-validate-on="change|blur"
                            input-class="form-control"
                            format="YYYY-MM-DD"
                            lang="ru"
                        />
                    </div>
                </div>
                <shipment-items
                    v-model="shipmentDoc.container"
                    :date="containers_date"
                    :get-containers-url="getContainersUrl"
                />

                <table
                    v-show="shipmentDoc.container.container_items.length !== 0"
                    class="table table-striped table-bordered main-table"
                >
                    <thead>
                        <tr>
                            <th>Вид лома</th>
                            <th>Код</th>
                            <th>Вес</th>
                            <th>Цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in shipmentDoc.container.container_items"
                            :key="index"
                        >
                            <td><span v-if="item.alias">{{ item.alias }}</span><span v-else>{{ item.name }}</span></td>
                            <td>{{ item.code }}</td>
                            <td class="text-right">
                                {{ formatters.formatWeight(item.quantity) }} кг.
                            </td>
                            <td
                                class="text-right"
                                :class="{'text-danger font-weight-bold': priceIsZero(item.price)}"
                            >
                                {{ formatters.formatPrice( item.price) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div slot="driver">
                <h4>Водитель</h4>

                <div class="row driver-inputs">
                    <div class="col-md-4 mb-4">
                        <model-list-select
                            id="driversList"
                            v-model="shipmentDoc.driver"
                            :list="drivers"
                            option-value="id"
                            :custom-text="driverFullName"
                        />
                    </div>
                    <div class="col-md-2 mb-2">
                        <button
                            data-toggle="modal"
                            data-target="#driverAddModal"
                            class="btn btn-default"
                        >
                            Добавить
                        </button>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        Выбранный водитель
                    </div>
                    <div class="col-md-6 mb-6">
                        <p>
                            ФИО:
                            {{ shipmentDoc.driver.surname }}
                            {{ shipmentDoc.driver.name }}
                            {{ shipmentDoc.driver.patronymic }}
                        </p>
                        <p>
                            Паспорт:
                            {{ shipmentDoc.driver.passport_series }}
                            {{ shipmentDoc.driver.passport_number }}
                            {{ shipmentDoc.driver.issued_by }}
                            {{ shipmentDoc.driver.date_of_issue }}
                        </p>
                        <p>Водительское удостоверение: {{ shipmentDoc.driver.license }}</p>
                    </div>
                    <input
                        v-model="shipmentDoc.driver.name"
                        v-validate="'required'"
                        name="'данные водителя'"
                        type="hidden"
                    >
                    <span class="validation-error-message">{{ errors.first("'данные водителя'") }}</span>
                </div>

                <h4>Транспорт</h4>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="vehicleModelNumber">Модель машины:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="vehicleModel"
                            v-model="shipmentDoc.vehicle.car_model"
                            v-validate="'required'"
                            type="text"
                            class="form-control"
                            name="'модель машины'"
                            data-vv-validate-on="change|blur"
                        >
                        <span class="validation-error-message">{{ errors.first("'модель машины'") }}</span>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="vehicleModelNumber">Номер машины:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="vehicleModelNumber"
                            v-model="shipmentDoc.vehicle.car_number"
                            v-validate="'required'"
                            type="text"
                            class="form-control"
                            name="'номер машины'"
                            data-vv-validate-on="change|blur"
                        >
                        <span class="validation-error-message">{{ errors.first("'номер машины'") }}</span>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="vehicleTrailerNumber">Номер прицепа:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="vehicleTrailerNumber"
                            v-model="shipmentDoc.vehicle.trailer_number"
                            type="text"
                            class="form-control"
                            name="'номер прицепа'"
                        >
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="vehicleSeals">Пломбы:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="vehicleSeals"
                            v-model="shipmentDoc.vehicle.seals"
                            type="text"
                            class="form-control"
                            name="'пломбы'"
                        >
                    </div>
                </div>
            </div>
            <div slot="shipper">
                <h4>Перевозчик</h4>

                <div class="row driver-inputs">
                    <div class="col-md-4 mb-4">
                        <model-list-select
                            id="ownersList"
                            v-model="shipmentDoc.owner"
                            :list="owners"
                            option-value="id"
                            option-text="name"
                        />
                    </div>
                    <div class="col-md-2 mb-2">
                        <button
                            data-toggle="modal"
                            data-target="#ownerAddModal"
                            class="btn btn-default"
                        >
                            Добавить
                        </button>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        Выбранный перевозчик
                    </div>
                    <div class="col-md-6 mb-6">
                        <p>Наименование: {{ shipmentDoc.owner.name }} </p>
                        <p>Телефон: {{ shipmentDoc.owner.phone }}</p>
                    </div>
                    <input
                        v-model="shipmentDoc.owner.name"
                        v-validate="'required'"
                        name="'данные водителя'"
                        type="hidden"
                    >
                    <span class="validation-error-message">{{ errors.first("'данные перевозчика'") }}</span>
                </div>
            </div>
            <div slot="receiver">
                <receiver-list
                    :chosen-receiver="shipmentDocProp.receiver"
                    :chosen-payer="shipmentDocProp.payer"
                    :receivers-prop="receivers"
                    :save-receiver-url="saveReceiverUrl"
                    @onReceiverChange="onReceiverChange"
                />
            </div>
            <div slot="doc_number">
                <h4>Номера документов</h4>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="letterOfAuthorityNumber">№ доверенности:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="letterOfAuthorityNumber"
                            v-model="shipmentDoc.letterOfAuthorityNumber"
                            v-validate="'required|numeric'"
                            type="text"
                            class="form-control"
                            name="'№ доверенности'"
                            data-vv-validate-on="change|blur"
                        >
                        <span class="validation-error-message">{{ errors.first("'№ доверенности'") }}</span>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="packingListNumber">№ товарной накладной:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="packingTransportDocument"
                            v-model="shipmentDoc.packingListNumber"
                            v-validate="'required|numeric'"
                            type="text"
                            class="form-control"
                            name="'№ товарной накладной'"
                            data-vv-validate-on="change|blur"
                        >
                        <span class="validation-error-message">{{ errors.first("'№ товарной накладной'") }}</span>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="packingListNumber">№ транспортной накладной</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="packingListNumber"
                            v-model="shipmentDoc.transportWaybillNumber"
                            v-validate="'required|numeric'"
                            type="text"
                            class="form-control"
                            name="'№ товарной накладной'"
                            data-vv-validate-on="change|blur"
                        >
                        <span class="validation-error-message">{{ errors.first("'№ товарной накладной'") }}</span>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="explosiveDocNumber">№ уд. взрыв:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="explosiveDocNumber"
                            v-model="shipmentDoc.explosiveDocNumber"
                            v-validate="'required|numeric'"
                            type="text"
                            class="form-control"
                            name="'№ удостоверения о взрывобезопасности'"
                            data-vv-validate-on="change|blur"
                        >
                        <span class="validation-error-message">
                            {{ errors.first("'№ удостоверения о взрывобезопасности'") }}
                        </span>
                    </div>
                </div>

                <div class="row driver-inputs">
                    <div class="col-md-2 mb-2">
                        <label for="radiationDocNumber">№ уд. рад:</label>
                    </div>
                    <div class="col-md-10 mb-10">
                        <input
                            id="radiationDocNumber"
                            v-model="shipmentDoc.radiationDocNumber"
                            v-validate="'required|numeric'"
                            type="text"
                            class="form-control"
                            name="'№ удостоверения о радиадозиметрическом контроле'"
                            data-vv-validate-on="change|blur"
                        >
                        <span class="validation-error-message">
                            {{ errors.first("'№ удостоверения о радиадозиметрическом контроле'") }}
                        </span>
                    </div>
                </div>
            </div>
            <div slot="ready_docs">
                <h4>Готовые документы</h4>

                <a
                    href="#"
                    @click="exportTnToPdf"
                >
                    Товарная накладная
                </a>
                <br>
                <a
                    href="#"
                    @click="exportTransportWaybill"
                >
                    Транспортная накладная
                </a>
                <br>
                <a
                    href="#"
                    @click="exportPackingTransportDocument"
                >
                    Товарно-транспортная накладная
                </a>
                <br>
                <a
                    href="#"
                    @click="exportLetterOfAuthorityDocument"
                >
                    Доверенность
                </a>
                <br>
                <a
                    href="#"
                    @click="exportExplosiveRadiationCertificate"
                >
                    Удостоверение о взрывобезопасности / радиадозиметрическом контроле
                </a>
            </div>
        </vue-good-wizard>

        <driver-modal
            :save-driver-url="saveDriverUrl"
            :form-type="'wizard'"
            @onDriverAdd="onDriverAdd"
        />

        <owner-modal
            @onOwnerSave="onOwnerAdd"
        />
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
