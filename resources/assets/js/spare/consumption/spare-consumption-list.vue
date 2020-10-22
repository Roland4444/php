<script>
    export default {
        props: {
            positions: {
                type: Array,
                default() {
                    return {
                        comment: '',
                        editableSpare: {
                            isComposite: 0,
                            spareUnits: '',
                            text: '',
                            value: 0,
                        },
                        id: 0,
                        newQuantity: '',
                        quantity: '',
                        spare: {
                            isComposite: 0,
                            spareUnits: '',
                            text: '',
                            value: 0,
                        },
                        vehicle: {
                            text: '',
                            value: null,
                        },
                    };
                },
            },
            spares: {
                type: Array,
                default() {
                    return [{
                        spareUnits: '',
                        text: '',
                        total: 0,
                        value: 0,
                    }];
                },
            },
            sparesObject: {
                type: Object,
                default() {
                    return {
                        0: {
                            spareUnits: '',
                            spare_id: 0,
                            text: '',
                            total: 0,
                        },
                    };
                },
            },
        },
        methods: {
            delPosition(index) {
                this.$emit('delPosition', index);
            },
            openEditForm(index) {
                this.$emit('openEditForm', index);
            },
        },
    };
</script>
<template>
    <div class="">
        <h4 style="text-align: left; ">
            Позиции расхода:
        </h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Комментарий</th>
                    <th>Техника</th>
                    <th>Редактировать</th>
                    <th>Удалить</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(position, index) in positions"
                    :key="index"
                >
                    <td>{{ index + 1 }}</td>
                    <td>{{ position.spare.text }}</td>
                    <td>{{ position.quantity }} {{ position.spare.spareUnits }}</td>
                    <td>{{ position.comment }}</td>
                    <td v-if="position.vehicle">
                        {{ position.vehicle.text }}
                    </td>
                    <td v-else />
                    <td>
                        <span
                            style="cursor: pointer;"
                            data-toggle="modal"
                            data-target="#consumptionModalForm"
                            @click="openEditForm(index)"
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
    </div>
</template>
