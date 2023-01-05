'use strict';

let filePath = '';

$(() => {
    $('body').on('click', '#send-excel-file', function () {

        const inpFile = $('#excel-file');

        if (!inpFile[0].files.length) {
            return;
        }

        $(this).prop('disabled', true).attr('data-kt-indicator', 'on');

        const fD = new FormData();

        fD.append('_token', $('input[name=_token]').val());
        fD.append('file', inpFile[0].files[0], inpFile[0].files[0].name);

        $.ajax({
            url: '/leads/import/upload',
            method: 'POST',
            data: fD,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: (response) => {
                $(this).prop('disabled', false).attr('data-kt-indicator', 'off');

                if (response.error) {
                    toastr.error(response.error);
                    return;
                }

                filePath = response.path;

                buildTable(response.data);
            }
        });

    }).on('click', '#import-excel-file', function () {
        $(this).prop('disabled', true).attr('data-kt-indicator', 'on');

        const colSel = $('.js-column-select');
        const fD = new FormData();

        colSel.each(function () {
            const val = $(this).val();

            if (val) {
                fD.append('column_ind[]', $(this).attr('data-index'));
                fD.append('column_name[]', val);
            }
        });

        fD.append('_token', $('input[name=_token]').val());
        fD.append('file_path', filePath);

        if ($('#exclude-first-row').prop('checked')) {
            fD.append('exclude_first_row', 1);
        }

        $.ajax({
            url: '/leads/import/process',
            method: 'POST',
            data: fD,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: (response) => {
                $(this).prop('disabled', false).attr('data-kt-indicator', 'off');

                if (response.error) {
                    toastr.error(response.error);
                    return;
                }

                window.location.replace('/leads');
            }
        });
    });
});

function buildTable(data) {
    const trArr = [];
    let tdLength = 0;

    data.forEach(tr => {
        let tdS = '';

        tdLength = tr.length;

        tr.forEach(td => {
            tdS += '<td>' + td + '</td>';
        });

        trArr.push(tdS);
    });

    let selectors = '';

    for (let i = 0; i < tdLength; i++) {
        selectors += `<th>
                        <select data-index="${i}"
                            class="js-column-select form-select form-select-sm form-select-solid">
                            <option></option>
                            <option value="date">Дата</option>
                            <option value="company">Компания</option>
                            <option value="name">Имя</option>
                            <option value="phone">Телефон</option>
                            <option value="viber">Viber</option>
                        </select>
                    </th>`;
    }

    trArr.unshift(selectors);

    $('#xls-table-wrap').show();
    $('#xls-table').html('<tr>' + trArr.join('</tr><tr>') + '</tr>');
}