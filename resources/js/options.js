'use strict';

$(() => {
    $('body').on('change', '.js-options-input', function () {
        const $inp = $(this);

        const data = {
            key: $inp.attr('name'),
            value: $inp.val(),
            _token: $('input[name=_token]').val(),
        };

        $.ajax({
            url: '/options',
            type: 'POST',
            dataType: 'json',
            data,
            success: function (res) {
                if (res.error) {
                    toastr.error(res.error);
                } else {
                    toastr.success('Успешно');
                }
            }
        });
    });
});