<script>

    export default {
        props: {
            documentNameProps: {
                type: String,
                default: '',
            },
            orderDate: {
                type: String,
                default: '',
            },
            positions: {
                type: Array,
                default() {
                    return [{
                        count: '',
                        countInPlanning: '',
                        countRestForOrder: 0,
                        date: '',
                        isComposite: 0,
                        itemId: 0,
                        nameSpare: '',
                        number: 0,
                        planningItemId: 0,
                        price: '',
                        spareId: 0,
                        spareUnits: '',
                        subCount: null,
                    }];
                },
            },
        },
        data() {
            return {
                documentName: this.documentNameProps,
                date: this.orderDate,
            };
        },
        methods: {
            delPosition(key) {
                this.$emit('delPosition', key);
            },
            openModalForEdit(key) {
                this.$emit('openModalForEdit', key);
            },
            getCountInPosition(key) {
                if (!this.positions[key].isComposite) {
                    return this.positions[key].count;
                }
                return this.positions[key].count * this.positions[key].subCount;
            },
            setRedColor(key) {
                if (this.getCountInPosition(key) < this.positions[key].countRestForOrder) {
                    return {
                        color: '#D20813',
                        fontWeight: 600,
                    };
                }

                return false;
            },
            getSubCount(key) {
                if (this.positions[key].isComposite) {
                    return `${this.positions[key].subCount} ${this.positions[key].spareUnits}`;
                }
                return 'Позиция неделимая';
            },
            toCurrency(value) {
                const formatter = new Intl.NumberFormat('ru', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 0,
                });
                return formatter.format(value);
            },
        },
    };
</script>
<template>
    <div class="">
        <h4 style="text-align: left; ">
            Позиции заказа:
        </h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Название</th>
                    <th class="limitShowMobile">
                        В заявке
                    </th>
                    <th>Заказанное количество</th>
                    <th class="limitShowMobile">
                        Количество единиц в упаковке
                    </th>
                    <th>Цена</th>
                    <th>Редактировать</th>
                    <th>Удалить</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(position, key) in positions"
                    :key="key"
                >
                    <td>{{ position.nameSpare }}</td>
                    <td
                        class="limitShowMobile"
                        :style="setRedColor(key)"
                    >
                        {{ position.countRestForOrder }}
                    </td>
                    <td
                        style="text-align: center"
                        :style="setRedColor(key)"
                    >
                        {{ position.count }} шт.
                    </td>
                    <td class="limitShowMobile">
                        {{ getSubCount(key) }}
                    </td>
                    <td class="limitShowMobile">
                        {{ toCurrency(position.price) }}
                    </td>
                    <td>
                        <span
                            style="cursor: pointer;"
                            @click="openModalForEdit(key)"
                        >
                            <img
                                src="/images/edit.png"
                                alt="Редактировать"
                            >
                        </span>
                    </td>
                    <td>
                        <span
                            style="cursor: pointer;"
                            @click="delPosition(key)"
                        >
                            <img
                                src="/images/del.png"
                                alt="Удалить"
                            >
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
