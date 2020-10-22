<script>
    import Moment from 'moment';
    import ReceiptList from './spare-receipt-list.vue';
    import ReceiptModal from './spare-receipt-modal.vue';
    import OrderList from './spare-orders-list.vue';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        components: {
            'spare-modal': ReceiptModal,
            'receipt-list': ReceiptList,
            'order-list': OrderList,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            apiUrl: {
                type: String,
                required: true,
                default: '',
            },
            action: {
                type: String,
                required: true,
                default: '',
            },
            roles: {
                type: Array,
                default() {
                    return [];
                },
            },
            sellerList: {
                type: Array,
                default() {
                    return [{
                        default: false,
                        text: '',
                        value: 0,
                    }];
                },
            },
            receipt: {
                type: Object,
                default() {
                    return {
                        date: '',
                        documentName: '',
                        id: 0,
                        items: [{
                            count: '',
                            countInOrder: '',
                            countRestForReceipt: 0,
                            date: '',
                            edited: false,
                            id: 0,
                            isComposite: 0,
                            itemPrice: '',
                            nameSpare: '',
                            number: 0,
                            orderDate: '',
                            orderItemId: 0,
                            spareUnits: '',
                            subCount: null,
                        }],
                        provider: 0,
                    };
                },
            },
        },
        data() {
            return {
                addableReceiptItem: {},
                documentName: this.receipt.documentName ? this.receipt.documentName : '',
                provider: this.receipt.provider ? this.receipt.provider : 0,
                savingEnable: true,
                receiptId: this.receipt.id ? this.receipt.id : '',
                date: this.receipt.date ? this.receipt.date : Moment().format('YYYY-MM-DD'),
                waitWhileSaving: false,
            };
        },
        computed: {
            sellers() {
                return this.$store.state.sellers;
            },
            receiptItems: {
                get() {
                    return this.$store.state.receiptItems;
                },
                set(value) {
                    this.$store.state.receiptItems = value;
                },
            },
        },
        created() {
            this.$store.commit('setApiUrl', this.apiUrl);
            this.$store.commit('setSellers', this.sellerList);
            this.$store.commit('setReceiptItems', this.receipt);
        },
        provide() {
            return {
                parentValidator: this.$validator,
            };
        },
        methods: {
            save() {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        const formData = new FormData();

                        formData.append('date', this.$refs.receiptList.date
                            ? Moment(this.$refs.receiptList.date).format('YYYY-MM-DD')
                            : '');
                        formData.append('documentName', this.$refs.receiptList.documentName);
                        formData.append('positions', JSON.stringify(this.receiptItems));
                        formData.append('receiptId', this.receiptId);

                        this.httpPostRequest(`${this.apiUrl}/${this.action}`, formData).then(() => {
                            window.location.href = this.apiUrl;
                        });
                    }
                });
            },
            openModalForAdd(receiptItemToAdd) {
                this.addableReceiptItem = receiptItemToAdd;
            },
            closeAddForm() {
                this.addableReceiptItem = {};
            },
            isEnableSave() {
                if (!this.roles.includes('admin')
                    && !this.roles.includes('officecash')
                    && Moment().diff(this.date, 'days') > 14) {
                    return false;
                }
                return this.savingEnable;
            },
        },
    };
</script>
<template>
    <div class="spare-receipt-form-container">
        <notifications />

        <spare-modal
            :addable-receipt-item="addableReceiptItem"
            @closeAddForm="closeAddForm"
        />

        <receipt-list
            ref="receiptList"
            :roles="roles"
            :receipt-date="date"
            :sellers="sellers"
            :provider-props="provider"
            :document-name-props="documentName"
        />

        <div class="buttons-span">
            <span
                v-if="receiptItems.length > 0 && isEnableSave()"
                :disabled="waitWhileSaving"
                class="btn btn-default"
                @click="save()"
            >
                Сохранить
            </span>
            <a
                :href="apiUrl"
                class="btn btn-default"
            >Отмена</a>
        </div>

        <order-list
            ref="orderList"
            :action="action"
            @openModalForAdd="openModalForAdd"
        />
    </div>
</template>

<style>
    .buttons-span {
        text-align: right;
        margin-top: 10px;
    }

    .spare-receipt-form-container {
        margin-left: 20px
    }
</style>
