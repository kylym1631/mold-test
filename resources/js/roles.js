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
                    if (window.location.search && window.location.search.includes('id')) {
                        window.location.replace('/roles/view' + window.location.search);
                    } else {
                        window.location.replace('/roles');
                    }
                }
            }
        });

        return false;
    });

    $('body').on('change', '.js-permission-checkbox', function () {
        if ($(this).prop('checked')) {
            $('#js-' + $(this).val() + '-permission-children').show()
                .find('.js-permissions-select').prop('disabled', false);
        } else {
            $('#js-' + $(this).val() + '-permission-children').hide()
                .find('.js-permissions-select').prop('disabled', true);
        }
    });

    permissionFields(Item && Item.cur_permissions, Item && Item.static_permissions);
});

function permissionFields(curPermissions, staticPermissions) {
    console.log(curPermissions);
    if (!$('#permissions-container').length) {
        return;
    }

    $('#permissions-container').html('');

    $.get('/role-permissions/all/json', function (permissions) {
        for (const key in permissions) {
            if (Object.hasOwnProperty.call(permissions, key)) {
                const name = permissions[key].name;
                let showChildren = false;

                if (curPermissions && curPermissions.includes(key)) {
                    showChildren = true;
                }

                if (view == 'add') {
                    if (!staticPermissions || (staticPermissions && !staticPermissions.includes(key))) {
                        $('#permissions-container').append(`<div class="d-flex form-check form-check-sm form-check-custom form-check-solid mt-5" style="align-items: center; gap: 10px"> 
                            <input id="permission-checkbox-${key}" class="js-permission-checkbox form-check-input" type="checkbox" name="permission[]" value="${key}"
                            ${curPermissions && curPermissions.includes(key) ? ' checked' : ''}>
                            <label for="permission-checkbox-${key}">${name}</label>
                        </div>`);

                        if (permissions[key].children) {
                            const options = Object.values(permissions[key].children).map(opt => {
                                return '<option value="' + opt.key + '" ' + (curPermissions && curPermissions.includes(opt.key) ? ' selected' : '') + '>' + opt.name + '</option>';
                            });

                            $('#permissions-container').append(`<div ${showChildren ? '' : 'style="display: none"'} id="js-${key}-permission-children">
                            <select name="permission[]" class="form-select form-select-sm form-select-solid js-permissions-select mt-3" multiple ${showChildren ? '' : 'disabled'}>${options}</select></div>`);
                        }
                    }

                } else if (view == 'view') {
                    if (curPermissions && curPermissions.includes(key)) {
                        $('#permissions-container').append(`<div class="mt-5">
                                    ${name}
                                </div>`);

                        if (permissions[key].children) {
                            const options = Object.values(permissions[key].children).map(opt => {
                                return curPermissions.includes(opt.key)
                                    ? '<li>' + opt.name + '</li>'
                                    : '';
                            });

                            if (showChildren) {
                                $('#permissions-container').append(`<ul>${options.join('')}</ul>`);
                            }
                        }
                    }
                }
            }
        }

        $('.js-permissions-select').select2({
            placeholder: 'Дополнительные разрешения',
        });
    });
}
