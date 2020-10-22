<script>
    export default {
        props: {
            expenseForBinding: {
                type: Object,
                default() {
                    return {
                        comment: '',
                        date: '',
                        id: 0,
                        inn: '',
                        money: 0,
                        orderId: null,
                        seller: '',
                    };
                },
            },
            orders: {
                type: Object,
                default() {
                    return {
                        0: {
                            0: {
                                date: '',
                                document: '',
                                id: 0,
                                items: [{
                                    price: '',
                                    quantity: '',
                                    spare: '',
                                }],
                                price: 0,
                            },
                        },
                    };
                },
            },
        },
        data() {
            return {
                spareEdit: '',
                countEdit: '',
                orderId: null,
            };
        },
        methods: {
            closeForm() {
                this.$emit('closeForm');
            },
            saveBind() {
                this.$emit('saveBind', this.orderId, this.expenseForBinding.id);
            },
            toCurrency(value) {
                const formatter = new Intl.NumberFormat('ru', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 0,
                });
                return formatter.format(value);
            },
            setColorLine(price) {
                if (price === this.expenseForBinding.money) {
                    return {
                        background: '#c2ffc7',
                    };
                }

                return false;
            },
        },
    };
</script>
<template>
    <div id="modalCenter">
        <div
            id="modalWindow"
            class="modalWindow"
        >
            <div class="modal-backdrop fade in" />
            <div
                id="myModal"
                class="modal fade in"
                tabindex="-1"
                role="dialog"
                aria-labelledby="myModalLabel"
                aria-hidden="false"
                style="display: block;"
                @click="closeForm()"
            />
            <div
                class="modal-dialog modal-lg modalInPayment"
                style="z-index: 1050"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-hidden="true"
                            @click="closeForm()"
                        >
                            ×
                        </button>
                        <h4
                            id="myLargeModalLabel"
                            class="modal-title"
                        >
                            {{ expenseForBinding.date }} - {{ expenseForBinding.comment }}
                            {{ toCurrency(expenseForBinding.money) }}
                        </h4>
                    </div>
                    <div
                        id="modal-body"
                        class="modal-body"
                    >
                        <div
                            class="row"
                            style="margin-top: 5px; "
                        >
                            <div
                                class="col-lg-12 col-md-12 col-sm-12"
                                style="overflow: auto; height: 350px;"
                            >
                                <div
                                    v-for="order in orders[expenseForBinding.inn]"
                                    :key="order.id"
                                >
                                    <p :style="setColorLine(order.price)">
                                        <label>
                                            <input
                                                v-model="orderId"
                                                name="bind"
                                                :value="order.id"
                                                type="radio"
                                            >
                                            Заказ № {{ order.id }} от {{ order.date }}. {{ order.document }}
                                            на сумму {{ toCurrency(order.price) }}
                                        </label>
                                    </p>
                                    <table class="table table-striped table-bordered main-table">
                                        <thead>
                                            <tr>
                                                <th style="">
                                                    Запчасть
                                                </th>
                                                <th style="">
                                                    Количество
                                                </th>
                                                <th style="">
                                                    Цена
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(item, itemIndex) in order.items"
                                                :key="itemIndex"
                                            >
                                                <td>{{ item.spare }}</td>
                                                <td>{{ item.quantity }}</td>
                                                <td style="text-align: center;">
                                                    {{ toCurrency(item.price) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-default"
                            data-dismiss="modal"
                            @click="closeForm()"
                        >
                            Закрыть
                        </button>
                        <button
                            v-if="orderId"
                            type="button"
                            class="btn btn-primary"
                            @click="saveBind()"
                        >
                            Сохранить изменения
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
