<script>
    import DatePicker from 'vue2-datepicker';
    import moment from 'moment';
    import { ModelSelect } from 'vue-search-select';
    import ShowAllCustomers from '../ShowAllCustomers.vue';

    export default {
        name: 'StoragePurchaseAdd',
        components: {
            'date-picker': DatePicker,
            'model-select': ModelSelect,
            'show-all-customers': ShowAllCustomers,
        },
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
            customersUrl: {
                type: String,
                required: true,
            },
            permissions: {
                type: Object,
                required: true,
            },
            departmentId: {
                type: Number,
                required: true,
            },
            metals: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                inputForm: {
                    date: moment().format('YYYY-MM-DD'),
                    customer: 0,
                    comment: '',
                    metal: {},
                    weight: '',
                    cost: '',
                    formal: '',
                },
                items: [],
                customers: [],
                dateIsEnabled: this.permissions.delete,
            };
        },
        created() {
            const defaultMetal = this.metals.find((item) => item.def === true);
            this.inputForm.metal = defaultMetal;
        },
        methods: {
            showError(msg) {
                this.$notify({
                    title: 'Ошибка',
                    text: msg,
                    type: 'error',
                });
            },
            addItem() {
                if (!this.validate()) {
                    return;
                }
                this.items.push({
                    date: moment(this.inputForm.date).format('YYYY-MM-DD'),
                    customer: this.inputForm.customer,
                    metal: this.inputForm.metal,
                    weight: this.inputForm.weight,
                    cost: this.inputForm.cost,
                    formal: this.inputForm.formal,
                });
                this.clearForm();
            },
            deleteItem(index) {
                this.items.splice(index, 1);
            },
            clearForm() {
                this.inputForm.weight = '';
                this.inputForm.cost = '';
                this.inputForm.formal = '';
                this.inputForm.date = moment(this.inputForm.date).format('YYYY-MM-DD');
                this.dateIsEnabled = false;
            },
            validate() {
                const errorFields = [];
                if (!this.inputForm.date) {
                    errorFields.push('Дата');
                }
                if (!this.inputForm.weight) {
                    errorFields.push('Масса');
                }
                if (!this.inputForm.cost) {
                    errorFields.push('Цена');
                }
                if (errorFields.length > 0) {
                    this.showError(`Поля ${errorFields.join()} заполнены не корректно`);
                    return false;
                }
                return true;
            },
            save() {
                const params = {
                    date: this.inputForm.date,
                    comment: this.inputForm.comment,
                    items: this.items,
                };
                this.$http.post(`${this.apiUrl}/save-ajax`, params).then(() => {
                    window.location.assign(this.apiUrl);
                }, (response) => {
                    this.showError(response.body.error);
                });
            },
            updateCustomers(updatedCustomers) {
                this.customers = updatedCustomers;
                this.inputForm.customer = this.customers.find((item) => item.def === true);
                if (!this.inputForm.customer) {
                    [this.inputForm.customer] = this.customers;
                }
            },
        },
    };
</script>
<template>
    <div>
        <notifications />

        <form
            id="needs-validation"
            novalidate
        >
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="date">Дата</label>
                    <date-picker
                        v-if="dateIsEnabled"
                        id="date"
                        v-model="inputForm.date"
                        input-class="form-control"
                        format="YYYY-MM-DD"
                        lang="ru"
                    />
                    <input
                        v-else
                        type="text"
                        disabled
                        class="form-control"
                        :value="inputForm.date"
                    >
                </div>
                <div class="col-md-4 mb-3">
                    <label for="customer">Поставщик</label>
                    <model-select
                        id="customer"
                        v-model="inputForm.customer"
                        class="form-control"
                        :options="customers"
                    />
                </div>
                <div class="col-md-2 mb-3">
                    <show-all-customers
                        :api-url="customersUrl"
                        :department-id="departmentId"
                        @customersUpdated="updateCustomers"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="comment">Комментарий</label>
                    <input
                        id="comment"
                        v-model="inputForm.comment"
                        type="text"
                        name="comment"
                        class="form-control"
                    >
                </div>
                <div class="col-md-3 mb-3">
                    <label for="metal">Металл</label>
                    <select
                        id="metal"
                        v-model="inputForm.metal"
                        class="form-control"
                    >
                        <option
                            v-for="metal in metals"
                            :key="metal.id"
                            :value="metal"
                        >
                            {{ metal.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="weight">Масса</label>
                    <input
                        id="weight"
                        v-model="inputForm.weight"
                        required
                        type="number"
                        class="form-control"
                    >
                </div>
                <div class="col-md-2 mb-3">
                    <label for="cost">Цена</label>
                    <input
                        id="cost"
                        v-model="inputForm.cost"
                        required
                        type="number"
                        class="form-control"
                    >
                </div>
                <div class="col-md-2 mb-3">
                    <label for="formal">Офиц. цена</label>
                    <input
                        id="formal"
                        v-model="inputForm.formal"
                        required
                        type="number"
                        class="form-control"
                    >
                </div>
            </div>
            <div
                id="add"
                style="margin-top:15px;"
                class="btn btn-default"
                @click="addItem"
            >
                Добавить
            </div>
            <div
                id="save"
                style="margin-top:15px;"
                class="btn btn-primary"
                @click="save"
            >
                Сохранить
            </div>
        </form>

        <table
            class="table table-striped table-bordered"
            style="margin-top: 20px;"
        >
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Поставщик</th>
                    <th>Металл</th>
                    <th>Масса</th>
                    <th>Цена</th>
                    <th>Оф цена</th>
                    <th>Сумма</th>
                    <th />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in items"
                    :key="index"
                >
                    <td>{{ item.date }}</td>
                    <td>{{ item.customer.name }}</td>
                    <td>{{ item.metal.name }}</td>
                    <td>{{ item.weight }}</td>
                    <td>{{ item.cost }}</td>
                    <td>{{ item.formal }}</td>
                    <td>{{ (item.weight * item.cost).toFixed(2) }}</td>
                    <td>
                        <a
                            href="#"
                            @click="deleteItem(index)"
                        >
                            <img
                                src="/images/del.png"
                                alt="Удалить"
                            >
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
