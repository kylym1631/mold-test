'use strict';

let arrivalsData = {};

$(function () {
    $('body').on('click', '.js-add-arrival', function () {
        arrivalsData = {
            candidate_id: $(this).attr('data-candidate-id'),
        };

        $('#arrivals-add-popup .modal-title').text('Добавить приезд');
        $('#arrivals-add-popup [name="place_arrive_id"]').html('');
        $('#arrivals-add-popup [name="transport_id"]').html('');
        $('#arrivals-add-popup [name="date_arrive"]').val('');
        $('#arrivals-add-popup [name="comment"]').val('');
        $('#arrivals-add-popup [name="transportation_id"]').html('');
        $('#arrivals-add-popup .js-transportations-field').hide();

        addArrival();

    }).on('click', '.js-edit-arrival', function () {
        arrivalsData = {
            id: $(this).attr('data-id'),
            place_arrive_id: $(this).attr('data-place_arrive_id'),
            transport_id: $(this).attr('data-transport_id'),
            date_arrive: $(this).attr('data-date_arrive'),
            comment: $(this).attr('data-comment'),
            transportation_id: $(this).attr('data-transportation-id'),
        };

        $('#arrivals-add-popup .modal-title').text('Редактировать приезд');

        $('#arrivals-add-popup [name="place_arrive_id"]').html('<option value="' + arrivalsData.place_arrive_id + '" selected>' + $(this).attr('data-place_arrive_name') + '</option>');

        $('#arrivals-add-popup [name="transport_id"]').html('<option value="' + arrivalsData.transport_id + '" selected>' + $(this).attr('data-transport_name') + '</option>');

        if (arrivalsData.transportation_id) {
            $('.js-transportations-field').show();
            $('#arrivals-add-popup [name="transportation_id"]').html('<option value="' + arrivalsData.transportation_id + '" selected>' + $(this).attr('data-transportation') + '</option>');
        }

        

        $('#arrivals-add-popup [name="date_arrive"]').val(arrivalsData.date_arrive);
        $('#arrivals-add-popup [name="comment"]').val(arrivalsData.comment);

        $('#arrivals-add-popup').modal('show');

        return false;

    }).on('click', '#arrivals-add-popup .js-save-btn', function () {
        const $mod = $(this).closest('.modal');
        const $input = $mod.find('.js-input');
        let err = 0;

        $input.each(function () {
            const $inp = $(this);

            if ($inp.prop('required') && !elemIsHidden($inp[0]) && (!$inp.val() || $inp.val().length < 1)) {
                toastr.error($('label[for="' + $inp.attr('id') + '"]').text() + ' обязательное поле');
                err++;
            } else if ($inp.attr('name')) {
                arrivalsData[$inp.attr('name')] = $inp.val();
            }
        });

        if (!err) {
            sendRequest();
            $mod.modal('hide');
        }

    }).on('click', '#arrivals-add-popup .js-status-cancel-btn', function () {
        refreshTable();
    });

    $('.js-place-arrive-id').select2({
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

    $('.js-transportions-id').select2({
        placeholder: 'Поиск регулярных перевозок',
        ajax: {
            url: "/search/transportations",
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
    }).on('change', function () {
        const val = $(this).val();

        $.get('/transportation/json/' + val, function (item) {
            $('input[name="date_arrive"]')
                .val(item.arrival_date_format)
                .prop('readonly', true)
                .removeClass('js-date-arrive flatpickr-input')
                .css('pointer-events', 'none');

            $('select[name="place_arrive_id"]')
                .append(new Option(item.arrival_place.name, item.arrival_place.id, true, true))
                .trigger('change')
                .next('span')
                .css('pointer-events', 'none');
        });
    });

    $('.js-transport-id').select2({
        placeholder: 'Тип транспорта',
        ajax: {
            url: "/search/candidate/transport",
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
    }).on('change', function () {
        const val = $(this).val();
        console.log(val);
        if (val == 999999) {
            $('.js-transportations-field').show().addClass('active');

            $('input[name="date_arrive"]')
                .prop('readonly', true)
                .removeClass('js-date-arrive flatpickr-input')
                .css('pointer-events', 'none');

            $('select[name="place_arrive_id"]')
                .next('span')
                .css('pointer-events', 'none');

        } else if ($('.js-transportations-field').hasClass('active')) {

            $('.js-transportations-field').hide().removeClass('active');
            $('.js-transportions-id').html('');

            $('input[name="date_arrive"]')
                .val('')
                .addClass('js-date-arrive flatpickr-input')
                .css('pointer-events', 'all');

            $('select[name="place_arrive_id"]').html('').next('span').css('pointer-events', 'all');
        }
    });

    $('.js-date-arrive').flatpickr({
        dateFormat: 'd.m.Y H:i',
        enableTime: true,
        minDate: "today",
        time_24hr: true,
        locale: {
            firstDayOfWeek: 1
        },
    });
});

window.addArrival = function addArrival(candidateId) {
    arrivalsData._token = $('input[name=_token]').val();

    if (candidateId) {
        arrivalsData.candidate_id = candidateId;
    }

    $.ajax({
        url: '/candidates/arrivals/count',
        type: 'GET',
        dataType: 'json',
        data: arrivalsData,
        success: function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                if (res.count >= 4) {
                    Swal.fire({
                        html: 'Кандидат сделал уже ' + (res.count - 1) + ' перепланировки!<br> Отправить кандидата в архив?',
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
                            arrivalsData.action = 'setCandidateStatus';
                            arrivalsData.candidateId = arrivalsData.candidate_id;
                            arrivalsData.status = 5;
                            arrivalsData.comment = 'Кандидат сделал уже ' + (res.count - 1) + ' перепланировки';

                            $.ajax({
                                url: '/status-manage',
                                type: 'POST',
                                dataType: 'json',
                                data: arrivalsData,
                                success: function (res) {
                                    if (res.error) {
                                        toastr.error(res.error);
                                    } else {
                                        toastr.success('Успешно');
                                    }

                                    refreshTable();
                                }
                            });

                        } else {
                            $('#arrivals-add-popup').modal('show');
                        }
                    });

                } else {
                    $('#arrivals-add-popup').modal('show');
                }
            }
        }
    });
}

function sendRequest() {
    arrivalsData._token = $('input[name=_token]').val();

    $.ajax({
        url: '/candidates/arrivals/add',
        type: 'POST',
        dataType: 'json',
        data: arrivalsData,
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

function refreshTable() {
    if (window.arrivalsTable) {
        arrivalsTable.draw();
    }

    if (window.tasksOTable) {
        tasksOTable.draw();
    }

    if (window.dTables) {
        dTables.forEach(tbl => {
            tbl.draw();
        });
    }
}