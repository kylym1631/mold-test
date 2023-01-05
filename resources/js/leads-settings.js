'use strict';

const reqData = {};

$(function () {
    $('body').on('click', '#add-settings-package', function () {
        delete reqData.id;

        $('#modal-lead-settings-input-name').val('');
        $('#modal-lead-settings-input-lifetime').val(7);
        $('#modal_leads_statuses_select').html('');
        $('#modal_leads_speciality_select').html('');
        $('#modal_leads_failed_call_1_delay_select').val('60');
        $('#modal_leads_failed_call_2_delay_select').val('360');
        $('#modal_leads_failed_call_3_delay_select').val('60');
        $('#modal_leads_not_interested_delay_select').val('1440');
        $('#modal_leads_not_liquidity_delay_select').val('1440');
        $('#modal_leads_liquidity_delay_select').val('1440');

        sourcesFields();

        [
            ['0', 'Новый лид'],
            ['1', 'Горячий'],
            ['2', 'Не оставлял заявку'],
            ['3', 'Не дозвон'],
            ['7', 'Не заинтересован'],
        ].forEach(st => {
            $('#modal_leads_statuses_select')
                .append(new Option(st[1], st[0]))
                .trigger('change');
        });

        $('#modal-lead-settings').modal('show');
    });

    $('body').on('click', '.js-show-lead-settings', function () {
        const id = $(this).attr('data-id');

        reqData.id = id;

        $.ajax({
            url: '/leads/settings/item/json',
            type: 'GET',
            data: 'id=' + id,
            success: function (res) {
                if (res.error) {
                    toastr.error(res.error);
                } else {
                    $('#modal-lead-settings-input-name').val(res.data.name);
                    $('#modal-lead-settings-input-lifetime').val(res.data.lifetime);
                    $('#modal_leads_statuses_select').html('');
                    $('#modal_leads_speciality_select').html('');
                    $('#modal_leads_failed_call_1_delay_select').val(res.data.failed_call_1_delay);
                    $('#modal_leads_failed_call_2_delay_select').val(res.data.failed_call_2_delay);
                    $('#modal_leads_failed_call_3_delay_select').val(res.data.failed_call_3_delay);
                    $('#modal_leads_not_interested_delay_select').val(res.data.not_interested_delay);
                    $('#modal_leads_not_liquidity_delay_select').val(res.data.not_liquidity_delay);
                    $('#modal_leads_liquidity_delay_select').val(res.data.liquidity_delay);

                    sourcesFields(res.data.sources);

                    [
                        ['0', 'Новый лид'],
                        ['1', 'Горячий'],
                        ['2', 'Не оставлял заявку'],
                        ['3', 'Не дозвон'],
                        ['7', 'Не заинтересован'],
                    ].forEach(st => {
                        if (res.data.statuses && res.data.statuses.includes(st[0])) {
                            $('#modal_leads_statuses_select')
                                .append(new Option(st[1], st[0], true, true))
                                .trigger('change');
                        } else {
                            $('#modal_leads_statuses_select')
                                .append(new Option(st[1], st[0]))
                                .trigger('change');
                        }
                    });

                    if (res.data.speciality) {
                        res.data.speciality.forEach(item => {
                            $('#modal_leads_speciality_select').append(new Option(item[1], item[0], true, true)).trigger('change');
                        });
                    }

                    $('#modal-lead-settings').modal('show');
                }
            }
        });

        return false;
    });

    $('body').on('click', '#modal-lead-settings-save-btn', function () {
        reqData._token = $('input[name="_token"]').val();
        reqData.name = $('#modal-lead-settings-input-name').val();
        reqData.statuses = $('#modal_leads_statuses_select').val();
        reqData.speciality = $('#modal_leads_speciality_select').val();
        reqData.lifetime = $('#modal-lead-settings-input-lifetime').val();
        reqData.failed_call_1_delay = $('#modal_leads_failed_call_1_delay_select').val();
        reqData.failed_call_2_delay = $('#modal_leads_failed_call_2_delay_select').val();
        reqData.failed_call_3_delay = $('#modal_leads_failed_call_3_delay_select').val();
        reqData.not_interested_delay = $('#modal_leads_not_interested_delay_select').val();
        reqData.not_liquidity_delay = $('#modal_leads_not_liquidity_delay_select').val();
        reqData.liquidity_delay = $('#modal_leads_liquidity_delay_select').val();

        reqData.sources = [];

        $('input[name="source[]"]:checked').each(function () {
            reqData.sources.push($(this).val());
        });

        $.ajax({
            url: '/leads/settings/update',
            method: 'POST',
            dataType: 'json',
            data: reqData,
            success: (response) => {
                if (response.error) {
                    toastr.error(response.error);
                    return;
                }

                $('#modal-lead-settings').modal('hide');

                toastr.success('Успешно');

                refreshTable();
            }
        });

        return false;
    });

    $('#modal_leads_statuses_select').select2({
        placeholder: 'Статус',
    });

    $('#modal_leads_speciality_select').select2({
        placeholder: 'Специальность',
        ajax: {
            url: "/search/speciality",
            dataType: 'json',
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

    $('body').on('click', '.js-show-lead-status-info', function () {
        const id = $(this).attr('data-id');

        $.ajax({
            url: '/leads/status-inform/' + id,
            type: 'GET',
            success: function (res) {
                if (res.error) {
                    toastr.error(res.error);
                } else {
                    $('#modal-lead-status-info-textarea').val('');

                    if (res.data.info) {
                        $('#modal-lead-status-info-textarea').val(res.data.info);
                    }

                    $('#modal-lead-status-info .modal-title').html(res.data.name).attr('data-id', id);

                    $('#modal-lead-status-info').modal('show');
                }
            }
        });

        return false;
    });

    $('body').on('click', '#modal-lead-status-info-save-btn', function () {
        const data = {
            id: $('#modal-lead-status-info .modal-title').attr('data-id'),
            _token: $('input[name="_token"]').val(),
            info: $('#modal-lead-status-info-textarea').val(),
        };

        $.ajax({
            url: '/leads/status-inform/' + data.id,
            method: 'PUT',
            dataType: 'json',
            data,
            success: (response) => {
                if (response.error) {
                    toastr.error(response.error);
                    return;
                }

                $('#modal-lead-status-info').modal('hide');

                toastr.success('Успешно');

                refreshTable();
            }
        });

        return false;
    });

    $('body').on('change', '.js-activate-lead-setting', function () {
        const data = {
            id: $(this).attr('data-user-id'),
            _token: $('input[name="_token"]').val(),
            leads_settings: [],
        };

        $('.js-activate-lead-setting[data-user-id="' + data.id + '"]:checked').each(function () {
            data.leads_settings.push($(this).val());
        });

        $.ajax({
            url: '/users/activate-lead-settings',
            method: 'POST',
            dataType: 'json',
            data,
            success: (response) => {
                if (response.error) {
                    toastr.error(response.error);
                    return;
                }

                toastr.success('Успешно');

                refreshTable('UsersLeadSettingsTable');
            }
        });

        return false;
    });

});

function sourcesFields(curSources) {
    $('#modal_leads_sources_container').html('');

    $.ajax({
        url: '/search/leads/company',
        type: 'GET',
        success: function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                res.forEach((item, i) => {
                    $('#modal_leads_sources_container').append(`<div class="d-flex form-check form-check-sm form-check-custom form-check-solid mt-5" style="align-items: center; gap: 10px"> 
                        <input id="source-checkbox-${i}" class="form-check-input" type="checkbox" name="source[]" value='${(item.id)}'
                        ${curSources && curSources.includes(item.id) ? ' checked' : ''}>
                        <label for="source-checkbox-${i}">${item.value}</label>
                    </div>`);
                });

            }
        }
    });
}

function refreshTable(tbId) {
    if (window.dTables) {
        dTables.forEach(tbl => {
            if (tbId) {
                if (tbl.context[0].sInstance == tbId) {
                    tbl.draw();
                }
            } else {
                tbl.draw();
            }
        });
    }
}