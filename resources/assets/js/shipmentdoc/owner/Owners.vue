<script>
    import { httpRequestWrapperMixin } from '../../common/utils/HttpRequestWrapperMixin';

    export default {
        mixins: [httpRequestWrapperMixin],
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
        },
        created() {
            this.$store.commit('setApiUrl', this.apiUrl);
        },
        methods: {
            onOwnerSave() {
                this.$router.push('/');
            },
            onOwnerAddCancel() {
                this.$router.push('/');
            },
        },
    };
</script>
<template>
    <div>
        <notifications />
        <nav style="margin-bottom: 20px;">
            <router-link
                class="btn btn-default"
                to="/add"
            >
                Добавить
            </router-link>
        </nav>
        <router-view
            @onOwnerSave="onOwnerSave"
            @onOwnerAddCancel="onOwnerAddCancel"
        />
    </div>
</template>
