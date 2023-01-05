'use strict';

let sendData = {};
let galleryFiles = [];
let documentFiles = [];
let toRemoveGalleryFiles = [];

$(() => {
    $('#item-form').submit(function () {
        if (galleryFiles.length > 25) {
            toastr.error('Максимально 25 файлов');
        }

        if (documentFiles.length > 1) {
            toastr.error('Максимально 1 файл');
        }

        const $submitBtn = $(this).find('button[type="submit"]');

        $submitBtn.prop('disabled', true).attr('data-kt-indicator', 'on');

        const fD = new FormData(this);

        galleryFiles.forEach(file => {
            if (file.type) {
                fD.append('file[]', file, file.name);
            }
        });

        documentFiles.forEach(file => {
            if (file.type) {
                fD.append('doc_file[]', file, file.name);
            }
        });

        toRemoveGalleryFiles.forEach(fileName => {
            fD.append('to_delete_files[]', fileName);
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: fD,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.error) {
                    toastr.error(response.error);
                    setTimeout(function () {
                        $submitBtn.prop('disabled', false).attr('data-kt-indicator', 'off');
                    }, 1000);
                } else {
                    if (window.location.search && window.location.search.includes('id')) {
                        window.location.replace('/cars/view' + window.location.search);
                    } else {
                        window.location.replace('/cars');
                    }
                }
            }
        });

        return false;
    });

    if ($('#gallery').length) {
        const dropzone = new Dropzone("#gallery", {
            autoProcessQueue: false,
            url: '/',
            maxFiles: 25,
            maxFilesize: 5,
            acceptedFiles: ".jpeg,.jpg,.png",
            addRemoveLinks: true,
            accept: function (file, done) {
                done();
            }
        }).on("addedfile", function (file) {
            galleryFiles.push(file);

            const filename = file.name.toLowerCase();
            const ext = filename.split('.').pop();

            if (ext == 'pdf') {
                $('#gallery').find('img[data-dz-thumbnail]').attr('src', hostUrl + 'media/svg/files/pdf.svg').addClass('pdf');
            }

        }).on("removedfile", function (file) {
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
        });

        const existFiles = existingGalleryFiles || [];

        if (existFiles.length) {
            existFiles.forEach(file => {
                dropzone.emit("addedfile", file);
                dropzone.emit("thumbnail", file, file.path);
                dropzone.emit("complete", file);
            });
        }
    }

    if ($('#document').length) {
        const dropzone = new Dropzone("#document", {
            autoProcessQueue: false,
            url: '/',
            maxFiles: 1,
            maxFilesize: 5,
            acceptedFiles: ".jpeg,.jpg,.png,.pdf",
            addRemoveLinks: true,
            accept: function (file, done) {
                done();
            }
        }).on("addedfile", function (file) {
            documentFiles.push(file);

            const filename = file.name.toLowerCase();
            const ext = filename.split('.').pop();

            if (ext == 'pdf') {
                $('#document').find('img[data-dz-thumbnail]').attr('src', hostUrl + 'media/svg/files/pdf.svg').addClass('pdf');
            }

        }).on("removedfile", function (file) {
            let ind = null;

            documentFiles.forEach((fItem, i) => {
                if (file.name == fItem.name) {
                    ind = i;
                }
            });

            if (ind !== null) {
                documentFiles.splice(ind, 1);
            }

            toRemoveGalleryFiles.push(file.name);
        });

        const existFiles = existingDocumentFiles || [];

        if (existFiles.length) {
            existFiles.forEach(file => {
                dropzone.emit("addedfile", file);
                dropzone.emit("thumbnail", file, file.ext == 'pdf' ? hostUrl + 'media/svg/files/pdf.svg' : file.path);
                dropzone.emit("complete", file);
            });
        }
    }

    $('body').on('change', '#is-rent', function () {
        if ($(this).prop('checked')) {
            $('#rent-cost-block, #landlord-block').show();
        } else {
            $('#rent-cost-block, #landlord-block').hide();
        }
    });

    if (is_rent) {
        $('#is-rent').prop('checked', true);
        $('#rent-cost-block, #landlord-block').show();
    }

    $('#coordinator_id').select2({
        placeholder: 'Сотрудник',
        ajax: {
            url: "/search/user/all",
            dataType: 'json',
            data: function (params) {
                return {
                    f_search: params.term,
                };
            },
            processResults: function (data) {
                var results = [];
                $.each(data, function (index, item) {
                    results.push({
                        id: item.id,
                        text: item.value
                    });
                });
                return {
                    results: results
                };
            }
        },
    });
});