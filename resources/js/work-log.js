'use strict';

let logData = {};
let sending = false;

$(() => {

    window.saveHours = function saveHours() {
        logData = {
            _token: $('input[name=_token]').val(),
        };

        logData.log_id = $(this).attr('data-log-id');
        logData.log_day_id = $(this).attr('data-id');
        logData.candidate_id = $(this).attr('data-candidate-id');
        logData.log_day_date = $(this).attr('data-date');
        logData.period = $(this).attr('data-date');
        logData.log_day_rate = $(this).attr('data-rate');
        logData.housing = $(this).attr('data-housing');
        logData.work_time_format = $(this).attr('data-format');
        logData.log_day_hours = $(this).val().replace(',', '.');

        sendRequest();
    }

    $('body').on('change', '.js-select-client', function () {
        logData = {
            _token: $('input[name=_token]').val(),
        };

        logData.log_id = $(this).attr('data-log-id');
        logData.candidate_id = $(this).attr('data-candidate-id');
        logData.period = $(this).attr('data-date');
        logData.client_id = $(this).val();

        sendRequest();
    });

    $('body').on('change', '.js-worklog-hours-input', function () {
        saveHours.call(this);
    });

    $('body').on('change', '.js-fine-input', function () {
        logData = {
            _token: $('input[name=_token]').val(),
        };

        logData.log_id = $(this).attr('data-log-id');
        logData.candidate_id = $(this).attr('data-candidate-id');
        logData.period = $(this).attr('data-date');
        logData.fine = $(this).val();

        sendRequest();
    });

    $('body').on('change', '.js-premium-input', function () {
        logData = {
            _token: $('input[name=_token]').val(),
        };

        logData.log_id = $(this).attr('data-log-id');
        logData.candidate_id = $(this).attr('data-candidate-id');
        logData.period = $(this).attr('data-date');
        logData.premium = $(this).val();

        sendRequest();
    });

    $('body').on('change', '.js-bhp-form-input', function () {
        logData = {
            _token: $('input[name=_token]').val(),
        };

        logData.log_id = $(this).attr('data-log-id');
        logData.candidate_id = $(this).attr('data-candidate-id');
        logData.period = $(this).attr('data-date');
        logData.bhp_form = $(this).val();

        sendRequest();
    });

    $('body').on('change', '.js-client-hours-input', function () {
        logData = {
            _token: $('input[name=_token]').val(),
        };

        logData.log_id = $(this).attr('data-log-id');
        logData.candidate_id = $(this).attr('data-candidate-id');
        logData.period = $(this).attr('data-date');
        logData[$(this).attr('name')] = $(this).val() ? $(this).val().replace(',','.') : '';

        sendRequest('refreshAllTables');
    });
});

function sendRequest(action) {
    if (sending) {
        return;
    }

    sending = true;

    $.ajax({
        url: '/work-logs/add',
        type: 'POST',
        dataType: 'json',
        data: logData,
        success: function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }

            console.log(res);

            sending = false;

            refreshTable(action);
        }
    });
}

function refreshTable(action) {
    if (window.dTables) {
        dTables.forEach(tbl => {
            if (tbl.context[0].sInstance == 'WorkLogsTable') {
                $('#WorkLogsTable').attr('data-candidate-id', logData.candidate_id);
                tbl.draw();
            } else if (action == 'refreshAllTables') {
                tbl.draw();
            }
        });
    }
}