<script>
    export default {
        name: 'UploadImage',
        props: {
            inputId: {
                type: String,
                required: false,
                default: 'default',
            },
            url: {
                type: String,
                required: true,
                default: null,
            },
            name: {
                type: String,
                required: false,
                default: 'images[]',
            },
            maxBatch: {
                type: Number,
                required: false,
                default: 0,
            },
            maxFiles: {
                type: Number,
                required: false,
                default: 10,
            },
            maxFilesize: {
                type: Number,
                required: false,
                default: 8000,
            },
            resizeEnabled: {
                type: Boolean,
                required: false,
                default: false,
            },
            resizeMaxWidth: {
                type: Number,
                required: false,
                default: 800,
            },
            resizeMaxHeight: {
                type: Number,
                required: false,
                default: 600,
            },
        },
        data() {
            return {
                form: null,
                input: null,
                index: 0,
                total: 0,
                files: {},
                image: {},
                batch: {},
                onDragover: false,
                onUploading: false,
            };
        },
        mounted() {
            this.form = document.getElementById(`upload_image_form--${this.inputId}`);
            this.input = document.getElementById(`upload_image_form__input--${this.inputId}`);
            [
                'drag',
                'dragstart',
                'dragend',
                'dragover',
                'dragenter',
                'dragleave',
                'drop',
            ].forEach((event) => this.form.addEventListener(event, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }));

            ['dragover', 'dragenter'].forEach((event) => this.form.addEventListener(event, this.dragEnter));

            ['dragleave', 'dragend', 'drop'].forEach((event) => this.form.addEventListener(event, this.dragLeave));

            ['drop'].forEach((event) => this.form.addEventListener(event, this.fileDrop));

            ['change'].forEach((event) => this.input.addEventListener(event, this.fileDrop));

            this.form.addEventListener('click', () => {
                this.input.click();
            });
        },
        methods: {
            getFiles() {
                return this.files;
            },
            canXhr() {
                return this.total < this.maxFiles;
            },
            canUploadFile(key) {
                const file = this.files[key];
                return !(file.attempted || file.bad_size);
            },
            xhr(formData, keys, callback) {
                this.onUploading = true;
                this.$emit('upload-image-attempt', formData);

                keys.forEach((key) => {
                    this.$set(this.files[key], 'attempted', true);
                });

                this.$http.post(this.url, formData).then((response) => {
                    keys.forEach((key) => {
                        this.$set(this.files[key], 'uploaded', true);
                        this.total += 1;
                    });
                    this.$emit('upload-image-success', [formData, response]);
                }, (response) => {
                    this.$emit('upload-image-failure', [formData, response]);
                }).then(() => {
                    this.onUploading = false;
                    callback();
                });
            },
            upload() {
                if (!this.canXhr()) {
                    return false;
                }
                for (const key in this.files) {
                    if (this.canUploadFile(key)) {
                        const formData = new FormData();
                        formData.append(this.name, this.files[key].file, this.files[key].name);

                        this.xhr(formData, [key], this.upload);
                        return true;
                    }
                }
                return true;
            },
            upload_batch() {
                if (!this.canXhr()) {
                    return false;
                }
                for (const key in this.batch) {
                    if (Object.prototype.hasOwnProperty.call(this.batch, key)) {
                        this.xhr(this.batch[key].form, this.batch[key].keys, this.upload_batch);
                        delete this.batch[key];
                        return true;
                    }
                }
                return true;
            },
            create_batch() {
                let index = 0;
                let count = 0;

                this.batch = {};
                for (const key in this.files) {
                    if (this.canUploadFile(key)) {
                        if (this.batch[index] == null || count === this.maxBatch) {
                            index += 1;
                            count = 0;
                            this.batch[index] = {
                                form: new FormData(),
                                keys: [],
                            };
                        }
                        count += 1;
                        this.batch[index].keys.push(key);
                        this.batch[index].form.append(this.name, this.files[key].file, this.files[key].name);
                    }
                }
            },
            submit(e) {
                e.preventDefault();
                e.stopPropagation();

                if (!this.onUploading) {
                    if (this.maxBatch > 1) {
                        this.create_batch();
                        return this.upload_batch();
                    }
                    return this.upload();
                }
                return null;
            },
            dragEnter(e) {
                e.preventDefault();
                this.onDragover = true;
            },
            dragLeave(e) {
                e.preventDefault();
                this.onDragover = false;
            },
            fileDrop(e) {
                e.preventDefault();

                const newFiles = e.target.files || e.dataTransfer.files;

                for (const fileIndex in newFiles) {
                    if (Object.prototype.hasOwnProperty.call(newFiles, fileIndex)) {
                        this.$set(this.files, this.index, newFiles[fileIndex]);

                        if (newFiles[fileIndex].type.match(/image.*/)) {
                            this.fileInit(this.index);
                            this.fileRead(this.index);
                            this.index += 1;
                        }
                    }
                }
            },
            fileInit(key) {
                const file = this.files[key];

                this.files[key] = {
                    name: this.files[key].name,
                    file: this.files[key],
                };

                if ((file.size * 0.001) > this.maxFilesize) {
                    this.$set(this.files[key], 'bad_size', true);
                }
            },
            fileRead(key) {
                const reader = new FileReader();

                reader.addEventListener('load', () => {
                    this.$set(this.image, key, reader.result);

                    if (this.resizeEnabled) {
                        const imager = new Image();

                        imager.onload = () => {
                            let { width } = imager;
                            let { height } = imager;

                            if (width > this.resizeMaxWidth || height > this.resizeMaxHeight) {
                                if ((height / width) - (this.resizeMaxHeight / this.resizeMaxWidth) > 0) {
                                    width *= this.resizeMaxHeight / height;
                                    height = this.resizeMaxHeight;
                                } else {
                                    height *= this.resizeMaxWidth / width;
                                    width = this.resizeMaxWidth;
                                }
                            }
                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(imager, 0, 0, width, height);

                            const newImageData = canvas.toDataURL('image/png');

                            this.$set(this.image, key, newImageData);

                            const img = atob(newImageData.split(',')[1]);
                            const imgBuffer = [];
                            let i = 0;
                            while (i < img.length) {
                                imgBuffer.push(img.charCodeAt(i));
                                i += 1;
                            }
                            const u8Image = new Uint8Array(imgBuffer);

                            this.$set(this.files, key, {
                                name: this.files[key].name,
                                file: new Blob([u8Image], { filename: this.files[key].name }),
                            });
                        };
                        imager.src = reader.result;
                    }
                });
                reader.readAsDataURL(this.files[key].file);
            },
            fileDelete(e, key) {
                this.$delete(this.files, key);
                this.$delete(this.image, key);
            },
            fileView(e) {
                e.preventDefault();
                e.stopPropagation();
            },
            addFile(file) {
                this.$set(this.files, this.index, file);

                if (file.type.match(/image.*/)) {
                    this.fileInit(this.index);
                    this.fileRead(this.index);
                    this.index += 1;
                }
            },
        },
    };
