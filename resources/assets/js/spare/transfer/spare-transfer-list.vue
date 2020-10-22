<script>
    export default {
        props: {
            positions: {
                type: Array,
                default() {
                    return [{
                        count: '',
                        spare: 0,
                    }];
                },
            },
            spares: {
                type: Array,
                default() {
                    return [{
                        text: '',
                        value: 0,
                    }];
                },
            },
            sparesObject: {
                type: Object,
                default() {
                    return {};
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
                    <td>{{ sparesObject[position.spare].text }}</td>
                    <td>{{ position.count }}</td>
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
