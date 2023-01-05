<template>
    <div
        class="dropzone dz-clickable"
        id="pg-dropzone"
    >
        <!--begin::Message-->
        <div class="dz-message needsclick">
            <!--begin::Icon-->
            <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
            <!--end::Icon-->

            <!--begin::Info-->
            <div class="ms-4">
                <h3 class="fs-5 fw-bolder text-gray-900 mb-1">
                    {{title || 'Загрузить фотографии'}}
                </h3>
                <span class="fs-7 fw-bold text-gray-400">Перетащите
                    фотографии сюда. Максимально {{filescount || 1}} файлов.<br>
                    Максимальный размер 5Мб. Формат: 
                    {{types || '.jpeg, .jpg, .png'}}
                </span>
            </div>
            <!--end::Info-->
        </div>
    </div>
</template>

<script>
export default {
    name: 'PgDropzone',
    props: ['filescount', 'existFiles', 'title', 'types'],
    mounted() {
        const galleryFiles = [];
        const toRemoveGalleryFiles = [];

        const dropzone = new Dropzone('#pg-dropzone', {
            autoProcessQueue: false,
            url: '/',
            maxFiles: this.filescount || 1,
            maxFilesize: 5,
            acceptedFiles: this.types || '.jpeg, .jpg, .png',
            addRemoveLinks: true,
            accept: function (file, done) {
                done();
            }
        }).on("addedfile", (file) => {
            galleryFiles.push(file);

            const filename = file.name.toLowerCase();
            const ext = filename.split('.').pop();

            if (ext == 'pdf') {
                $('#pg-dropzone').find('img[data-dz-thumbnail]').attr('src', '/assets/media/svg/files/pdf.svg').addClass('pdf');
            }

            this.$emit('uploaded', { files: galleryFiles, toRemove: toRemoveGalleryFiles });

        }).on("removedfile", (file) => {
            let ind = null;

            galleryFiles.forEach((fItem, i) => {
                if (file.name == fItem.name) {
                    ind = i;
                }
            });

            if (ind !== null) {
                galleryFiles.splice(ind, 1);
            }

            toRemoveGalleryFiles.push(file.name);

            this.$emit('uploaded', { files: galleryFiles, toRemove: toRemoveGalleryFiles });
        });

        if (this.existFiles && this.existFiles.length) {
            this.existFiles.forEach(file => {
                dropzone.emit("addedfile", file);
                dropzone.emit("thumbnail", file, file.path);
                dropzone.emit("complete", file);
            });
        }
    }
}
</script>

<style lang="scss">
#pg-dropzone {
    justify-content: center;
    .dz-progress {
        display: none !important;
    }
    .dz-image {
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
    }
}
</style>