<script>
    import OrdersFilter from './orders-filter.vue';
    import { formatPrice } from '../../util/formatter';

    export default {
        components: {
            'orders-filter': OrdersFilter,
        },
        props: {
            action: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                formatPrice,
            };
        },
        computed: {
            orders() {
                return this.$store.state.orders;
            },
            receiptItems() {
                return this.$store.state.receiptItems;
            },
        },
        methods: {
            openModalForAdd(orderItem) {
                const itemToAdd = orderItem;
                itemToAdd.countRestForReceipt = this.calcCountForRest(orderItem);
                this.$emit('openModalForAdd', itemToAdd);
            },
            issetInReceiptItems(item) {
                for (const receiptItem of this.receiptItems) {
                    if (receiptItem.orderItemId === item.orderItemId) {
                        return true;
                    }
                }
                return false;
            },
            setBackground(item) {
                if (this.issetInReceiptItems(item)) {
                    return {
                        background: '#d2ba5c54',
                    };
                }

                return false;
            },
            calcReceived(orderItem) {
                const reducer = (acc, value) => {
                    if (value.orderItemId === orderItem.orderItemId) {
                        // eslint-disable-next-line
                        acc += parseFloat(value.count);
                    }
                    return acc;
                };
                const countInReceiptItem = this.receiptItems.reduce(reducer, 0);
                return orderItem.countReceipted + countInReceiptItem;
            },
            calcCountForRest(item) {
                return parseFloat(item.countInOrder) - item.countReceipted;
            },
            isAvailable(item) {
                const received = this.calcReceived(item);
                return item.countInOrder > received && !this.issetInReceiptItems(item);
            },
        },
    };
</script>
<template>
    <div>
        <orders-filter
            :action="action"
        />

        <div
            v-for="order in orders"
            :key="order.id"
            class="rows"
        >
            <h4 class="order-table-title-alignment">
                Заказ №{{ order.id }} от {{ order.date }}.
                Поставщик: {{ order.seller }}<br>
                Документ заказа: {{ order.documentName }}
            </h4>
            <table class="table table-striped table-bordered main-table">
                <thead>
                    <tr>
                        <th class="spare-order-th">
                            Запчасть
                        </th>
                        <th class="spare-order-th">
                            Стоимость запчасти
                        </th>
                        <th class="spare-order-th">
                            Количество в заказе
                        </th>
                        <th class="spare-order-th">
                            Пришедшее количество
                        </th>
                        <th class="spare-order-th">
                            Добавление
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in order.items"
                        :key="item.id"
                        :style="setBackground(item)"
                    >
                        <td>{{ item.nameSpare }}</td>
                        <td class="spare-order-td">
                            {{ formatPrice(item.itemPrice) }}
                        </td>
                        <td class="spare-order-td">
                            {{ item.countInOrder }}
                            <span v-if="item.subCount">(по {{ item.subCount }} {{ item.spareUnits }})</span>
                        </td>
                        <td class="spare-order-td">
                            {{ calcReceived(item) }}
                        </td>
                        <td class="spare-order-td">
                            <span
                                v-if="isAvailable(item)"
                                class="forClick"
                                data-target="#addReceiptModal"
                                data-toggle="modal"
                                @click="openModalForAdd(item)"
                            >
                                Добавить
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<style>
    .spare-order-th {
        width: 20%
    }
    .spare-order-td {
        text-align: center;
    }
    .order-table-title-alignment {
        text-align: left
    }
</style>
