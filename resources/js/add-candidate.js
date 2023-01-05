'use strict';

$(() => {
    let candidateFiles = [];
    let ticketFile = null;

    $('#candidate-form').submit(function () {
        const $submitBtn = $(this).find('button[type="submit"]');

        $submitBtn.prop('disabled', true).attr('data-kt-indicator', 'on');

        const fD = new FormData(this);

        candidateFiles.forEach(function (item) {
            fD.append('file[]', item[0], item[0].name);
            fD.append('fileType[]', item[1]);
        });

        if (ticketFile) {
            fD.append('ticket_file', ticketFile, ticketFile.name);
        }

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
                    window.location.replace('/candidate/view?id=' + response.id);
                }
            }
        });

        return false;
    });

    $('#candidate-form-simple').submit(function () {
        const $submitBtn = $(this).find('button[type="submit"]');

        $submitBtn.prop('disabled', true).attr('data-kt-indicator', 'on');

        const fD = new FormData(this);

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
                    window.location.replace('/candidate/view?id=' + response.id);
                }
            }
        });

        return false;
    });

    $('#dateOfBirth').flatpickr({
        dateFormat: 'd.m.Y',
        locale: {
            firstDayOfWeek: 1
        },
    });

    // $('#date_arrive').flatpickr({
    //     dateFormat: 'd.m.Y H:i',
    //     enableTime: true,
    //     minDate: "today",
    //     time_24hr: true,
    //     locale: {
    //         firstDayOfWeek: 1
    //     },
    // });

    $('#citizenship_id').select2({
        placeholder: 'Поиск гражданства',
        ajax: {
            url: "/search/candidate/citizenship",
            dataType: 'json',
            // delay: 250,
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

    $('#speciality_id').select2({
        placeholder: 'Поиск специальности',
        ajax: {
            url: "/search/speciality",
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

    // $('#nacionality_id').select2({
    //     placeholder: 'Поиск национальности',
    //     ajax: {
    //         url: "/search/candidate/nacionality",
    //         dataType: 'json',
    //         data: function (params) {
    //             return {
    //                 s: '',
    //                 f_search: params.term,
    //             };
    //         },
    //         processResults: function (data) {
    //             var results = [];
    //             $.each(data, function (index, item) {
    //                 results.push({
    //                     id: item.id,
    //                     text: item.value
    //                 });
    //             });
    //             return {
    //                 results: results
    //             };
    //         }
    //     },

    // }).on('change.select2', function () {
    //     $.ajax({
    //         url: '/vacancy/check-nacionality',
    //         type: 'GET',
    //         data: 'vid=' + $('select#real_vacancy_id').val() + '&nacionality_id=' + $('#nacionality_id').val(),
    //         success: function (res) {
    //             if (res.is_disabled === true) {
    //                 toastr.error('Национальность ' + $('#select2-nacionality_id-container').text() + ' не принимается на данной вакансии');

    //                 $('#nacionality_id').html('');
    //             }
    //         }
    //     });
    // });

    $('#country_id').select2({
        placeholder: 'Поиск страны',
        ajax: {
            url: "/search/candidate/country",
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

    $('#type_doc_id').select2({
        placeholder: 'Поиск документа',
        ajax: {
            url: "/search/candidate/typedocs",
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

    $('#real_vacancy_id').select2({
        placeholder: 'Вакансия по факту',
        ajax: {
            url: "/search/candidate/vacancy",
            dataType: 'json',
            data: function (params) {
                return {
                    gender: $('select[name="gender"]').val(),
                    nacionality_id: $('select[name="nacionality_id"]').val(),
                    view: 'candidate.add',
                    f_search: params.term,
                };
            },
            processResults: function (data) {
                var results = [];
                $.each(data, function (index, item) {
                    results.push({
                        id: item.id,
                        text: item.value,
                        disabled: item.filled || item.disabled,
                    });
                });
                return {
                    results: results
                };
            }
        },
    }).on('select2:open', function (e) {
        if (!$('select[name="gender"]').val()) {
            toastr.error('Укажите пол кандидата');
        }
        // if ((group == 1 || group == 5) && !$('select[name="nacionality_id"]').val()) {
        //     toastr.error('Укажите национальность кандидата');
        // }
    });

    $('body').on('click', '#select2-real_vacancy_id-results .select2-results__option--disabled', function () {
        // toastr.error('Вакансия уже заполнена или недоступна национальность');
        toastr.error('Вакансия уже заполнена');
    });

    $('body').on('change', 'select[name="gender"]', function () {
        $.ajax({
            url: '/vacancy/check-filling',
            type: 'GET',
            data: 'vid=' + $('select#real_vacancy_id').val() + '&gender=' + $(this).val(),
            success: function (res) {
                if (res.is_filled === true) {
                    if ($('select[name="gender"]').val() == 'm') {
                        toastr.error('Эта ванасия не доступна для мужчин');
                    } else {
                        toastr.error('Эта ванасия не доступна для женщин');
                    }

                    $('select[name="gender"]').prop('selectedIndex', 0);
                }
            }
        });
    });

    $('#recruiter_id').select2({
        placeholder: 'Рекрутер',
        ajax: {
            url: "/search/candidate/recruter",
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

    $('#client_id:not([type="hidden"])').select2({
        placeholder: 'Клиент',
        ajax: {
            url: "/search/candidate/client",
            dataType: 'json',
            data: function (params) {
                return {
                    f_search: params.term,
                    vacancy_id: $('#real_vacancy_id').val(),
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
    }).on('select2:open', function (e) {
        if (!$('#real_vacancy_id').val()) {
            toastr.error('Выберите вакансию');
        }
    });

    $('#client_position_id').select2({
        placeholder: 'Должность',
        ajax: {
            url: "/search/candidate/client/position",
            dataType: 'json',
            data: function (params) {
                return {
                    f_search: params.term,
                    client_id: $('#client_id').val(),
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
    }).on('select2:open', function (e) {
        if ($('#real_vacancy_id').length && !$('#real_vacancy_id').val()) {
            toastr.error('Выберите вакансию');
        }
        if ($('#client_id').length && !$('#client_id').val()) {
            toastr.error('Выберите клиента');
        }
    });

    $('#real_status_work_id').select2({
        placeholder: 'трудоустройство',
        ajax: {
            url: "/search/candidate/realstatuswork",
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

    $('#place_arrive_id').select2({
        placeholder: 'Поиск места прибытия',
        ajax: {
            url: "/search/candidate/placearrive",
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

    // $('#transport_id').select2({
    //     placeholder: 'Тип транспорта',
    //     ajax: {
    //         url: "/search/candidate/transport",
    //         dataType: 'json',
    //         data: function (params) {
    //             return {
    //                 s: '',
    //                 f_search: params.term,
    //             };
    //         },
    //         processResults: function (data) {
    //             var results = [];
    //             $.each(data, function (index, item) {
    //                 results.push({
    //                     id: item.id,
    //                     text: item.value
    //                 });
    //             });
    //             return {
    //                 results: results
    //             };
    //         }
    //     },
    // });

    if ($('#kt_file_doc').length) {
        const drZones = [
            ['#kt_file_doc', 3],
            ['#kt_file_doc1', 103],
            ['#kt_file_doc2', 104],
            ['#kt_file_doc3', 105],
            ['#kt_file_doc4', 106],
            ['#kt_file_doc5', 107],
        ];

        drZones.forEach(function (item) {
            const myDropzoneT = new Dropzone(item[0], {
                autoProcessQueue: false,
                url: '/',
                maxFiles: 1,
                dictInvalidFileType: 'Данный тип файла не допустим',
                maxFilesize: 5,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
                addRemoveLinks: true,
                accept: function (file, done) {
                    done();
                }
            });

            myDropzoneT.on("addedfile", function (file) {
                candidateFiles.push([file, item[1]]);

                var filename = file.name.toLowerCase();
                var ext = filename.split('.').pop();

                if (ext == 'pdf') {
                    $(item[0]).find('img[data-dz-thumbnail]').attr('src', hostUrl + 'media/svg/files/pdf.svg').addClass('pdf');
                }
            });

            myDropzoneT.on("removedfile", function (file) {
                var ind = null;

                candidateFiles.forEach(function (it, i) {
                    if (it[1] == item[1]) {
                        ind = i;
                    }
                });

                candidateFiles.splice(ind, 1);
            });

            myDropzoneT.options.dropzoneForm = {
                dictFileTooBig: "Максимальный размер файла 5 мб",
                dictMaxFilesExceeded: "Не более одного файла",
                dictInvalidFileType: "Данный тип файла не допустим",
            };
        });
    }

    if ($('#kt_file_ticket').length) {
        new Dropzone("#kt_file_ticket", {
            autoProcessQueue: false,
            url: '/',
            maxFiles: 1,
            dictInvalidFileType: 'Данный тип файла не допустим',
            maxFilesize: 5,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
            addRemoveLinks: true,
            accept: function (file, done) {
                done();
            }
        }).on("addedfile", function (file) {
            ticketFile = file;
            const filename = file.name.toLowerCase();
            const ext = filename.split('.').pop();

            if (ext == 'pdf') {
                $('#kt_file_ticket').find('img[data-dz-thumbnail]').attr('src', hostUrl + 'media/svg/files/pdf.svg').addClass('pdf');
            }

        }).on("removedfile", function (file) {
            ticketFile = null;
        });
    }

});