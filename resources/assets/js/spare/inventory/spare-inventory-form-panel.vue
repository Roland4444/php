<script>
    import Moment from 'moment';
    import { ModelSelect } from 'vue-search-select';
    import DatePicker from 'vue2-datepicker';

    export default {
        components: {
            'model-select': ModelSelect,
            'date-picker': DatePicker,
        },
        props: {
            roles: {
                type: Array,
                default() {
                    return [];
                },
            },
            enableSaving: {
                type: Number,
                default: 0,
            },
            spareList: {
                type: Object,
                default() {
                    return {
                        isComposite: false,
                        text: '',
                        units: '',
                        value: 0,
                    };
                },
            },
            inventory: {
                type: Object,
                default() {
                    return {
                        date: '',
                        inventoryId: 0,
                        positions: {
                            0: {
                                spareId: 0,
                                spareName: '',
                                totalFact: '',
                                totalFormal: '',
                            },
                        },
                    };
                },
            },
            spareTotal: {
                type: Array,
                default() {
                    return [{
                        spareUnits: '',
                        spare_id: 0,
                        text: '',
                        total: 0,
                    }];
                },
            },
            cancelUri: {
                type: String,
                default: '',
            },
            saveUri: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                msgResponse: '',
                inventoryId: this.inventory.inventoryId ? this.inventory.inventoryId : '',
                isEnableSaving: this.enableSaving,
                date: this.inventory.date ? this.inventory.date : Moment(Date.now()).format('YYYY-MM-DD'),
                positions: [],
                selectedSpare: null,
                idsSpareInPositions: [],
                optionsForSpareSelect: Object.values(this.spareList),
            };
        },
        created() {
            this.positions = this.getStartPositions();
            this.idsSpareInPositions = this.positions.map((position) => position.spareId);
        },
        methods: {
            getStartPositions() {
                const positions = [];
                const idsSpareInPositions = [];

                Object.keys(this.spareTotal).forEach((index) => {
                    const item = this.spareTotal[index];
                    const spareId = item.spare_id;
                    let totalFact;
                    if (this.inventory && this.inventory.positions && this.inventory.positions[spareId]) {
                        totalFact = this.inventory.positions[spareId].totalFact;
                    } else {
                        totalFact = item.total;
                    }

                    positions.push({
                        spareId: spareId * 1,
                        spareName: item.text,
                        totalFormal: item.total,
                        spareUnits: item.spareUnits,
                        totalFact,
                    });
                    idsSpareInPositions.push(spareId * 1);
                });

                if (!this.inventory || !this.inventory.positions) {
                    return positions;
                }
                for (const spareId in this.inventory.positions) {
                    if (idsSpareInPositions.indexOf(spareId * 1) === -1) {
                        const item = this.inventory.positions[spareId];

                        positions.push({
                            spareId: spareId * 1,
                            spareName: item.spareName,
                            totalFormal: item.totalFormal,
                            totalFact: item.totalFact,
                        });
                        idsSpareInPositions.push(spareId * 1);
                    }
                }
                return positions;
            },
            save() {
                this.msgResponse = '';
                if (this.positions.length < 1) {
                    this.msgResponse = 'Количество позиций в инвентаризации должно быть не нулевым';
                    return;
                }
                if (!this.date) {
                    this.msgResponse = 'Поля Дата обязательно для заполнения';
                    return;
                }

                this.isEnableSaving = false;
                const formData = new FormData();
                formData.append('positions', JSON.stringify(this.positions));
                formData.append('inventoryId', this.inventoryId ? this.inventoryId : '');

                formData.append('date', this.date
                    ? Moment(this.date).format('YYYY-MM-DD')
                    : '');

                this.$http.post(this.saveUri, formData).then((response) => {
                    if (response.body.status === 'ok') {
                        this.msgResponse = 'Сохранено успешно';
                        window.location.href = this.cancelUri;
                    } else {
                        this.msgResponse = response.body.error;
                        this.isEnableSaving = true;
                    }
                }, (response) => {
                    this.$notify({ text: response.body.error });
                    this.isEnableSaving = true;
                });
            },
            delPosition(index) {
                const { spareId } = this.positions;
                const indexInIdsSpareInPositions = this.idsSpareInPositions.indexOf(spareId);
                this.idsSpareInPositions.splice(indexInIdsSpareInPositions, 1);
                this.positions.splice(index, 1);
            },
            addPosition() {
                this.msgResponse = '';
                if (this.selectedSpare === null) {
                    this.msgResponse = 'Выберите позицию';
                    return;
                }

                const spareId = this.selectedSpare;
                this.msgResponse = '';
                if (this.idsSpareInPositions.indexOf(spareId) !== -1) {
                    this.msgResponse = 'Позиция уже в списке';
                    this.selectedSpare = null;
                    return;
                }
                this.idsSpareInPositions.push(spareId);
                this.positions.push({
                    spareId,
                    spareName: this.spareList[spareId].text,
                    totalFormal: 0,
                    totalFact: 0,
                });
                this.selectedSpare = null;
            },
            isEnableSave() {
                if (!this.roles.includes('admin')) {
                    const currentDate = Moment().format('YYYY-MM-DD');
                    if (this.date !== currentDate) {
                        return false;
                    }
                }
                return this.isEnableSaving;
            },
            differenceTotal(position) {
                return (position.totalFact - position.totalFormal).toFixed(2);
            },
        },
    };
</script>
<template>
    <div>
        <notifications />

        <div style="margin-left: 20px">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="form-group">
                        <label>Дата</label>
                        <date-picker
                            v-model="date"
                            :disabled="!roles.includes('admin')"
                            input-class="form-control"
                            lang="ru"
                            :first-day-of-week="1"
                        />
                    </div>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Позиция</th>
                        <th>Ед. изм.</th>
                        <th>Остаток по базе</th>
                        <th>Остаток по факту</th>
                        <th>Разница</th>
                        <th>Удалить</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(position, index) in positions"
                        :key="index"
                    >
                        <td>{{ index + 1 }}</td>
                        <td>{{ position.spareName }}</td>
                        <td>{{ position.spareUnits }}</td>
                        <td>{{ position.totalFormal }}</td>
                        <td>
                            <span v-if="isEnableSave()"><input
                                v-model="position.totalFact"
                                type="number"
                                step="0.01"
                            ></span>
                            <span v-else>{{ position.totalFact }}</span>
                        </td>
                        <!--                <td>{{position.totalFact - position.totalFormal}}</td>-->
                        <td>{{ differenceTotal(position) }}</td>
                        <td>
                            <span
                                v-if="! position.totalFormal && isEnableSave()"
                                style="cursor: pointer;"
                                @click="delPosition(index)"
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

            <div
                v-if="isEnableSave()"
                class="row"
                style="margin-top: 5px; "
            >
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="form-group">
                        <label for="position">Позиция</label>
                        <div class="searchMenu">
                            <model-select
                                id="position"
                                v-model="selectedSpare"
                                :options="optionsForSpareSelect"
                            />
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <p style="margin-top: 25px;">
                        <span
                            class="btn btn-default"
                            @click="addPosition()"
                        >Добавить позицию</span>
                    </p>
                </div>
            </div>

            <p
                v-if="msgResponse"
                style="color: red"
            >
                {{ msgResponse }}
            </p>

            <p style="text-align: right; margin-top: 10px;">
                <span
                    v-if="positions.length > 0 && isEnableSave()"
                    class="btn btn-default"
                    @click="save()"
                >Сохранить</span>
                <a
                    :href="cancelUri"
                    class="btn btn-default"
                >Отмена</a>
            </p>
        </div>
    </div>
</template>
