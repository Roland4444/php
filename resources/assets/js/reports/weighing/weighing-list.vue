<script>
    import Moment from 'moment';
    import ConfirmationModal from '../../common/confirmation_modal.vue';
    import { formatPrice, formatWeight } from '../../util/formatter';
    import WeighingEdit from './weighing-edit.vue';
    import WeighingPhotoFullModal from './weighing-photo-full-modal.vue';
    import WeighingPaymentModal from './weighing-payment-modal.vue';
    import WeighingPurchaseModal from './weighing-purchase-modal.vue';
    import WeighingGrouppingByMetalsModal from './weighing-groupping-by-metals-modal.vue';

    export default {
        name: 'WeighingList',
        components: {
            'weighing-edit': WeighingEdit,
            'weighing-photo-full-modal': WeighingPhotoFullModal,
            'weighing-payment-modal': WeighingPaymentModal,
            'delete-confirmation-modal': ConfirmationModal,
            'weighing-purchase-modal': WeighingPurchaseModal,
            'weighing-groupping-by-metals-modal': WeighingGrouppingByMetalsModal,
        },
        props: {
            weighings: {
                type: Array,
                required: true,
            },
            customers: {
                type: Array,
                required: true,
            },
            departments: {
                type: Array,
                required: true,
            },
            currentDepartment: {
                type: Object,
                required: false,
                default: null,
            },
            fullAccess: {
                type: Number,
                required: false,
                default: 0,
            },
            deleteUrl: {
                type: String,
                required: true,
            },
            deleteItemUrl: {
                type: String,
                required: true,
            },
            updateUrl: {
                type: String,
                required: true,
            },
            payUrl: {
                type: String,
                required: true,
            },
            purchaseSaveUrl: {
                type: String,
                required: true,
            },
            fullImageUrl: {
                type: String,
                required: true,
            },
            thumbnailImageUrl: {
                type: String,
                required: true,
            },
            weighingsGrouppedByMetal: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                editableWeighing: {
                    weighings: [],
                    editableCustomer: [],
                    customer: {
                        value: 0,
                        text: '',
                    },
                },
                fullImagePath: '',
                payableWeighing: {
                    id: 0,
                    totalPrice: 0,
                },
                payablePrice: 0,
                momentPlugin: Moment,
                formatters: {
                    formatPrice, formatWeight,
                },
                deletableHref: '',
            };
        },
        methods: {
            editWeighing(weighing) {
                this.editableWeighing = weighing;
                this.editableWeighing.oldCustomer = this.editableWeighing.customer;

                for (const weighingItem of this.editableWeighing.weighings) {
                    weighingItem.newPrice = weighingItem.price;
                    weighingItem.calculatedMass = (weighingItem.mass * weighingItem.newPrice).toFixed(2);
                }
            },
            showFullImage(weighingItemId) {
                this.fullImagePath = `${this.fullImageUrl}/${weighingItemId}`;
            },
            openPayableModal(weighing) {
                this.$refs.paymentModal.diamondPayment = 0;
                this.payableWeighing = weighing;
                const diff = this.payableWeighing.totalPrice - this.payableWeighing.totalPaidAmount;
                this.payableWeighing.payableAmount = diff.toFixed(2);
            },
            onPaymentUpdate(payableWeighing) {
                const weighingIndex = this.weighings.map((e) => e.id).indexOf(payableWeighing.id);

                this.payableWeighing = {};

                this.weighings[weighingIndex].totalPaidAmount += parseFloat(payableWeighing.payableAmount);
            },
            resolveDeletableEntity(entityType, entity) {
                if (entityType === 'weighing') {
                    this.deletableHref = `${this.deleteUrl}/${entity.id}`;
                } else if (entityType === 'weighingItem') {
                    this.deletableHref = `${this.deleteItemUrl}/${entity.id}`;
                }
            },
            deleteWeighing() {
                window.location.href = this.deletableHref;
            },
            weighingCustomer(weighing) {
                if (weighing.customer) {
                    return `:: ${weighing.customer.text}`;
                }

                return '';
            },
            totalSumFormatted() {
                let totalSum = 0;

                for (const weighing of this.weighings) {
                    totalSum += weighing.totalPrice;
                }
                return this.formatters.formatPrice(totalSum);
            },
            totalMassFormatted() {
                let totalMass = 0;

                for (const weighing of this.weighings) {
                    totalMass += weighing.totalMass;
                }
                return this.formatters.formatWeight(totalMass);
            },
            onEdit(editableWeighing) {
                const elIndex = this.weighings.map((x) => x.id).indexOf(editableWeighing.id);
                this.weighings[elIndex] = editableWeighing;
                this.$forceUpdate();
            },
            isLittleDifference(total, paid) {
                const diff = parseFloat(total) - parseFloat(paid);
                return (diff >= 0 && diff < 2) || diff < 0;
            },
        },
    };
