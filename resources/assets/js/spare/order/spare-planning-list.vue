<script>
    export default {
        props: {
            plannings: {
                type: Array,
                default() {
                    return [{
                        comment: '',
                        data: [{
                            countInPlanning: '',
                            countOrdered: 0,
                            id: 0,
                            isComposite: 0,
                            nameSpare: '',
                            planningItemId: 0,
                            spareId: 0,
                            spareUnits: '',
                        }],
                        date: '',
                        employee: 0,
                        number: 0,
                        status: {},
                        vehicle: '',
                    }];
                },
            },
            eddMsg: {
                type: String,
                default: '',
            },
            spares: {
                type: String,
                default: '',
            },
            positions: {
                type: Array,
                default() {
                    return {
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
                    };
                },
            },
            order: {
                type: Array,
                default() {
                    return [];
                },
            },
        },
        data() {
            return {
                spareNew: '',
                countNew: '',
            };
        },
        methods: {
            openModalForAdd(order, item) {
                this.$emit('openModalForAdd', {
                    planningItemId: item.planningItemId,
                    nameSpare: item.nameSpare,
                    spareId: item.spareId,
                    spareUnits: item.spareUnits,
                    count: item.isComposite ? 1 : this.calcCountForOrdered(item), // количество заказываемое
                    countInOrder: this.calcCountForOrdered(item), // количество в плане (верхняя граница)
                    countRestForOrder: this.calcCountForRest(item),
                    date: order.date,
                    number: order.number,
                    isComposite: item.isComposite,
                    subCount: item.isComposite ? this.calcCountForOrdered(item) : '',
                    price: '',
                });
            },
            notIssetInPositions(index) {
                return !(index in this.positions);
            },
            setRedColor(index) {
                if (!this.notIssetInPositions(index)) {
                    return {
                        color: '#D20813',
                        fontWeight: 600,
                    };
                }

                return false;
            },
            setBackground(index) {
                if (!this.notIssetInPositions(index)) {
                    return {
                        background: '#d2ba5c54',
                    };
                }

                return false;
            },
            calcCountForOrdered(item) {
                const countInOrderData = this.getCountInPosition(this.order[item.planningItemId]);
                const countInPosition = this.getCountInPosition(this.positions[item.planningItemId]);
                return item.countInPlanning * 1 - item.countOrdered * 1 + countInOrderData - countInPosition;
            },

            calcCountForRest(item) {
                const countInOrderData = this.getCountInPosition(this.order[item.planningItemId]);
                return item.countInPlanning * 1 - item.countOrdered * 1 + countInOrderData;
            },

            getCountInPosition(data) {
                if (!data) {
                    return 0;
                }
                if (!data.isComposite) {
                    return data.count;
                }
                return data.count * data.subCount;
            },
            isAvailable(item) {
                const ordered = item.countInPlanning - this.calcCountForOrdered(item);
                return (item.countInPlanning > ordered);
            },
            update(editablePosition) {
                if (editablePosition) {
                    this.highlightAppendedPosition(editablePosition);
                }
                this.$forceUpdate();
            },
            /**
             * Подсветить однажды выбранную позицию
             * @param editablePosition
             */
            highlightAppendedPosition(editablePosition) {
                for (const position of this.plannings) {
                    const searched = position.data.find((x) => x.planningItemId === editablePosition.planningItemId);

                    if (searched !== undefined) {
                        searched.highlighted = true;
                    }
                }
            },
        },
    };
</script>
<template>
    <div>
        <div
            v-for="(planning, index) in plannings"
            :key="index"
            class="rows"
        >
            <h4 style="text-align: left">
                Заявка №{{ planning.number }} от {{ planning.date }}
            </h4>
            <table class="table table-striped table-bordered main-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">
                            Запчасть
                        </th>
                        <th style="width: 25%;">
                            Количество в заявке
                        </th>
                        <th style="width: 25%;">
                            Заказанное количество
                        </th>
                        <th style="width: 25%;">
                            Добавление
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(item, itemIndex) in planning.data"
                        :key="itemIndex"
                        :class="{'highlighted-item' : item.highlighted}"
                        :style="setBackground(item.planningItemId)"
                    >
                        <td>{{ item.nameSpare }}</td>
                        <td style="text-align: center;">
                            {{ item.countInPlanning }} {{ item.spareUnits }}
                        </td>
                        <td
                            style="text-align: center;"
                            :style="setRedColor(item.planningItemId)"
                        >
                            {{ item.countInPlanning * 1 - calcCountForOrdered(item) *1 }} {{ item.spareUnits }}
                        </td>
                        <td style="text-align: center;">
                            <span
                                v-if="isAvailable(item)"
                                class="forClick"
                                @click="openModalForAdd(planning, item)"
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
    .highlighted-item {
        color: #c8b400;
    }
</style>
