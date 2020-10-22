<script>
    import { formatPrice } from '../../util/formatter';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        mixins: [httpRequestWrapperMixin],
        props: {
            items: {
                type: Array,
                default() {
                    return [{
                        bank: '',
                        date: '',
                        id: 0,
                        money: '',
                        provider: '',
                        trader: '',
                    }];
                },
            },
            permissions: {
                type: Object,
                default() {
                    return {
                        add: false,
                        confirm: false,
                        delete: false,
                        edit: false,
                    };
                },
            },
            sum: {
                type: Number,
                default: 0,
            },
            editUrl: {
                type: String,
                default: '',
            },
            deleteUrl: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                formatPrice,
                item: {},
            };
        },
    };
</script>

<template>
    <div>
        <notifications />

        <table class="table table-striped table-bordered main-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Плательщик</th>
                    <th>Контрагент</th>
                    <th>Счет</th>
                    <th>Сумма</th>
                    <th v-if="permissions['edit']" />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(paymentItem, index) of items"
                    :key="index"
                >
                    <td class="date">
                        {{ paymentItem.date }}
                    </td>
                    <td class="date">
                        {{ paymentItem.provider }}
                    </td>
                    <td class="date">
                        {{ paymentItem.trader }}
                    </td>
                    <td class="date">
                        {{ paymentItem.bank }}
                    </td>
                    <td class="num">
                        {{ formatPrice(paymentItem.money) }}
                    </td>
                    <td
                        v-if="permissions['edit']"
                        class="action"
                    >
                        <a :href="editUrl + '/' + paymentItem.id"><img
                            src="/images/edit.png"
                            alt="Редактировать"
                        ></a>
                        <a
                            class="confirm"
                            :href="deleteUrl + '/' + paymentItem.id"
                        ><img
                            src="/images/del.png"
                            alt="Удалить"
                        ></a>
                    </td>
                </tr>
                <tr>
                    <td />
                    <td />
                    <td />
                    <td />
                    <td class="total num">
                        {{ formatPrice(sum) }}
                    </td>
                    <td v-if="permissions['edit']" />
                </tr>
            </tbody>
        </table>
    </div>
</template>
