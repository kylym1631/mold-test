'use strict';

$(() => {
    $('#industry_id').select2({
        placeholder: 'Поиск отрасли',
        ajax: {
            url: "/search/client/industry",
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
    $('#work_place_id').select2({
        placeholder: 'Место работы',
        ajax: {
            url: "/search/client/workplace",
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
    $('#nationality_id').select2({
        placeholder: 'Национальность',
        ajax: {
            url: "/search/client/nationality",
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
    $('#housing_id').select2({
        placeholder: 'Жилье',
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
    });
    $('#work_time_format').select2({
        placeholder: 'Формат',
        minimumResultsForSearch: Infinity,
    });
    $('#coordinator_id').select2({
        placeholder: 'Координатор',
        ajax: {
            url: "/search/user/all",
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

    $('#add_contact').click(function () {
        let html = $('#template_add').html();
        $('.contacts').append(html);
    });

    $(document).on('click', '.delete_contact', function () {
        $(this).parent().parent().parent().remove();
    });

    $('#save_vacancies').click(function (e) {
        e.preventDefault();
        let self = $(this);
        self.prop('disabled', true);


        var data = {
            name: $('#name').val(),
            coordinator_id: $('#coordinator_id').val(),
            address: $('#address').val(),
            industry_id: $('#industry_id').val().join(','),
            work_place_id: $('#work_place_id').val().join(','),
            nationality_id: $('#nationality_id').val().join(','),
            work_time_format: $('#work_time_format').val(),
            min_work_time: $('#min_work_time').val(),
            active: $('#active').val(),
            _token: $('input[name=_token]').val(),
        };

        if ($('#housing_id').val()) {
            data.housing_id = $('#housing_id').val().join(',');
        }

        let id = $('#id').val();
        if (id !== '') {
            data.id = id;
        }

        let contacts = [];
        $('.contacts .contact').each(function () {
            let firstName = $(this).find('.cfirstName').val();
            let lastName = $(this).find('.clastName').val();
            let position = $(this).find('.cposition').val();
            let phone = $(this).find('.cphone').val();
            let email = $(this).find('.cemail').val();

            if (firstName == '') {
                toastr.error('Имя контакта обязательное поле!');
                self.prop('disabled', false);
                return '';
            }

            if (phone == '') {
                toastr.error('Телефон контакта обязательное поле!');
                self.prop('disabled', false);
                return '';
            }

            contacts.push([firstName, lastName, position, phone, email]);
        });

        if (contacts.length === 0) {
            toastr.error('Добавте хоть один контакт!');
            self.prop('disabled', false);
            return '';
        }

        data.contacts = JSON.stringify(contacts);

        const positions = [];

        $('#client-positions-container .client-position').each(function () {
            let description = $(this).find('[name="description[]"]').val();
            let title = $(this).find('[name="title[]"]').val();
            let id = $(this).find('[name="title[]"]').attr('data-id');

            const rates = [];

            $(this).find('.position-rate').each(function () {
                const start_at = $(this).find('[name="start_at[]"]').val();
                const type = $(this).find('[name="type[]"]').val();
                const amount = $(this).find('[name="amount[]"]').val();
                const id = $(this).find('[name="amount[]"]').attr('data-id');

                rates.push({ start_at, type, amount, id });
            });

            positions.push({ rates, description, title, id });
        });

        if (positions.length === 0) {
            toastr.error('Добавте хоть одну должность!');
            self.prop('disabled', false);
            return;
        } else {
            let type_rate = 0;
            let type_rate_after = 0;
            let type_personal_rate = 0;

            for (const pos of positions) {
                if (pos.title == '') {
                    toastr.error('Заголовок должности - обязательное поле!');
                    self.prop('disabled', false);
                    return;
                }

                if (pos.rates.length < 3) {
                    toastr.error('Добавьте три ставки для каждой должности. Ставку, ставку после 3 месяца, ставку от клиента brutto');
                    self.prop('disabled', false);
                    return;
                }

                // for (const rate of pos.rates) {
                //     if (rate.type == '') {
                //         toastr.error('Тип ставки - обязательное поле!');
                //         self.prop('disabled', false);
                //         return;
                //     }

                //     if (rate.start_at == '') {
                //         toastr.error('Начало действия - обязательное поле!');
                //         self.prop('disabled', false);
                //         return;
                //     }

                //     if (rate.amount == '') {
                //         toastr.error('Ставка - обязательное поле!');
                //         self.prop('disabled', false);
                //         return;
                //     }

                //     if (rate.type == 'rate') {
                //         type_rate++;
                //     }

                //     if (rate.type == 'rate_after') {
                //         type_rate_after++;
                //     }

                //     if (rate.type == 'personal_rate') {
                //         type_personal_rate++;
                //     }
                // }

                // if (!type_rate) {
                //     toastr.error('Добавьте cтавку');
                //     self.prop('disabled', false);
                //     return;
                // }

                // if (!type_rate_after) {
                //     toastr.error('Добавьте ставку после 3 месяца');
                //     self.prop('disabled', false);
                //     return;
                // }

                // if (!type_personal_rate) {
                //     toastr.error('Добавьте ставку от клиента brutto');
                //     self.prop('disabled', false);
                //     return;
                // }
            }
        }

        data.positions = JSON.stringify(positions);

        $.ajax({
            url: "/client/add",
            method: 'post',
            data: data,
            success: function (response, status, xhr, $form) {
                if (response.error) {
                    toastr.error(response.error);
                } else {
                    location.href = '/clients';
                }
                self.prop('disabled', false);
            }
        });

    });

    if (clientOpt.Coordinator) {
        $('#coordinator_id').append(new Option(clientOpt.Coordinator[1], clientOpt.Coordinator[0], true, true)).trigger('change');
    }

    if (clientOpt.h_v_industry) {
        clientOpt.h_v_industry.forEach(industry => {
            $('#industry_id').append(new Option(industry[1], industry[0], true, true)).trigger('change');
        });
    }

    if (clientOpt.h_v_city) {
        clientOpt.h_v_city.forEach(city => {
            $('#work_place_id').append(new Option(city[1], city[0], true, true)).trigger('change');
        });
    }

    if (clientOpt.h_v_nationality) {
        clientOpt.h_v_nationality.forEach(nationality => {
            $('#nationality_id').append(new Option(nationality[1], nationality[0], true, true)).trigger('change');
        });
    }

    if (clientOpt.h_v_housing) {
        clientOpt.h_v_housing.forEach(housing => {
            $('#housing_id').append(new Option(housing[1], housing[0], true, true)).trigger('change');
        });
    }

    if (clientOpt.work_time_format) {
        $('#work_time_format').val(clientOpt.work_time_format).trigger('change');
    }

    $('body').on('click', '#add-client-position', function () {
        const tplHtml = $('#position-template').html();
        const rateTplHtml = $('#rate-template').html();
        const $container = $('#client-positions-container');

        $container.append(tplHtml);

        const $ratesContainer = $container.find('.client-position').last().find('.position-rates-container');

        $ratesContainer.append(rateTplHtml.replace('value="rate"', 'value="rate" selected'));
        $ratesContainer.append(rateTplHtml.replace('value="rate_after"', 'value="rate_after" selected'));
        $ratesContainer.append(rateTplHtml.replace('value="personal_rate"', 'value="personal_rate" selected'));

        $('.js-rate-date:not([readonly])').flatpickr({
            dateFormat: 'd.m.Y',
            locale: {
                firstDayOfWeek: 1
            },
        });

    }).on('click', '.js-delete-client-position', function () {
        const $contact = $(this).closest('.client-position');

        $contact.remove();

    }).on('click', '.js-add-rate', function () {
        $(this).hide();
        const rateTplHtml = $('#rate-template').html();
        const $container = $(this).closest('.client-position').find('.position-rates-container');

        $container.append(rateTplHtml.replace('value="rate"', 'value="rate" selected'));
        $container.append(rateTplHtml.replace('value="rate_after"', 'value="rate_after" selected'));
        $container.append(rateTplHtml.replace('value="personal_rate"', 'value="personal_rate" selected'));

        $container.next('.js-no-rate-msg').hide();

        $container.find('.js-rate-date:not([readonly])').flatpickr({
            dateFormat: 'd.m.Y',
            locale: {
                firstDayOfWeek: 1
            },
        });
    });

    $('.js-rate-date:not([readonly])').flatpickr({
        dateFormat: 'd.m.Y',
        locale: {
            firstDayOfWeek: 1
        },
    });
});