</script>

<template>
    <div
        class="vue_component__upload--image"
        :class="{ 'dragover': onDragover }"
    >
        <form
            :id="'upload_image_form--' + inputId"
            enctype="multipart/form-data"
        >
            <div class="upload_image_form__thumbnails">
                <div
                    v-for="(value, key) in files"
                    :key="key"
                    class="upload_image_form__thumbnail"
                    :class="{ 'uploaded': value.uploaded, 'bad-size': value.bad_size }"
                    @click="fileView($event, key)"
                >
                    <span @click="fileDelete($event, key)">
                        &#x2716;
                    </span>
                    <img
                        :src="image[key]"
                        :class="{ 'show': image[key]}"
                        alt="image"
                    >
                </div>
            </div>
            <input
                :id="'upload_image_form__input--' + inputId"
                type="file"
                hidden
                multiple
            >
            <div>
                <input
                    type="button"
                    class="btn btn-default"
                    value="Загрузить картинки"
                >
            </div>
        </form>
    </div>
</template>

<style lang="css" scoped>
    .vue_component__upload--image{
        padding: 5px;
        cursor: pointer;
        min-height: 80px;
        border-radius: 5px;
    }
    .vue_component__upload--image.dragover{}

    .vue_component__upload--image form > div{
        text-align: center;
    }

    .vue_component__upload--image .upload_image_form__thumbnails{
        margin-bottom: 1em;
    }
    .vue_component__upload--image .upload_image_form__thumbnail{
        border-radius: 2.5px;
        position:relative;
        width:20%;
        padding:20% 0 0;
        overflow: hidden;
        margin:10px;
        display:inline-block;
    }

    .vue_component__upload--image .upload_image_form__thumbnail img{
        position: absolute;
        top:50%;
        left: 50%;
        min-width: 100%;
        min-height: 100%;
        max-height: 150%;
        opacity: 0;
        transform: translateX(-50%) translateY(-50%);
        transition: 1s opacity;
    }
    .vue_component__upload--image .upload_image_form__thumbnail img.show{
        opacity: 1;
    }
    .vue_component__upload--image .upload_image_form__thumbnail img:hover{
        filter: blur(2px);
    }
    .vue_component__upload--image .upload_image_form__thumbnail.bad-size img{
        filter: grayscale(100%);
    }
    .vue_component__upload--image .upload_image_form__thumbnail.uploaded img{
        opacity: 0.1;
    }
    .vue_component__upload--image .upload_image_form__thumbnail span{
        position: absolute;
        top: -5px;
        left: 0px;
        z-index: 100;
        padding: 0px 1px;
        border-radius: 2px;
        background-color: grey;
    }
</style>
