<template>
    <div>
        <div
            id="weighingPhotoFullModal"
            class="modal fade bs-example-modal-lg"
            tabindex="-1"
            role="dialog"
            aria-labelledby="weighingEditLabel"
        >
            <div
                class="modal-dialog modal-lg"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-body">
                        <img
                            :src="imagePath"
                            alt="Изображение отсутствует"
                            class="center-full-image"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'WeighingPhotoFullModal',
        props: {
            imagePath: {
                type: String,
                required: true,
            },
        },
    };
</script>
<style>
    .center-full-image {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 50%;
    }
</style>
