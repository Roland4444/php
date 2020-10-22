<script>
    import { formatPrice, formatWeight } from '../../util/formatter';

    export default {
        name: 'WeighingGrouppingByMetalsModal',
        props: {
            weighingsGrouppedByMetal: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                formatters: {
                    formatPrice, formatWeight,
                },
            };
        },
    };
</script>

<template>
    <div
        id="groupWeighingByMetalModal"
        class="modal fade bs-example-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="groupWeighingByMetalModalLabel"
    >
        <div
            class="modal-dialog modal-lg"
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
                        id="groupWeighingByMetalModalLabel"
                        class="modal-title"
                    >
                        Взвешивания по видам лома
                    </h4>
                </div>
                <div class="modal-body">
                    <table class="mytable table-striped table-bordered main-table">
                        <thead>
                            <tr>
                                <th style="width: 200px;">
                                    Металл
                                </th>
                                <th style="width: 80px;">
                                    Масса, кг.
                                </th>
                                <th style="width: 80px;">
                                    Средняя цена, руб.
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in weighingsGrouppedByMetal"
                                :key="item.name"
                            >
                                <td>
                                    {{ item.name }}
                                </td>
                                <td class="num">
                                    {{ formatters.formatWeight(item.mass) }} кг.
                                </td>
                                <td class="num">
                                    {{ formatters.formatPrice(item.avg_price) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
