'use strict';

$(() => {
    $('#item-form').submit(function () {
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
                    window.location.replace('/transportations');
                }
            }
        });

        return false;
    });

    $('#driver_id').select2({
        placeholder: 'Водитель',
        ajax: {
            url: "/search/user/102",
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

    $('#arrival_place_id').select2({
        placeholder: 'Место приезда',
        ajax: {
            url: "/search/candidate/placearrive",
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

    $('.js-date').flatpickr({
        dateFormat: 'd.m.Y H:i',
        enableTime: true,
        time_24hr: true,
        locale: {
            firstDayOfWeek: 1
        },
    });
});
