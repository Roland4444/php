<script>
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        name: 'OwnerForm',
        mixins: [httpRequestWrapperMixin],
        props: {
            id: {
                type: Number,
                default: 0,
                required: false,
            },
            owner: {
                type: Object,
                default: () => ({
                    name: '',
                    phone: '',
                    inn: '',
                    account: '',
                }),
            },
        },
        data() {
            return {
                newOwner: this.owner,
                waitWhileSaving: false,
            };
        },
        computed: {
            apiUrl() {
                return `${this.$store.state.apiUrl}/owner`;
            },
        },
        mounted() {
            if (this.id) {
                this.httpGetRequest(`${this.apiUrl}/get/${this.id}`).then((response) => {
                    const owner = JSON.parse(response.body.data);
                    this.newOwner.id = owner.id;
                    this.newOwner.name = owner.name;
                    this.newOwner.phone = owner.phone;
                    this.newOwner.inn = owner.inn;
                    this.newOwner.account = owner.account;
                });
            }
        },
        methods: {
            save() {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.httpPostRequest(`${this.apiUrl}/save`, this.newOwner).then((response) => {
                            const newOwner = JSON.parse(response.body.data);

                            this.$emit('onOwnerSave', newOwner);
                            this.clearFields();
                        });
                    }
                });
            },

            clearFields() {
                this.newOwner.id = null;
                this.newOwner.name = null;
                this.newOwner.phone = null;
                this.newOwner.account = null;
                this.newOwner.inn = null;
            },
        },
    };
</script>

<template>
    <div>
        <notifications />
        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="ownerName">Наименование:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="ownerName"
                    v-model="newOwner.name"
                    v-validate="'required'"
                    name="'наименование перевозчика'"
                    type="text"
                    data-vv-validate-on="change|blur"
                    class="form-control"
                >
                <span class="validation-error-message">{{ errors.first("'наименование перевозчика'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="ownerPhone">Телефон:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="ownerPhone"
                    v-model="newOwner.phone"
                    v-validate="'required|max:20'"
                    name="'телефон перевозчика'"
                    type="text"
                    data-vv-validate-on="change|blur"
                    class="form-control"
                >
                <span class="validation-error-message">{{ errors.first("'телефон перевозчика'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="ownerInn">ИНН:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="ownerInn"
                    v-model="newOwner.inn"
                    v-validate="'required|max:12'"
                    name="'инн перевозчика'"
                    type="text"
                    data-vv-validate-on="change|blur"
                    class="form-control"
                >
                <span class="validation-error-message">{{ errors.first("'инн перевозчика'") }}</span>
            </div>
        </div>

        <div class="row driver-inputs">
            <div class="col-md-2 mb-2">
                <label for="ownerBankDetails">Банковские реквизиты:</label>
            </div>
            <div class="col-md-10 mb-10">
                <input
                    id="ownerBankDetails"
                    v-model="newOwner.account"
                    v-validate="'required'"
                    name="'банковские реквизиты перевозчика'"
                    type="text"
                    data-vv-validate-on="change|blur"
                    class="form-control"
                >
                <span class="validation-error-message">{{ errors.first("'банковские реквизиты перевозчика'") }}</span>
            </div>
        </div>

        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-primary"
                :disabled="waitWhileSaving"
                @click="save"
            >
                Сохранить
            </button>
            <button
                type="button"
                class="btn btn-default"
                @click="$emit('onOwnerAddCancel')"
            >
                Отмена
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
