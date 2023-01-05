'use strict';

$(function () {
    $('#users__add_btn').click(function () {

        if (group != 1) {
            $('#modal_users_add__group_id').html('');

            allRoles.forEach(role => {
                if (permissions.includes('user.create.role.' + role.id)) {
                    $('#modal_users_add__group_id').append(new Option(role.name, role.id));
                }
            });
        }

        $('.dz-preview').remove();
        $('.dz-message').show();
        $('#modal_users_add__id').val('');
        $('#modal_users_add__email').val('');
        $('#modal_users_add__password').val('');
        $('#modal_users_add__firstName').val('');
        $('#modal_users_add__lastName').val('');
        $('#modal_users_add__phone').val('');
        $('#modal_users_add__account').val('');
        $('#modal_users_leads_companies_select').html('');
        $('#modal_users_add').modal('show');

        if ($('#modal_users_add__group_id').val() == 2) {
            toggleLeadsCompaniesField(2);
        }

        permissionFields(null, $('#modal_users_add__group_id').val());
    });

    $('#modal_users_add__save').click(function (e) {
        e.preventDefault();
        let self = $(this);

        self.prop('disabled', true);

        var data = {
            user_id: userId,
            email: $('#modal_users_add__email').val(),
            password: $('#modal_users_add__password').val(),
            group_id: $('#modal_users_add__group_id').val(),
            activation: $('#modal_users_add__activation').val(),
            lang: $('#modal_users_add__lang').val(),
            firstName: $('#modal_users_add__firstName').val(),
            lastName: $('#modal_users_add__lastName').val(),
            phone: $('#modal_users_add__phone').val(),
            account: $('#modal_users_add__account').val(),
            _token: $('input[name=_token]').val(),
        };

        if (
            $('#modal_users_RecruitmentDirector_id').length
            && $('#modal_users_RecruitmentDirector_field').hasClass('active')
        ) {
            data.user_id = $('#modal_users_RecruitmentDirector_id').val();
        }

        if ($('#modal_users_leads_companies_field').length) {
            data.leads_settings = $('#modal_users_leads_companies_select').val();
        }

        if ($('#modal_users_add__permission').length) {
            data.permissions = [];

            $('input[name="permission[]"]:checked').each(function () {
                data.permissions.push($(this).val());
            });

            $('select[name="permission[]"]:not(:disabled)').each(function () {
                if ($(this).val()) {
                    $(this).val().forEach(arg => {
                        data.permissions.push(arg);
                    });
                }
            });
        }

        let id = $('#modal_users_add__id').val();
        if (id !== '') {
            data.id = id;
        }

        $.ajax({
            url: '/users/add',
            method: 'post',
            data: data,
            success: function (response, status, xhr, $form) {
                if (response.error) {
                    toastr.error(response.error);
                } else {
                    $('#modal_users_add').modal('hide');

                    if (window.dTables) {
                        dTables.forEach(function (tbl) {
                            tbl.draw();
                        });
                    }
                }
                self.prop('disabled', false);
            }
        });
    });

    $('#modal_users_add__group_id').on('change', function () {
        toggleRecruitmentDirectorField($(this).val());
        toggleLeadsCompaniesField($(this).val());
        permissionFields(null, $(this).val());
    });

    new Dropzone("#kt_ecommerce_add_product_media", {
        url: "/files/user/add", // Set the url for your upload script location
        paramName: "file",
        maxFiles: 1,
        maxFilesize: 5,
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
        addRemoveLinks: true,
        sending: function (file, xhr, formData) {
            formData.append('_token', $('input[name=_token]').val());
            formData.append('user_id', $('#modal_users_add__id').val());
        },
        success: function (file, done) {
            $('#modal_users_add__id').val(done.user_id);
        },
        accept: function (file, done) {
            done();
        }
    });

    if ($('#modal_users_leads_companies_select').length) {
        $('#modal_users_leads_companies_select').select2({
            placeholder: 'Поиск пакета',
            ajax: {
                url: "/search/leads/settings",
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
    }

    if ($('#modal_users_RecruitmentDirector_id').length) {
        $('#modal_users_RecruitmentDirector_id').select2({
            placeholder: 'Поиск руководителя',
            minimumResultsForSearch: 5,
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
        }).on('select2:open', function () {
            $('input.select2-search__field').focus();
        });
    }

    $('body').on('change', '.js-permission-checkbox', function () {
        if ($(this).prop('checked')) {
            $('#js-' + $(this).val() + '-permission-children').show()
                .find('.js-permissions-select').prop('disabled', false);
        } else {
            $('#js-' + $(this).val() + '-permission-children').hide()
                .find('.js-permissions-select').prop('disabled', true);
        }
    });
});

window.generatePassword = function generatePassword() {
    var length = 11,
        charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    $('#modal_users_add__password').val(retVal);
}

window.editUser = function editUser(id) {
    $('.dz-preview').remove();
    $('.dz-message').show();

    if (group != 1) {
        $('#modal_users_add__group_id').html('');

        allRoles.forEach(role => {
            if (permissions.includes('user.edit.role.' + role.id)) {
                $('#modal_users_add__group_id').append(new Option(role.name, role.id));
            }
        });
    }


    $.get('/users/ajax/id/' + id, function (res) {
        $('#modal_users_add__id').val(id);
        $('#modal_users_add__email').val(res.user.email);
        $('#modal_users_add__firstName').val(res.user.firstName);
        $('#modal_users_add__lastName').val(res.user.lastName);
        $('#modal_users_add__phone').val(res.user.phone);
        $('#modal_users_add__account').val(res.user.account);
        $('#modal_users_add__group_id').val(res.user.group_id);
        $('#modal_users_add__activation').val(res.user.activation);
        $('#modal_users_add__lang').val(res.user.lang);

        if ($('#modal_users_RecruitmentDirector_id').length) {
            $('#modal_users_RecruitmentDirector_id').html('');
            if (res.user.supervisor) {
                $('#modal_users_RecruitmentDirector_id')
                    .append(new Option(res.user.supervisor, res.user.user_id, true, true));
            }
            // toggleRecruitmentDirectorField(res.user.group_id);
        }

        if ($('#modal_users_leads_companies_field').length) {
            $('#modal_users_leads_companies_select').html('');

            toggleLeadsCompaniesField(res.user.group_id);

            if (res.user.leads_settings) {
                res.user.leads_settings.forEach(function (lc) {
                    $('#modal_users_leads_companies_select')
                        .append(new Option(lc.name, lc.id, true, true))
                        .trigger('change');
                });
            }
        }

        permissionFields(res.user.permissions, res.user.group_id);

        $('#modal_users_add').modal('show');
    });
}

window.changeActivation = function changeActivation(id) {
    var changeActivation = $('.changeActivation' + id).val();
    $.get('/users/activation?s=' + changeActivation + '&id=' + id, function (res) {
        if (res.error) {
            toastr.error(res.error);
        } else {
            toastr.success('Успешно');
        }
    });
}

function toggleRecruitmentDirectorField(group_id) {
    // if ($('#modal_users_RecruitmentDirector_field').length) {
    //     if (group_id == '2' || group_id == '4' || group_id == '8' || group_id == '10') {
    //         $('#modal_users_RecruitmentDirector_field').show().addClass('active');
    //     } else {
    //         $('#modal_users_RecruitmentDirector_field').hide().removeClass('active');
    //         $('#modal_users_RecruitmentDirector_id').val('');
    //     }
    // }
}

function toggleLeadsCompaniesField(group_id) {
    if ($('#modal_users_leads_companies_field').length) {
        if (group_id == '2') {
            $('#modal_users_leads_companies_field').show();
        } else {
            $('#modal_users_leads_companies_field').hide();
            $('#modal_users_leads_companies_select').html('');
        }
    }
}

function permissionFields(curPermissions, group_id) {
    if ($('#modal_users_add__permission').length) {
        $('#modal_users_add__permission').html('');

        $.get('/permissions/get/' + group_id, function (permissions) {
            for (const key in permissions) {
                if (Object.hasOwnProperty.call(permissions, key)) {
                    const name = permissions[key].name;
                    let showChildren = false;

                    if (curPermissions && curPermissions.includes(key)) {
                        showChildren = true;
                    }

                    $('#modal_users_add__permission').append(`<div class="d-flex form-check form-check-sm form-check-custom form-check-solid mt-5" style="align-items: center; gap: 10px"> 
                        <input id="permission-checkbox-${key}" class="js-permission-checkbox form-check-input" type="checkbox" name="permission[]" value="${key}"
                        ${curPermissions && curPermissions.includes(key) ? ' checked' : ''}>
                        <label for="permission-checkbox-${key}">${name}</label>
                    </div>`);

                    if (permissions[key].children) {
                        const options = Object.values(permissions[key].children).map(opt => {
                            return '<option value="' + opt.key + '" ' + (curPermissions && curPermissions.includes(opt.key) ? ' selected' : '') + '>' + opt.name + '</option>';
                        });

                        $('#modal_users_add__permission').append(`<div ${showChildren ? '' : 'style="display: none"'} id="js-${key}-permission-children">
                        <select name="permission[]" class="form-select form-select-sm form-select-solid js-permissions-select mt-3" multiple ${showChildren ? '' : 'disabled'}>${options}</select></div>`);
                    }
                }
            }

            $('.js-permissions-select').select2({
                placeholder: 'Разрешения',
            });
        });
    }
}