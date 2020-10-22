<script>
    export default {
        props: {
            apiUrl: {
                type: String,
                required: true,
            },
            permissions: {
                type: Object,
                required: true,
            },
        },
        created() {
            this.$store.commit('setApiUrl', this.apiUrl);
            this.$store.commit('setPermissions', this.permissions);
        },
        methods: {
        },
    };
</script>

<template>
    <router-view />
</template>
