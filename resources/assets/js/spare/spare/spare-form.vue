<script>
    import UploadImage from '../../common/upload_image.vue';

    export default {
        name: 'SpareForm',
        components: {
            UploadImage,
        },
        props: {
            url: {
                type: String,
                required: true,
                default: '',
            },
            indexUrl: {
                type: String,
                required: true,
                default: '',
            },
            entity: {
                type: Object,
                required: true,
                default() {
                    return {
                        article: '',
                        composite: 0,
                        id: 0,
                        name: '',
                        units: '',
                    };
                },
            },
            imagesPaths: {
                type: Array,
                required: false,
                default() {
                    return [];
                },
            },
        },
        data() {
            return {
                name: '',
                comment: '',
                composite: false,
                units: 'шт.',
                errorMsg: '',
            };
        },
        created() {
            if (this.entity.name) {
                this.name = this.entity.name;
                this.comment = this.entity.comment;
                this.composite = this.entity.composite === 1;
                this.units = this.entity.units;
            }
        },
        mounted() {
            const self = this;
            if (this.imagesPaths) {
                this.imagesPaths.forEach((path) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', path, true);
                    xhr.responseType = 'blob';
                    xhr.onload = () => {
                        if (this.status === 200) {
                            const myBlob = this.response;
                            const file = new File([myBlob], 'fileNameGoesHere', { type: 'image/png' });
                            self.$refs.images.addFile(file);
                        }
                    };
                    xhr.send();
                });
            }
        },
        methods: {
            submit() {
                const files = this.$refs.images.getFiles();
                const formData = new FormData();

                Object.keys(files).forEach((key) => {
                    formData.append('images[]', files[key].file, files[key].name);
                });

                formData.append('name', this.name);
                formData.append('comment', this.comment);
                formData.append('isComposite', this.composite);
                formData.append('units', this.units);
                this.xhr(formData, () => {
                    this.redirectIndex();
                });
            },
            xhr(formData, callback) {
                const self = this;
                this.$http.post(this.url, formData).then(() => {
                    callback();
                }, (response) => {
                    self.errorMsg = response.body.error;
                });
            },
            redirectIndex() {
                document.location.href = this.indexUrl;
            },
        },
    };
</script>
<template>
    <div>
        <p
            v-if="errorMsg"
            style="color: red;"
        >
            {{ errorMsg }}
        </p>
        <form>
            <div class="form-group">
                <label class="col-sm-3 control-label">Картинки:</label>
                <div class="col-sm-4">
                    <upload-image
                        ref="images"
                        :max_files="5"
                        url=""
                    />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Наименование:</label>
                <div class="col-sm-4">
                    <input
                        v-model="name"
                        type="text"
                        class="form-control"
                        name="name"
                    >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Комментарий:</label>
                <div class="col-sm-4">
                    <input
                        v-model="comment"
                        type="text"
                        class="form-control"
                        name="comment"
                    >
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-3" />
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input
                                v-model="composite"
                                type="checkbox"
                            >
                            Составное - может быть разделено на несколько единиц (литров/шт и т.п.)
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Ед. изм.:</label>
                <div class="col-sm-4">
                    <input
                        v-model="units"
                        type="text"
                        class="form-control"
                        name="units"
                    >
                </div>
            </div>
            <div class="form-group ">
                <label class="col-sm-3 control-label" />
                <div class="col-sm-4">
                    <input
                        type="button"
                        class="btn btn-default"
                        value="Сохранить"
                        @click="submit"
                    >
                    <input
                        type="button"
                        class="btn btn-default"
                        value="Отмена"
                        @click="redirectIndex"
                    >
                </div>
            </div>
        </form>
    </div>
</template>
<style lang="css" scoped>
</style>
