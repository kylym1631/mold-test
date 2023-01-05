'use strict';

let statusData = {};

$(function () {
    $('body').on('change', '.js-select-status', function () {
        statusData = {
            _token: $('input[name=_token]').val(),
            taskId: $(this).attr('data-task-id'),
            candidateId: $(this).attr('data-candidate-id'),
            leadId: $(this).attr('data-lead-id'),
            action: $(this).attr('data-action'),
            status: $(this).val(),
        };

        if (
            statusData.action == 'setLeadStatus'
            && statusData.status == 4
        ) {
            const $mod = $('#status-manage-popup-lead-' + statusData.status);

            if ($mod.length) {
                $mod.modal('show');
                $mod.find('.js-status-input').val('');
                $mod.find('select.js-status-input').html('');
                $mod.find('.js-status-details-other').hide();
            }

            return;

        } else if (
            statusData.action == 'setCandidateStatus'
            && statusData.status == '3'
            && group == 5
        ) {
            Swal.fire({
                html: 'Отправить в архив?',
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
                    sendRequest();
                    return;
                }

                refreshTable();
            });

            return;

        } else if (
            statusData.action == 'setCandidateStatus'
            && ['3', '5', '7', '9', '10', '11', '13', '14', '15', '16', '21', '22'].includes(statusData.status)
        ) {

            if (['9', '13', '14', '15', '16', '21', '22'].includes(statusData.status)) {
                statusData.specialStatus = true;
            }

            const $mod = $('#status-manage-popup-' + statusData.status);

            if ($mod.length) {
                $mod.modal('show');
                $mod.find('.js-status-input').val('');
                $mod.find('select.js-status-input').html('');
            }

            return;

        } else if (statusData.action == 'setTaskStatus' && ['4'].includes(statusData.status)) {

            const $mod = $('#status-manage-popup-3');

            if ($mod.length) {
                $mod.modal('show');
                $mod.find('.js-status-input').val('');
            }

            return;
        }


        sendRequest();

        // Swal.fire({
        //     html: 'Сменить статус на <b>' + ($(this).find('option[value="' + statusData.status + '"]').text()) + '</b> ?',
        //     icon: "question",
        //     buttonsStyling: false,
        //     showCancelButton: true,
        //     confirmButtonText: "Да!",
        //     cancelButtonText: 'Нет, отмена!',
        //     customClass: {
        //         confirmButton: "btn btn-primary",
        //         cancelButton: 'btn btn-secondary'
        //     }
        // }).then((r) => {
        //     if (r.isConfirmed) {
        //         sendRequest();
        //         return;
        //     }

        //     refreshTable();
        // });

    }).on('change', '.js-status-details-select', function () {
        if ($(this).val() == 'other') {
            $(this).closest('.js-status-details-wrap')
                .find('.js-status-details-other').show().removeClass('js-hidden');
        } else {
            $(this).closest('.js-status-details-wrap')
                .find('.js-status-details-other').hide().addClass('js-hidden');

            $(this).closest('.js-status-details-wrap')
                .find('.js-status-details-other .js-status-input').val('');
        }

    }).on('click', '.js-call-fail-btn', function () {
        statusData = {
            _token: $('input[name=_token]').val(),
            taskId: $(this).attr('data-task-id'),
            candidateId: $(this).attr('data-candidate-id'),
            leadId: $(this).attr('data-lead-id'),
            action: $(this).attr('data-action'),
            status: $(this).attr('data-status'),
            specialStatus: true,
        };

        const $mod = $('#status-manage-popup-15');

        if ($mod.length) {
            $mod.modal('show');
        }

        return false;

    }).on('click', '.js-not-liquidity-btn', function () {
        statusData = {
            _token: $('input[name=_token]').val(),
            taskId: $(this).attr('data-task-id'),
            candidateId: $(this).attr('data-candidate-id'),
            leadId: $(this).attr('data-lead-id'),
            action: $(this).attr('data-action'),
            status: $(this).attr('data-status'),
        };

        const $mod = $('#status-manage-popup-lead-' + statusData.status);

        if ($mod.length) {
            $mod.modal('show');
            $mod.find('.js-status-input').val('');
            $mod.find('.js-status-details-other').hide();
        }

        return false;

    }).on('click', '.js-status-save-btn', function () {
        const $mod = $(this).closest('.modal');
        const $input = $mod.find('.js-status-input');
        let err = 0;

        $input.each(function () {
            const $inp = $(this);

            if ($('.js-own-housing-checkbox').prop('checked')) {
                statusData['own_housing'] = 1;

                if (
                    $inp.attr('name') == 'housing_id'
                    || $inp.attr('name') == 'housing_room_id'
                    || $inp.attr('name') == 'residence_started_at'
                ) {
                    return;
                }
            }

            if (
                $inp.prop('required')
                && (!$inp.val() || $inp.val().length < 1)
                && !$inp.closest('.js-hidden').length
            ) {
                toastr.error($('label[for="' + $inp.attr('id') + '"]').text() + ' обязательное поле');
                err++;
            } else if (!$inp.closest('.js-hidden').length) {
                if ($inp.attr('name') == 'details' && $inp.val() != 'other') {
                    statusData.comment = $inp.val();
                } else {
                    statusData[$inp.attr('name')] = $inp.val();
                }
            }
        });

        if (!err) {
            sendRequest();
            $mod.modal('hide');
        }

    }).on('click', '.js-status-cancel-btn, .modal .btn-icon[aria-label="Close"]', function () {
        refreshTable();
    }).on('click', '.modal', function (e) {
        if (!$(e.target).closest('.modal-content').length) {
            refreshTable();
        }

    }).on('click', '.js-create-candidate-from-lead', function () {
        statusData = {
            _token: $('input[name=_token]').val(),
            taskId: $(this).attr('data-task-id'),
            leadId: $(this).attr('data-lead-id'),
            action: 'createCandidateFromLead',
            afterAction: 'goToCandidateEditPage',
        };

        Swal.fire({
            html: 'Создать карточку кандидата?',
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
                sendRequest();
                return;
            }

            refreshTable();
        });
    });

    $('#status-manage-input-9, .js-date-input').flatpickr({
        dateFormat: 'd.m.Y',
        locale: {
            firstDayOfWeek: 1
        },
    });

    $('.js-status-input-callback').flatpickr({
        dateFormat: 'd.m.Y H:i',
        enableTime: true,
        minDate: 'today',
        time_24hr: true,
        locale: {
            firstDayOfWeek: 1
        },
        onChange: function (selectedDates) {
            var d = new Date(selectedDates);
            const hour = d.getHours();
            const day = d.getDay();
            const $saveBtn = $(this.input).closest('.modal').find('.js-status-save-btn');
            let err = 0;

            if (hour < 7 || hour > 19) {
                toastr.error('только в рабочее время с 7 до 19');
                err++;
            }

            if (day === 0) {
                toastr.error('только Пн-Сб');
                err++;
            }

            if (err) {
                $saveBtn.prop('disabled', true);
            } else {
                $saveBtn.prop('disabled', false);
            }
        },
        onDayCreate: function (dObj, dStr, fp, dayElem) {
            const d = new Date(dayElem.dateObj);
            const day = d.getDay();
            if (day === 0) {
                dayElem.classList.add('flatpickr-disabled');
            }
        },
    });

    $('#status-manage-input-16').flatpickr({
        dateFormat: 'd.m.Y H:i',
        enableTime: true,
        minDate: 'today',
        time_24hr: true,
        locale: {
            firstDayOfWeek: 1
        },
    });

    $('.js-housing-id').select2({
        placeholder: 'Поиск жилья',
        ajax: {
            url: "/search/housing",
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
    }).on('select2:open', function (e) {
        $('select.js-housing-room-id').html('');
    });

    $('.js-doc-type-id').select2({
        placeholder: 'Тип документа',
        minimumResultsForSearch: -1,
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
    }).on('select2:open', function (e) {

    });

    $('.js-housing-room-id').select2({
        placeholder: 'Поиск комнаты',
        ajax: {
            url: "/search/housing_room",
            dataType: 'json',
            data: function (params) {
                return {
                    s: $('.js-housing-id').val(),
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
    }).on('select2:open', function (e) {
        if (!$('.js-housing-id').val()) {
            toastr.error('Выберите жилье');
        }
    });

    $('.js-client-position-id-select').select2({
        placeholder: 'Должность',
        ajax: {
            url: "/search/candidate/client/position",
            dataType: 'json',
            data: function (params) {
                return {
                    candidate_id: statusData.candidateId,
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

    $('body').on('change', '.js-own-housing-checkbox', function () {
        if ($(this).prop('checked')) {
            $('.js-housing-container').hide();
        } else {
            $('.js-housing-container').show();
        }
    });

    if ($('.js-status-dropzone').length) {
        new Dropzone(".js-status-dropzone", {
            autoProcessQueue: false,
            url: '/',
            maxFiles: 1,
            dictInvalidFileType: 'Данный тип файла не допустим',
            maxFilesize: 1,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
            addRemoveLinks: true,
            accept: function (file, done) {
                done();
            }
        }).on("addedfile", function (file) {
            statusData.file = file;

            const filename = file.name.toLowerCase();
            const ext = filename.split('.').pop();

            if (ext == 'pdf') {
                $('.js-status-dropzone').find('img[data-dz-thumbnail]').attr('src', hostUrl + 'media/svg/files/pdf.svg').addClass('pdf');
            }

        }).on("removedfile", function (file) {
            statusData.file = null;
        });
    }
});

function sendRequest() {
    const fD = new FormData();

    for (const key in statusData) {
        if (Object.hasOwnProperty.call(statusData, key)) {
            const val = statusData[key];

            if (val) {
                if (key == 'file') {
                    fD.append('file[]', val, val.name);
                } else {
                    fD.append(key, val);
                }
            }
        }
    }

    $.ajax({
        url: '/status-manage',
        type: 'POST',
        data: fD,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.error) {
                if (res.errCode == 'ADD_ARRIVAL') {
                    if (window.addArrival) {
                        addArrival(res.candidateId);
                    }
                }

                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }

            refreshTable();

            if (res.goTo) {
                window.location.href = res.goTo;
            }
        }
    });
}

function refreshTable() {
    if (window.tasksOTable) {
        tasksOTable.draw();
    }

    if (window.candidatesOTable) {
        candidatesOTable.draw();
    }

    if (window.arrivalsTable) {
        arrivalsTable.draw();
    }

    if (window.dTables) {
        dTables.forEach(tbl => {
            tbl.draw();
        });
    }
}