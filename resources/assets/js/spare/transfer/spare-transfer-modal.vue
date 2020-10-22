<script>
    import { ModelSelect } from 'vue-search-select';

    export default {
        components: {
            'model-select': ModelSelect,

        },
        props: {
            spares: {
                type: Array,
                default() {
                    return [{
                        text: '',
                        value: 0,
                    }];
                },
            },
            editMsg: {
                type: String,
                default: '',
            },
            editLinePosition: {
                type: Object,
                default() {
                    return {
                        count: '',
                        idList: 0,
                        spare: 0,
                    };
                },
            },
        },
        data() {
            return {
                spareEdit: '',
                countEdit: '',
            };
        },
        methods: {
            closeEditForm() {
                this.$emit('closeEditForm');
            },
            editPosition() {
                this.$validator.validateAll().then((validationSuccessed) => {
                    if (validationSuccessed) {
                        this.$emit('editPosition', this.editLinePosition);
                    }
                });
            },
        },
    };
</script>
<template>
    <div
        v-if="Object.entries(editLinePosition).length !== 0"
        id="modalCenter"
    >
        <div
            id="modalWindow"
            class="modalWindow"
        >
            <div class="modal-backdrop fade in" />
            <div
                id="myModal"
                class="modal fade in"
                tabindex="-1"
                role="dialog"
                aria-labelledby="myModalLabel"
                aria-hidden="false"
                style="display: block;"
                @click="closeEditForm()"
            />
            <div
                class="modal-dialog"
                style="z-index: 1050"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-hidden="true"
                            @click="closeEditForm()"
                        >
                            ×
                        </button>
                        <h4
                            id="myLargeModalLabel"
                            class="modal-title"
                        >
                            Редактирование позиции #{{ editLinePosition.idList + 1 }}
                        </h4>
                        <p
                            v-if="editMsg"
                            style="color: red"
                        >
                            {{ editMsg }}
                        </p>
                    </div>
                    <div
                        id="modal-body"
                        class="modal-body"
                    >
                        <div
                            class="row"
                            style="margin-top: 5px; "
                        >
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="modalPosition">Позиция</label>
                                    <div class="searchMenu">
                                        <model-select
                                            id="modalPosition"
                                            v-model="editLinePosition.spare"
                                            :options="spares"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="modalCount">Количество</label>
                                    <input
                                        id="modalCount"
                                        v-model="editLinePosition.count"
                                        v-validate="'min:1'"
                                        min="1"
                                        type="number"
                                        class="form-control"
                                        autocomplete="off"
                                        data-vv-validate-on="change|blur"
                                        :name="'кол-во'"
                                        @keyup.enter="editPosition()"
                                    >
                                    <span class="validation-error-message">{{ errors.first('кол-во') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-default"
                            data-dismiss="modal"
                            @click="closeEditForm()"
                        >
                            Закрыть
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="editPosition()"
                        >
                            Сохранить изменения
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .validation-error-message {
        color: #ff0000;
        font-size: 10pt
    }
</style>
