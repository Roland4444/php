<script>
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';
    import ConfirmationModal from '../../common/confirmation_modal.vue';

    export default {
        components: {
            'confirmation-modal': ConfirmationModal,
        },
        mixins: [httpRequestWrapperMixin],
        data() {
            return {
                items: [],
                deletableItemId: 0,
            };
        },
        computed: {
            apiUrl() {
                return `${this.$store.state.apiUrl}/owner`;
            },
        },
        created() {
            this.refreshList();
        },
        methods: {
            refreshList() {
                this.httpGetRequest(`${this.apiUrl}/list`).then((response) => {
                    this.items = response.body.data;
                });
            },
            del(id) {
                this.deletableItemId = id;
            },
            confirm() {
                this.httpPostRequest(`${this.apiUrl}/delete/${this.deletableItemId}`, null).then(() => {
                    this.refreshList();
                });
            },
        },
    };
</script>
<template>
    <div>
        <notifications />
        <table class="table table-striped table-bordered main-table">
            <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Телефон</th>
                    <th />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in items"
                    :key="index"
                >
                    <td>{{ item.name }}</td>
                    <td>{{ item.phone }}</td>
                    <td>
                        <router-link :to="{name: 'edit', params: {id: item.id}}">
                            <img
                                src="/images/edit.png"
                                alt="Редактировать"
                            >
                        </router-link>
                        <span
                            data-toggle="modal"
                            data-target="#confirmationModal"
                        >
                            <img
                                class="confirm"
                                style="cursor: pointer"
                                src="/images/del.png"
                                alt="Удалить"
                                @click="del(item.id)"
                            >
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <confirmation-modal
            @onConfirmation="confirm"
        >
            <span>Удалить?</span>
        </confirmation-modal>
    </div>
</template>
