<script>
    import DatePicker from 'vue2-datepicker';
    import Moment from 'moment';
    import Autocomplete from 'vue2-autocomplete-js';
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        components: {
            'date-picker': DatePicker,
            Autocomplete,
        },
        mixins: [httpRequestWrapperMixin],
        props: {
            apiUrl: {
                type: String,
                default: '',
            },
            departments: {
                type: Array,
                default() {
                    return [{
                        id: 0,
                        name: '',
                        alias: '',
                        type: '',
                    }];
                },
            },
        },
        data() {
            return {
                filter: {
                    startDate: new Date(),
                    endDate: new Date(),
                    comment: '',
                    type: '',
                    department: 0,
                },
            };
        },
        computed: {
            formattedStartDate: {
                set(value) {
                    this.filter.startDate = Moment(value).format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.startDate).format('YYYY-MM-DD');
                },
            },
            formattedEndDate: {
                set(value) {
                    this.filter.endDate = Moment(value).format('YYYY-MM-DD');
                },
                get() {
                    return Moment(this.filter.endDate).format('YYYY-MM-DD');
                },
            },
        },
        methods: {
            search() {
                this.$emit('onSearch', {
                    formattedStartDate: this.formattedStartDate,
                    formattedEndDate: this.formattedEndDate,
                    filter: {
                        comment: this.filter.comment,
                        type: this.filter.type,
                        department: this.filter.department,
                    },
                });
            },
            setNumber(value) {
                this.filter.comment = value.transnumb;
            },
            setInput(value) {
                this.filter.comment = value;
            },
        },
    };
</script>

<template>
    <div
        id="DepExportFilter"
        class="form-inline form-filter"
    >
        <select
            v-model="filter.type"
            class="form-control"
        >
            <option value="">
                Тип
            </option>
            <option value="1">
                Экспорт
            </option>
            <option value="2">
                Переброска
            </option>
        </select>
        <span style="display: inline-block;">
            <date-picker
                v-model="filter.startDate"
                input-class="form-control"
                format="YYYY-MM-DD"
                lang="ru"
            />
        </span>
        <span style="display: inline-block;">
            <date-picker
                v-model="filter.endDate"
                input-class="form-control"
                format="YYYY-MM-DD"
                lang="ru"
            />
        </span>

        <select
            v-model="filter.department"
            class="form-control"
        >
            <option
                value="0"
                selected="selected"
            >
                Все подразделения
            </option>
            <option
                v-for="(department, index) in departments"
                :key="index"
                :value="department.id"
            >
                {{ department.name }}
            </option>
        </select>
        <div style="display: inline-block">
            <autocomplete
                :classes="{input: 'form-control ui-autocomplete-input', list: 'data-list'}"
                :url="apiUrl + '/get-numbers'"
                anchor="transnumb"
                label=""
                :min="2"
                :debounce="1000"
                :on-select="setNumber"
                :on-input="setInput"
                placeholder="Номер"
            />
        </div>
        <input
            id="submitbutton"
            type="submit"
            name="submit"
            class="btn btn-default"
            value="Фильтр"
            @click="search"
        >
    </div>
</template>
