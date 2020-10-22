<script>
    export default {
        props: {
            positions: {
                type: Array,
                default() {
                    return [{
                        countInPlanning: '',
                        id: 0,
                    }];
                },
            },
            spares: {
                type: Array,
                default() {
                    return [{
                        isComposite: false,
                        text: '',
                        units: '',
                        value: 0,
                    }];
                },
            },
            sparesObject: {
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
            Позиции:
        </h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Ед. изм</th>
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
                    <td>{{ sparesObject[position.id].text }}</td>
                    <td>{{ position.countInPlanning }}</td>
                    <td>{{ sparesObject[position.id].units }}</td>
                    <td>
                        <span
                            style="cursor: pointer;"
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
