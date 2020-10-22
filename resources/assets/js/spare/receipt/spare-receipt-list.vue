<script>
    import DatePicker from 'vue2-datepicker';

    export default {
        components: {
            'date-picker': DatePicker,
        },
        props: {
            roles: {
                type: Array,
                default() {
                    return [];
                },
            },
            sellers: {
                type: Array,
                default() {
                    return [{
                        default: false,
                        text: '',
                        value: 0,
                    }];
                },
            },
            providerProps: {
                type: Number,
                default: 0,
            },
            documentNameProps: {
                type: String,
                default: '',
            },
            receiptDate: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                documentName: this.documentNameProps,
                provider: this.providerProps,
                seller: '',
                date: this.receiptDate,
            };
        },
        computed: {
            receiptItems() {
                return this.$store.state.receiptItems;
            },
        },
        inject: ['parentValidator'],
        created() {
            this.$validator = this.parentValidator;
        },
        methods: {
            delReceiptItem(itemId) {
                this.$store.commit('delReceiptItem', itemId);
            },
            getSubCount(receiptItem) {
                if (receiptItem.isComposite) {
                    return receiptItem.subCount;
                }
                return 'Позиция неделимая';
            },
            updateReceiptItem(index) {
                if (!this.validate(this.receiptItems[index])) {
                    return;
                }

                this.receiptItems[index].edited = false;

                this.$store.commit('updateReceiptItems', this.receiptItems[index]);
                this.$forceUpdate();
            },
            validate(editableReceiptItem) {
                if (editableReceiptItem.count <= 0) {
                    this.$notify({ text: 'Количество должно быть не нулевым' });
                    return false;
                }

                if (editableReceiptItem.count > editableReceiptItem.countRestForReceipt) {
                    this.$notify({ text: 'Количество не должно превышать указанное в заявке' });
                    return false;
                }

                return true;
            },

        },
    };
</script>
<template>
    <div>
        <h4 class="title-alignment">
            Информация о приходе
        </h4>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <label>Дата прихода</label>
                    <date-picker
                        v-model="date"
                        input-class="form-control"
                        :disabled="!roles.includes('admin') && !roles.includes('officecash')"
                        lang="ru"
                        :first-day-of-week="1"
                    />
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="documentName">Документ</label>
                    <textarea
                        id="documentName"
                        v-model="documentName"
                        v-validate="'required'"
                        name="'документ'"
                        required
                        class="form-control"
                        rows="1"
                        data-vv-validate-on="change|blur"
                    />
                    <span class="validation-error-message">{{ errors.first("'документ'") }}</span>
                </div>
            </div>
        </div>

        <h4 class="title-alignment">
            Позиции прихода:
        </h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th class="limitShowMobile">
                        Остаток по заказу
                    </th>
                    <th>Полученное количество</th>
                    <th>Редактировать</th>
                    <th>Удалить</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(receiptItem, index) in receiptItems"
                    :key="index"
                >
                    <td>{{ index + 1 }}</td>
                    <td>{{ receiptItem.nameSpare }} {{ Number(receiptItem.itemPrice).toFixed(2) }}</td>
                    <td class="limitShowMobile">
                        {{ receiptItem.countRestForReceipt - receiptItem.count }}
                    </td>
                    <td class="td-alignment-center">
                        <span v-if="!receiptItem.edited">{{ receiptItem.count }}</span>
                        <span v-else>
                            <input
                                v-model="receiptItem.count"
                                type="number"
                                class="form-control"
                                autocomplete="off"
                                autofocus
                                min="1"
                                :max="receiptItem.countRestForReceipt"
                                @keyup.enter="updateReceiptItem(index)"
                            >
                        </span>
                    </td>
                    <td>
                        <span
                            v-if="!receiptItem.edited"
                            @click="receiptItem.edited = true; $forceUpdate()"
                        >
                            <img
                                src="/images/edit.png"
                                alt="Редактировать"
                                class="cursor-pointer"
                            >
                        </span>
                        <span
                            v-else
                            @click="updateReceiptItem(index)"
                        >
                            <img
                                src="/images/save.png"
                                alt="Сохранить"
                                class="cursor-pointer"
                            >
                        </span>
                    </td>
                    <td>
                        <span @click="delReceiptItem(receiptItem.orderItemId)">
                            <img
                                src="/images/del.png"
                                alt="Удалить"
                                class="cursor-pointer"
                            >
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style>
    .validation-error-message {
        color: #ff0000;
        font-size: 10pt
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .td-alignment-center {
        text-align: center
    }
    .title-alignment {
        text-align: left;
    }
</style>
