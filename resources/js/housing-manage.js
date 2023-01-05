'use strict';

let sendData = {};

$(function () {
    $('body').on('click', '.js-edit-housing-period', function () {
        sendData = {
            id: $(this).attr('data-id'),
            start_at: $(this).attr('data-start'),
            end_at: $(this).attr('data-end'),
            is_current: $(this).attr('data-current'),
            candidate_id: $(this).attr('data-candidate-id'),
        };

        $('#housing-edit-popup [name="start_at"]').val(sendData.start_at);
        $('#housing-edit-popup [name="end_at"]').val(sendData.end_at);

        if (sendData.is_current == 'true') {
            $('#housing-edit-popup .at-end-wrap').hide();
        } else {
            $('#housing-edit-popup .at-end-wrap').show();
        }

        $('#housing-edit-popup').modal('show');

        return false;

    }).on('click', '#housing-edit-popup .js-save-btn', function () {
        const $mod = $(this).closest('.modal');
        const $input = $mod.find('.js-input');

        $input.each(function () {
            const $inp = $(this);
            sendData[$inp.attr('name')] = $inp.val();
        });

        sendRequest();

    }).on('click', '#housing-edit-popup .js-status-cancel-btn', function () {
        refreshTable();
    });

    $('.js-date-housing').flatpickr({
        dateFormat: 'd.m.Y',
        locale: {
            firstDayOfWeek: 1
        },
    });
});

function sendRequest() {
    sendData._token = $('input[name=_token]').val();

    $.ajax({
        url: '/candidate/housing/update',
        type: 'POST',
        dataType: 'json',
        data: sendData,
        success: function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
                $('#housing-edit-popup').modal('hide');
            }

            refreshTable();
        }
    });
}

function refreshTable() {
    if (window.dTables) {
        dTables.forEach(tbl => {
            if (tbl.context[0].sInstance == 'CandidateHousingTable') {
                tbl.draw();
            }
        });
    }
}