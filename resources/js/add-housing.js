'use strict';

let roomData = {};
let housingGalleryFiles = [];
let toRemoveHousingGalleryFiles = [];

$(() => {
    $('#housing-form').submit(function () {
        if (housingGalleryFiles.length > 25) {
            toastr.error('Максимально 25 файлов');
        }

        const $submitBtn = $(this).find('button[type="submit"]');

        $submitBtn.prop('disabled', true).attr('data-kt-indicator', 'on');

        const fD = new FormData(this);

        housingGalleryFiles.forEach(file => {
            if (file.type) {
                fD.append('file[]', file, file.name);
            }
        });

        toRemoveHousingGalleryFiles.forEach(fileName => {
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
                        window.location.replace('/housing/view' + window.location.search);
                    } else {
                        window.location.replace('/housing');
                    }
                }
            }
        });

        return false;
    });

    $('#clients_add_select').select2({
        placeholder: 'Поиск клиента',

        ajax: {
            url: "/search/client",
            dataType: 'json',
            data: function (params) {
                return {
                    s: '',
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

    $('body').on('click', '.js-add-room', function () {
        roomData = {
            housing_id: $(this).attr('data-housing-id'),
        };

        $('.js-room-popup .modal-title').text('Добавить комнату');

        $('.js-room-popup [name="number"]').val('');
        $('.js-room-popup [name="places_count"]').val('');
        $('.js-room-popup [name="filled_count"]').val(0).prop('readonly', true);

        $('.js-room-popup').modal('show');

    }).on('click', '.js-edit-room', function () {
        roomData = {
            id: $(this).attr('data-id'),
            housing_id: $(this).attr('data-housing-id'),
            number: $(this).attr('data-number'),
            places_count: $(this).attr('data-places-count'),
            filled_count: $(this).attr('data-filled-count'),
        };

        $('.js-room-popup .modal-title').text('Редактировать комнату');

        $('.js-room-popup [name="number"]').val(roomData.number);
        $('.js-room-popup [name="places_count"]').val(roomData.places_count);
        $('.js-room-popup [name="filled_count"]').val(roomData.filled_count).prop('readonly', true);

        $('.js-room-popup').modal('show');

        return false;

    }).on('click', '.js-room-popup .js-save-btn', function () {
        const $mod = $(this).closest('.modal');
        const $input = $mod.find('.js-input');
        let err = 0;

        $input.each(function () {
            const $inp = $(this);

            if ($inp.prop('required') && (!$inp.val() || $inp.val().length < 1)) {
                toastr.error($('label[for="' + $inp.attr('id') + '"]').text() + ' обязательное поле');
                err++;
            } else if ($inp.attr('name')) {
                roomData[$inp.attr('name')] = $inp.val();
            }
        });

        if (!err) {
            sendRequest();
        }

    }).on('click', '#add-housing-contacts', function () {
        const tplHtml = $('#contacts-template').html();
        const $container = $('#husing-contacts-container');

        $container.append(tplHtml);

    }).on('click', '.js-delete-housing-contact', function () {
        const $contact = $(this).closest('.housing-contact');

        $contact.remove();

    }).on('click', '.js-delete-room-btn', function () {
        const id = $(this).attr('data-id');

        Swal.fire({
            html: 'Удалить комнату?',
            icon: "question",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Да!",
            cancelButtonText: 'Нет, отмена!',
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: 'btn btn-secondary'
            }
        }).then((r) => {
            if (r.isConfirmed) {
                $.ajax({
                    url: '/housing/room/remove',
                    type: 'POST',
                    dataType: 'json',
                    data: { id, _token: $('input[name=_token]').val() },
                    success: function (res) {
                        if (res.error) {
                            toastr.error(res.error);
                        } else {
                            toastr.success('Успешно');
                        }

                        refreshTable();
                    }
                });
            }
        });
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
            housingGalleryFiles.push(file);

            const filename = file.name.toLowerCase();
            const ext = filename.split('.').pop();

            if (ext == 'pdf') {
                $('#gallery').find('img[data-dz-thumbnail]').attr('src', hostUrl + 'media/svg/files/pdf.svg').addClass('pdf');
            }

        }).on("removedfile", function (file) {
            let ind = null;

            housingGalleryFiles.forEach((fItem, i) => {
                if (file.name == fItem.name) {
                    ind = i;
                }
            });

            if (ind !== null) {
                housingGalleryFiles.splice(ind, 1);
            }

            toRemoveHousingGalleryFiles.push(file.name);
        });

        const existFiles = existingHousingFiles || [];

        if (existFiles.length) {
            existFiles.forEach(file => {
                dropzone.emit("addedfile", file);
                dropzone.emit("thumbnail", file, file.path);
                dropzone.emit("complete", file);
            });
        }
    }
});

function sendRequest() {
    roomData._token = $('input[name=_token]').val();

    $.ajax({
        url: '/housing/room/add',
        type: 'POST',
        dataType: 'json',
        data: roomData,
        success: function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
                $('.js-room-popup').modal('hide');
            }

            refreshTable();
        }
    });
}

function refreshTable() {
    if (window.dTables) {
        dTables.forEach(tbl => {
            tbl.draw();
        });
    }
}