</script>
<template>
    <div>
        <notifications />

        <button
            type="button"
            class="btn btn-default"
            data-target="#weighingReceiptModal"
            data-toggle="modal"
        >
            Оформить приход за день
        </button>
        <span class="weighing-total-info">
            Общая масса:
            <strong>
                {{ totalMassFormatted() }} кг
            </strong>
            :: Общая сумма:
            <strong>
                {{ totalSumFormatted() }}
            </strong>
            <button
                v-if="weighingsGrouppedByMetal.length !== 0"
                class="like-a-link-button"
                data-target="#groupWeighingByMetalModal"
                data-toggle="modal"
            >
                по видам лома
            </button>
        </span>
        <hr>
        <div
            v-for="weighing in weighings"
            :key="weighing.id"
            class="weighing-block"
        >
            <p class="total">
                {{ weighing.date }} {{ weighing.time }} :: Накладная: {{ weighing.waybill }} ::
                {{ weighing.department.text }} :: {{ weighing.comment }} {{ weighingCustomer(weighing) }}

                <span
                    v-if="((weighing.date === momentPlugin().format('YYYY-MM-DD'))
                        && !weighing.hasBeenPaid) || fullAccess"
                >
                    <a
                        href="#"
                        class="btn confirm"
                        data-toggle="modal"
                        data-target="#weighingEditModal"
                        @click="editWeighing(weighing)"
                    >
                        Изменить цены
                    </a>
                </span>

                <span
                    v-if="weighing.totalPrice !== 0"
                    style="margin-right: 25px"
                >
                    <a
                        v-if="!isLittleDifference(weighing.totalPrice, weighing.totalPaidAmount)"
                        type="button"
                        class="btn"
                        data-target="#weighingPaymentModal"
                        data-toggle="modal"
                        @click="openPayableModal(weighing)"
                    >
                        Оплата
                    </a>
                    <span
                        v-else
                        style="color: #2ab708"
                    >
                        <strong>
                            Оплачено
                        </strong>
                    </span>
                </span>

                <span v-if="fullAccess">
                    <a
                        class="confirm"
                        href="#"
                        data-toggle="modal"
                        data-target="#confirmationModal"
                        @click="resolveDeletableEntity('weighing', weighing)"
                    >
                        <img
                            src="/images/del.png"
                            alt="Удалить"
                        >
                    </a>
                </span>
            </p>
            <table class="mytable table-striped table-bordered main-table">
                <thead>
                    <tr>
                        <th style="width: 200px;">
                            Вид лома
                        </th>
                        <th style="width: 80px;">
                            Брутто, кг.
                        </th>
                        <th style="width: 80px;">
                            Тара, кг.
                        </th>
                        <th style="width: 80px;">
                            Примеси, кг.
                        </th>
                        <th style="width: 80px;">
                            Засор, %
                        </th>
                        <th style="width: 200px;">
                            Масса, кг.
                        </th>
                        <th style="width: 200px;">
                            Цена за кг., руб.
                        </th>
                        <th style="width: 200px;">
                            Итого, руб.
                        </th>
                        <th style="width: 60px;">
                            Изображение
                        </th>
                        <th
                            v-if="fullAccess"
                            style="width: 60px;"
                        >
                            Удалить
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="weighingItem in weighing.weighings"
                        :key="weighingItem.id"
                    >
                        <td>
                            {{ weighingItem.metal.name }}
                        </td>
                        <td class="num">
                            {{ formatters.formatWeight(weighingItem.brutto) }} кг.
                        </td>
                        <td class="num">
                            {{ formatters.formatWeight(weighingItem.tare) }} кг.
                        </td>
                        <td class="num">
                            {{ formatters.formatWeight(weighingItem.trash) }} кг.
                        </td>
                        <td class="num">
                            {{ weighingItem.clogging }} %
                        </td>
                        <td class="num">
                            {{ formatters.formatWeight(weighingItem.mass) }} кг.
                        </td>
                        <td
                            v-if="weighingItem.price"
                            style="text-align: center;"
                        >
                            {{ formatters.formatPrice(weighingItem.price) }}
                        </td>
                        <td v-else />
                        <td
                            v-if="weighingItem.price"
                            class="num"
                        >
                            {{ formatters.formatPrice(weighingItem.totalPrice) }}
                        </td>
                        <td v-else />
                        <td>
                            <p
                                data-toggle="modal"
                                data-target="#weighingPhotoFullModal"
                                @click="showFullImage(weighingItem.id)"
                            >
                                <img
                                    class="weighing-image"
                                    :src="thumbnailImageUrl + '/' + weighingItem.id"
                                    alt="Изображение отсутствует"
                                >
                            </p>
                        </td>
                        <td v-if="fullAccess">
                            <div class="weighing-item-del-div">
                                <a
                                    class="confirm"
                                    href="#"
                                    data-toggle="modal"
                                    data-target="#confirmationModal"
                                    @click="resolveDeletableEntity('weighingItem', weighingItem)"
                                >
                                    <img
                                        src="/images/del.png"
                                        alt="Удалить"
                                        class="weighing-item-del-image"
                                    >
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="total">
                        <td />
                        <td />
                        <td />
                        <td />
                        <td />
                        <td class="num">
                            {{ formatters.formatWeight(weighing.totalMass) }} кг.
                        </td>
                        <td />
                        <td class="num">
                            {{ formatters.formatPrice(weighing.totalPrice) }}
                        </td>
                        <td />
                        <td v-if="fullAccess" />
                    </tr>
                </tbody>
            </table>
        </div>

        <weighing-edit
            :update-url="updateUrl"
            :editable-weighing="editableWeighing"
            :customers="customers"
            @onEdit="onEdit"
        />

        <weighing-photo-full-modal :image-path="fullImagePath" />

        <weighing-payment-modal
            ref="paymentModal"
            :payable-weighing="payableWeighing"
            :pay-url="payUrl"
            @onPaymentUpdate="onPaymentUpdate"
        />

        <delete-confirmation-modal @onConfirmation="deleteWeighing">
            <span>Удалить?</span>
        </delete-confirmation-modal>

        <weighing-purchase-modal
            :departments="departments"
            :current-department="currentDepartment"
            :full-access="fullAccess"
            :purchase-save-url="purchaseSaveUrl"
        />

        <weighing-groupping-by-metals-modal :weighings-groupped-by-metal="weighingsGrouppedByMetal" />
    </div>
</template>
<style>
    .weighing-image {
        width: 50px;
        height: 50px;
    }
    .weighing-block {
        margin-bottom: 10px;
    }

    .weighing-item-del-image {
        width: 16px
    }

    .weighing-item-del-div {
        margin: 0 auto;
        width: 16px
    }

    .weighing-total-info {
        margin-left: 10px;

    }

    .like-a-link-button {
        padding-left: 10px !important;
        background: none!important;
        border: none;
        font-family: arial, sans-serif;
        color: #069;
        cursor: pointer;
    }
</style>
