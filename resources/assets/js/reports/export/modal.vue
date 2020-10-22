<script>
    export default {
        name: 'Modal',
        props: {
            data: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
            };
        },
        methods: {
            closeModal() {
                this.$emit('closeModal');
            },
        },
    };
</script>
<template>
    <div
        id="modalWindow"
        class="modalWindow fullscreen"
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
            @click="closeModal()"
        />
        <div
            class="modal-dialog modal-1350"
            style="z-index: 1050"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-hidden="true"
                        @click="closeModal()"
                    >
                        ×
                    </button>
                    <h4
                        class="modal-title"
                        style="text-align: left;"
                    >
                        {{ data['date'] }}&nbsp;{{ data['time'] }} {{ data['transnumb'] }} {{ data['department'] }}
                    </h4>
                </div>
                <div
                    id="modal-body"
                    class="modal-body"
                    style="text-align: center;max-width: 1350px !important;"
                >
                    <a
                        target="_blank"
                        :href="data['path'] + '_1.jpg'"
                    ><img
                        :src="data['path'] + '_1.jpg'"
                        class="img-rounded"
                        alt="Первое взвешивание"
                    ></a>
                    <a
                        target="_blank"
                        :href="data['path'] + '_2.jpg'"
                    ><img
                        :src="data['path'] + '_2.jpg'"
                        class="img-rounded"
                        alt="Второе взвешивание"
                    ></a>
                    <table
                        class="mytable table-bordered table-striped"
                        style="width: 90%;"
                    >
                        <thead>
                            <tr>
                                <th>Брутто</th><th>Тара</th><th>Нетто</th><th>Засор</th><th>Примеси</th><th>Масса</th>
                            </tr>
                            <tr>
                                <td>{{ data['brute'] }}&nbsp;кг.</td>
                                <td>{{ data['tare'] }}&nbsp;кг.</td>
                                <td>{{ data['netto'] }}&nbsp;кг.</td>
                                <td>{{ data['sor'] }}&nbsp;кг.</td>
                                <td>{{ data['trash'] }}&nbsp;кг.</td>
                                <td>{{ data['massa'] }}&nbsp;кг.</td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                        @click="closeModal()"
                    >
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
